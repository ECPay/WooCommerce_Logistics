<?php
/**
 * @copyright Copyright (c) 2018 Green World FinTech Service Co., Ltd. (https://www.ecpay.com.tw)
 * @version 1.3.2003020
 *
 * Plugin Name: ECPay Logistics for WooCommerce
 * Plugin URI: https://www.ecpay.com.tw
 * Description: ECPay Integration Logistics Gateway for WooCommerce
 * Version: 1.3.2003020
 * Author: ECPay Green World FinTech Service Co., Ltd.
 * Author URI: https://www.ecpay.com.tw
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * WC requires at least: 3
 * WC tested up to: 3.9.2
 */

defined( 'ABSPATH' ) or exit;
define('ECPAY_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('ECPAY_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('ECPAY_SHIPPING_ID', 'ecpay_shipping');
define('ECPAY_SHIPPING_PAY_ID', 'ecpay_shipping_pay');
define('ECPAY_SHIPPING_PLUGIN_VERSION', '1.3.2003020');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ECPAY_PLUGIN_PATH . 'ECPayLogisticsHelper.php');

// 新增訂單狀態
if (!class_exists('ECPayShippingStatus')) {
    class ECPayShippingStatus
    {
        public function __construct()
        {
            add_filter('wc_order_statuses', array($this, 'add_statuses'));
            add_action('init', array($this, 'register_status'));
        }

        // 註冊新訂單狀態
        public function register_status()
        {
            register_post_status(
                'wc-ecpay',
                array(
                    'label'                     => _x( 'ECPay Shipping', 'Order status', 'woocommerce' ),
                    'public'                    => true,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( _x( 'ECPay Shipping', 'Order status', 'woocommerce' ) . ' <span class="count">(%s)</span>', _x( 'ECPay Shipping', 'Order status', 'woocommerce' ) . ' <span class="count">(%s)</span>' )
                )
            );
        }

        // 加入新訂單狀態
        public function add_statuses($order_statuses)
        {
            $order_statuses['wc-ecpay'] = _x( 'ECPay Shipping', 'Order status', 'woocommerce' );

            return $order_statuses;
        }
    }
    new ECPayShippingStatus();
}

// 物流主架構: 綠界科技超商取貨
function ECPayShippingMethodsInit()
{
    # Make sure WooCommerce is setted.
    if (!class_exists('WC_Shipping_Method')) {
        add_action( 'admin_notices', 'ecpay_wc_shipping_render_wc_inactive_notice' );
        return;
    }

    class ECPayShippingMethods extends WC_Shipping_Method
    {
        private $helper = null;
        public $MerchantID;
        public $HashKey;
        public $HashIV;

        // 綠界結帳記錄 session 欄位
        private $checkoutData = array(
            'billing_first_name',
            'billing_last_name',
            'billing_company',
            'billing_phone',
            'billing_email',
            'shipping_first_name',
            'shipping_last_name',
            'shipping_company',
            'shipping_to_different_address',
            'order_comments'
        );
        public $SenderName;
        public $SenderPhone;
        public $ecpaylogistic_min_amount;
        public $ecpaylogistic_max_amount;
        public $cartAmount;

        public function __construct()
        {
            global $woocommerce;

            $chosen_methods = array();

            # Helper
            $this->helper = ECPayCustomFeatures::getHelper();

            if (method_exists($woocommerce->session, 'get') && ($woocommerce->session->get( 'chosen_shipping_methods' ) != null)) {
                $chosen_methods = $woocommerce->session->get( 'chosen_shipping_methods' );
            }

            if (in_array(ECPAY_SHIPPING_ID, $chosen_methods)) {
                add_filter( 'woocommerce_checkout_fields' , array(&$this, 'custom_override_checkout_fields'));
            }

            $this->id = ECPAY_SHIPPING_ID;
            $this->method_title = "綠界科技超商取貨";
            $this->title = "綠界科技超商取貨";
            $this->options_array_label = '綠界科技超商取貨';
            $this->method_description = '';

            add_action('woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
            add_action('woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_admin_options'));
            add_action('woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_shipping_options'));

            $this->init();

            // add the action
            add_action( 'woocommerce_admin_order_data_after_order_details', array(&$this,'action_woocommerce_admin_order_data_after_shipping_address' ));

            // 後台變更門市 Response
            add_action( 'woocommerce_api_get_change_response', array($this, 'get_change_response'));
        }

        /**
         * Init settings
         *
         * @access public
         * @return void
         */
        public function init()
        {
            // Load the settings API
            global $woocommerce;
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title        = $this->get_option('title');
            $this->type         = $this->get_option('type');
            $this->fee          = $this->get_option('fee');
            $this->type         = $this->get_option('type');
            $this->codes        = $this->get_option('codes');
            $this->availability = $this->get_option('availability');
            $this->testMode     = $this->get_option('testMode');
            $this->countries    = $this->get_option('countries');
            $this->category     = $this->get_option('category');
            $this->MerchantID   = $this->get_option('ecpay_merchant_id');
            $this->HashKey      = $this->get_option('ecpay_hash_key');
            $this->HashIV       = $this->get_option('ecpay_hash_iv');
            $this->SenderName   = $this->get_option('sender_name');
            // $this->SenderPhone  = $this->get_option('sender_phone');
            $this->SenderPhone  = '';
            $this->SenderCellPhone = $this->get_option('sender_cell_phone');
            $this->ecpaylogistic_min_amount = $this->get_option('ecpaylogistic_min_amount');
            $this->ecpaylogistic_max_amount = $this->get_option('ecpaylogistic_max_amount');
            $this->ecpaylogistic_free_shipping_amount = $this->get_option('ecpaylogistic_free_shipping_amount');

            $this->get_shipping_options();

            // 設定Helper MerchantID
            $this->helper->setMerchantId($this->MerchantID);

            // 結帳頁 Filter
            add_filter('woocommerce_shipping_methods', array(&$this, 'add_wcso_shipping_methods'), 10, 1);

            // 隱藏與顯示貨到付款金流
            add_filter('woocommerce_available_payment_gateways', array(&$this, 'wcso_filter_available_payment_gateways'), 10, 1);

            // 結帳頁 Hook

            // ecpay_save_data response
            add_action('woocommerce_api_ecpay_get_checkout_session', array($this, 'ecpay_get_checkout_session'));

            // 清除門市資訊 session
            add_action('woocommerce_api_ecpay_cvs_info_session_clear', array($this, 'ecpay_cvs_info_session_clear'));

            // 接收電子地圖回傳資訊
            add_action('woocommerce_api_ecpay_receive_cvs_info', array($this, 'ecpay_receive_cvs_info'));

            // 加入 ECPay-shipping-checkout.js
            add_action('wp_enqueue_scripts', array( $this, 'ecpay_shipping_checkout'));

            // 顯示電子地圖
            add_action('woocommerce_review_order_after_shipping', array(&$this, 'wcso_review_order_shipping_options'));

            add_action('woocommerce_checkout_update_order_meta', array(&$this, 'wcso_field_update_shipping_order_meta'), 10, 2);

            // 後台 Hook
            if (is_admin()) {
                // 加入 ECPay-shipping-change-response.js
                add_action('admin_enqueue_scripts' , array( $this, 'ecpay_shipping_helper' ));
                add_action('woocommerce_admin_order_data_after_shipping_address', array(&$this, 'wcso_display_shipping_admin_order_meta'), 10, 2 );
            }
        }

        /**
         * 後台 - 訂單詳細頁面的產生物流單按鈕
         * @return void
         */
        public function action_woocommerce_admin_order_data_after_shipping_address()
        {
            try {
                global $woocommerce, $post;

                // 載入 JS
                wp_enqueue_script('ecpay_shipping_helper');

                // 取得訂單資訊
                $orderInfo = get_post_meta($post->ID);
                if ( ! is_array($orderInfo) ) {
                    return false;
                }
                // 判斷綠界物流欄位是否存在
                if ( ! array_key_exists('ecPay_shipping', $orderInfo) ) {
                    return false;
                }
                // 判斷綠界物流欄位是否有值
                if ( ! isset($orderInfo['ecPay_shipping'][0]) ) {
                    return false;
                }

                // 物流子類型
                $subType = "";
                $shippingMethod = $this->helper->getPaymentCategory($this->category);
                if (array_key_exists($orderInfo['ecPay_shipping'][0], $shippingMethod)) {
                    $subType = $shippingMethod[$orderInfo['ecPay_shipping'][0]];
                    // 判斷是否為C2C
                    if (isset($this->helper->paymentFormMethods[$subType])) {
                        $paymentFormMethod = $this->helper->paymentFormMethods[$subType];
                    }
                }

                $orderObj = new WC_Order($post->ID);
                $itemsInfo = $orderObj->get_items();

                // 訂單的商品
                $items = array();

                foreach ($itemsInfo as $key => $value) {
                    $items[] = $value['name'];
                }

                // 訂單金額
                $temp = explode('.', $orderInfo['_order_total'][0]);
                $totalPrice = esc_html($temp[0]);

                // 建立物流訂單所需資料
                $data = array(
                    'HashKey'              => $this->HashKey,
                    'HashIV'               => $this->HashIV,
                    'MerchantTradeNo'      => $post->ID,
                    'LogisticsSubType'     => $subType,
                    'GoodsAmount'          => (int)$totalPrice,
                    'CollectionAmount'     => (int)$totalPrice,
                    'IsCollection'         => $orderInfo['ecPay_shipping'][0],
                    'SenderName'           => $this->SenderName,
                    'SenderPhone'          => $this->SenderPhone,
                    'SenderCellPhone'      => $this->SenderCellPhone,
                    'ReceiverName'         => esc_html($this->get_receiver_name($orderInfo)),
                    'ReceiverPhone'        => esc_html($orderInfo['_billing_phone'][0]),
                    'ReceiverCellPhone'    => esc_html($orderInfo['_billing_phone'][0]),
                    'ReceiverEmail'        => esc_html($orderInfo['_billing_email'][0]),
                    'ServerReplyURL'       => str_replace( 'http:', $this->isHttps(), add_query_arg('wc-api', 'WC_Gateway_Ecpay_Logis', home_url('/')) ),
                    'LogisticsC2CReplyURL' => str_replace( 'http:', $this->isHttps(), add_query_arg('wc-api', 'WC_Gateway_Ecpay_Logis', home_url('/')) ),
                    'Remark'               => esc_html($orderObj->get_customer_note()),
                    'ReceiverStoreID'      => (array_key_exists('_shipping_CVSStoreID', $orderInfo)) ? $orderInfo['_shipping_CVSStoreID'][0] : ((isset($orderInfo['_CVSStoreID'][0])) ? $orderInfo['_CVSStoreID'][0] : ''),
                    'ReturnStoreID'        => (array_key_exists('_shipping_CVSStoreID', $orderInfo)) ? $orderInfo['_shipping_CVSStoreID'][0] : ((isset($orderInfo['_CVSStoreID'][0])) ? $orderInfo['_CVSStoreID'][0] : ''),
                );

                // 顯示建立物流單按鈕的條件 :
                // 1. 金流:貨到付款、訂單狀態:保留
                // 2. 金流:貨到付款以外的付款方式、訂單狀態:處理中
                $postStatus = (null !== get_post_status( $post->ID )) ? get_post_status( $post->ID ) : '';
                $statusData = array(
                    'category'     => $this->category,
                    'orderStatus'  => $postStatus,
                    'isCollection' => $orderInfo['ecPay_shipping'][0]
                );
                $status = $this->helper->getStatusTable($statusData);

                switch ($status) {
                    case 0:
                        // 顯示建立物流訂單按鈕
                        echo '</form>';
                        echo $this->helper->createShippingOrder($data);
                        echo "<input class='button' type='button' value='建立物流訂單' onclick='ecpayCreateLogisticsOrder();'>";

                        if ($this->testMode == 'yes') {
                            $serviceUrl = 'https://logistics-stage.ecpay.com.tw/Express/map';
                        } else {
                            $serviceUrl = 'https://logistics.ecpay.com.tw/Express/map';
                        }

                        $formData = array(
                            'formId'     => 'ecpayChangeStoreForm',
                            'serviceURL' => $serviceUrl,
                            'postParams' => [
                                'MerchantID'       => $this->MerchantID,
                                'MerchantTradeNo'  => $post->ID,
                                'LogisticsSubType' => $subType,
                                'IsCollection'     => EcpayIsCollection::NO,
                                'ServerReplyURL'   => str_replace( 'http:', $this->isHttps(), add_query_arg('wc-api', 'Get_Change_Response', home_url('/')) ),
                                'ExtraData'        => "",
                                'Device'           => wp_is_mobile(),
                                'LogisticsType'    => "CVS",
                            ],
                        );

                        // 顯示變更門市按鈕
                        echo $this->helper->changeStore($formData);
                        break;
                    case 1:
                        // 後台建立物流訂單之後，產生列印繳款單
                        echo '</form>';

                        $paymentFormFileds = array(
                            'AllPayLogisticsID' => $orderInfo['_AllPayLogisticsID'][0],
                            'CVSPaymentNo'      => $orderInfo['_CVSPaymentNo'][0],
                            'CVSValidationNo'   => $orderInfo['_CVSValidationNo'][0],
                        );
                        echo $this->helper->paymentForm($data, $paymentFormMethod, $paymentFormFileds);
                        break;
                    default:
                        break;
                }
            }catch(Exception $e) {
                echo esc_html($e->getMessage());
            }
        }

        /**
         * 後台 - 變更門市 Response
         * @return void
         */
        public function get_change_response()
        {
            // 接收資料
            $CVSStoreName = sanitize_text_field($_REQUEST['CVSStoreName']);
            $CVSAddress   = sanitize_text_field($_REQUEST['CVSAddress']);
            $CVSTelephone = sanitize_text_field($_REQUEST['CVSTelephone']);
            $CVSStoreID   = sanitize_text_field($_REQUEST['CVSStoreID']);

            // 驗證
            if (mb_strlen( $CVSStoreName, "utf-8") > 10) {
                $CVSStoreName = mb_substr($CVSStoreName, 0, 10, "utf-8");
            }
            if (mb_strlen( $CVSAddress, "utf-8") > 60) {
                $CVSAddress = mb_substr($CVSAddress , 0, 60, "utf-8");
            }
            if (strlen($CVSTelephone) > 20) {
                $CVSTelephone = substr($CVSTelephone  , 0, 20);
            }
            if (strlen($CVSStoreID) > 10) {
                $CVSStoreID = substr($CVSTelephone , 0, 10);
            }

            // 自動儲存
            $MerchantTradeNo = sanitize_text_field($_REQUEST['MerchantTradeNo']);
            update_post_meta($MerchantTradeNo, '_shipping_purchaserStore', $CVSStoreName);
            update_post_meta($MerchantTradeNo, '_shipping_purchaserAddress', $CVSAddress);
            update_post_meta($MerchantTradeNo, '_shipping_purchaserPhone', $CVSTelephone);
            update_post_meta($MerchantTradeNo, '_shipping_CVSStoreID', $CVSStoreID);

            // template
            wc_get_template('admin/ECPay-admin-change-response.php', array(), '', ECPAY_PLUGIN_PATH . 'templates/');

            exit;
        }

        /**
         * 取得收件者姓名
         * @param  array    $orderInfo    訂單資訊
         * @return string                 收件者姓名
         */
        private function get_receiver_name($orderInfo)
        {
            $orderInfo = array(
                'shippingFirstName' => $orderInfo['_shipping_first_name'][0],
                'shippingLastName'  => $orderInfo['_shipping_last_name'][0],
                'billingFirstName'  => $orderInfo['_billing_first_name'][0],
                'billingLastName'   => $orderInfo['_billing_last_name'][0],
            );
            return $this->helper->getReceiverName($orderInfo);
        }

        /**
         * 管理結帳欄位
         *
         * @param  array $fields
         * @return array $fields
         */
        public function custom_override_checkout_fields($fields)
        {
            if ( ECPayShippingOptions::hasVirtualProducts() !== true ) {
                $this->fill_checkout_info();
                $fields = $this->custom_checkout_fields($fields);
            }
            return $fields;
        }

        /**
         * 填入結帳資料
         * @return void
         */
        private function fill_checkout_info()
        {
            if (!isset($_SESSION)) {
                session_start();
            }
            foreach ($this->checkoutData as $name) {
                if (isset($_SESSION[$name]) === true) {
                    if ($name === 'shipping_to_different_address') {
                        $temp_callback = '';
                        if ($_SESSION[$name] === '1') {
                            $temp_callback = '__return_true';
                        } else {
                            $temp_callback = '__return_false';
                        }
                        add_filter('woocommerce_ship_to_different_address_checked', $temp_callback);
                    } else {
                        if (isset($_POST[$name]) === false) {
                            $_POST[$name] = wc_clean($_SESSION[$name]);
                        }
                    }
                }
            }
        }

        /**
         * 前台 - 結帳頁超商門市欄位
         * @param  array $fields
         * @return array $fields
         */
        private function custom_checkout_fields($fields)
        {
            $cvsInfo = WC()->session->get('cvsInfo');

            $fields['billing']['purchaserStore'] = array(
                'label'         => __( '超商取貨門市名稱', 'purchaserStore' ),
                'default'       => $cvsInfo['CVSStoreName'],
                'required'      => true,
                'priority'      => 300,
                'class'         => array('hidden')
            );
            $fields['billing']['purchaserAddress'] = array(
                'label'         => __( '超商取貨門市地址', 'purchaserAddress' ),
                'default'       => $cvsInfo['CVSAddress'],
                'required'      => true,
                'priority'      => 310,
                'class'         => array('hidden')
            );
            $fields['billing']['purchaserPhone'] = array(
                'label'         => __( '超商取貨門市電話', 'purchaserPhone' ),
                'default'       => $cvsInfo['CVSTelephone'],
                'priority'      => 320,
                'class'         => array('hidden'),
            );
            $fields['billing']['CVSStoreID'] = array(
                'label'         => __( '超商取貨門市代號', 'CVSStoreID' ),
                'default'       => $cvsInfo['CVSStoreID'],
                'required'      => true,
                'priority'      => 330,
                'class'         => array('hidden')
            );
            return $fields;
        }

        // 儲存門市資訊 session
        public function set_cvs_info_session()
        {
            $cvsInfo = [
                'CVSStoreName' => isset($_REQUEST['CVSStoreName']) ? sanitize_text_field($_REQUEST['CVSStoreName']) : '',
                'CVSAddress' => isset($_REQUEST['CVSAddress']) ? sanitize_text_field($_REQUEST['CVSAddress']) : '',
                'CVSTelephone' => isset($_REQUEST['CVSTelephone']) ? sanitize_text_field($_REQUEST['CVSTelephone']) : '',
                'CVSStoreID' => isset($_REQUEST['CVSStoreID']) ? sanitize_text_field($_REQUEST['CVSStoreID']) : ''
            ];

            WC()->session->set('cvsInfo', $cvsInfo);
        }

        /**
         * calculate_shipping function.
         * 運費計算
         *
         * @access public
         * @param array $package (default: array())
         * @return void
         */
        public function calculate_shipping($package = array())
        {
            $shipping_total = 0;
            $fee = ( trim($this->fee) == '' ) ? 0 : $this->fee; // 運費
            $contents_cost = $package['contents_cost']; // 總計金額
            $freeShippingAmount = $this->ecpaylogistic_free_shipping_amount; // 超過多少金額免運費

            if ($freeShippingAmount > 0) {
                $shipping_total = ($contents_cost > $freeShippingAmount) ? 0 : $fee;
            } else {
                $shipping_total = $fee ;
            }

            $rate = array(
                'id' => $this->id,
                'label' => $this->title,
                'cost' => $shipping_total
            );

            $this->add_rate($rate);
        }

        /**
         * init_form_fields function.
         *
         * @access public
         * @return void
         */
        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => '是否啟用',
                    'type' => 'checkbox',
                    'label' => '啟用綠界科技超商取貨',
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => '名稱',
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                    'default' => '綠界科技超商取貨',
                    'desc_tip' => true,
                ),
                'testMode' => array(
                    'title' => "測試模式",
                    'type' => 'checkbox',
                    'label' => '啟用測試模式',
                    'default' => 'no'
                ),
                'category' => array(
                    'title' => "物流類型",
                    'type' => 'select',
                    'options' => array('B2C'=>'B2C','C2C'=>'C2C')
                ),
                'ecpay_merchant_id' => array(
                    'title' => "特店編號",
                    'type' => 'text',
                    'default' => '2000132'
                ),
                'ecpay_hash_key' => array(
                    'title' => "物流介接Hash_Key",
                    'type' => 'text',
                    'default' => '5294y06JbISpM5x9'
                ),
                'ecpay_hash_iv' => array(
                    'title' => "物流介接Hash_IV",
                    'type' => 'text',
                    'default' => 'v77hoKGq4kWxNNIS'
                ),
                'sender_name' => array(
                    'title' => "寄件人名稱",
                    'type' => 'text',
                    'default' => 'ECPAY',
                    'placeholder' => '請輸入中文2~5個字或英文4~10個字',
                    'custom_attributes' => array(
                        'pattern' => ECPayShippingOptions::getRegex('SenderName'),
                    ),
                ),
                'sender_cell_phone' => array(
                    'title' => "寄件人手機",
                    'type' => 'text',
                    'default' => '',
                    'placeholder' => "請輸入09開頭的手機號碼共10碼",
                    'custom_attributes' => array(
                        'pattern' => ECPayShippingOptions::getRegex('SenderCellPhone'),
                    ),
                ),
                /*'sender_phone' => array(
                    'title' => "寄件人電話",
                    'type' => 'text',
                    'default' => ''
                ),*/
                'ecpaylogistic_min_amount' => array(
                    'title' => "超商取貨最低金額",
                    'type' => 'number',
                    'default' => '10',
                    'placeholder' => "超商取貨金額限制範圍1~19999",
                    'custom_attributes' => array(
						'min'  => 1,
						'max' => 19999,
					),
                ),
                'ecpaylogistic_max_amount' => array(
                    'title' => "超商取貨最高金額",
                    'type' => 'number',
                    'default' => '19999',
                    'placeholder' => "超商取貨金額限制範圍1~19999",
                    'custom_attributes' => array(
						'min'  => 1,
						'max' => 19999,
					),
                ),
                'fee' => array(
                    'title' => '運費',
                    'type' => 'price',
                    'description' => __('What fee do you want to charge for local delivery, disregarded if you choose free. Leave blank to disable.', 'woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                    'placeholder' => wc_format_localized_price(0)
                ),
                'ecpaylogistic_free_shipping_amount' => array(
                    'title' => "超過多少金額免運費",
                    'type' => 'price',
                    'default' => '0'
                ),
                'shipping_options_table' => array(
                    'type' => 'shipping_options_table'
                )
            );
        }

        /**
         * is_available function.
         * 前台購物車頁-判斷物流是否顯示
         *
         * @access public
         * @param array $package
         * @return bool
         */
        public function is_available($package)
        {
            global $woocommerce;

            // 檢查後台設定的[超商取貨金額]
            if (( $woocommerce->cart->cart_contents_total < $this->ecpaylogistic_min_amount) || ( $woocommerce->cart->cart_contents_total > $this->ecpaylogistic_max_amount)) {
                return false;
            }

            // 取得綠界物流的設定參數
            $gateway_settings = get_option( 'woocommerce_ecpay_shipping_settings', '' );
            if (empty( $gateway_settings['enabled'] ) || $gateway_settings['enabled'] === 'no' || $this->enabled == 'no') {
                return false;
            }

            // If post codes are listed, let's use them.
            $codes = array();
            if ($this->codes != '') {
                foreach (explode(',', $this->codes) as $code) {
                    $codes[] = $this->clean($code);
                }
            }

            if (!empty($codes)) {
                $found_match = false;

                if (in_array($this->clean($package['destination']['postcode']), $codes)) {
                    $found_match = true;
                }

                // Pattern match
                if (!$found_match) {
                    $customer_postcode = $this->clean($package['destination']['postcode']);
                    foreach ($codes as $c) {
                        $pattern = '/^' . str_replace('_', '[0-9a-zA-Z]', $c) . '$/i';
                        if (preg_match($pattern, $customer_postcode)) {
                            $found_match = true;
                            break;
                        }
                    }
                }

                // Wildcard search
                if (!$found_match) {
                    $customer_postcode = $this->clean($package['destination']['postcode']);
                    $customer_postcode_length = strlen($customer_postcode);

                    for ($i = 0; $i <= $customer_postcode_length; $i++) {
                        if (in_array($customer_postcode, $codes)) {
                            $found_match = true;
                        }
                        $customer_postcode = substr($customer_postcode, 0, -2) . '*';
                    }
                }

                if (!$found_match) {
                    return false;
                }
            }

            // 取得可運送地區清單
            if ($this->availability == 'specific') {
                $ship_to_countries = $this->countries;
            } else {
                $ship_to_countries = array_keys(WC()->countries->get_shipping_countries());
            }

            // 檢查是否為運送地區
            if (is_array($ship_to_countries)) {
                if (!in_array($package['destination']['country'], $ship_to_countries)) {
                    return false;
                }
            }

            return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', true, $package);
        }

        /**
         * clean function.
         *
         * @access public
         * @param mixed $code
         * @return string
         */
        public function clean($code)
        {
            return str_replace('-', '', sanitize_title($code)) . ( strstr($code, '*') ? '*' : '' );
        }

        /**
         * validate_shipping_options_table_field function.
         *
         * @access public
         * @param  mixed $key
         * @return bool
         */
        public function validate_shipping_options_table_field( $key )
        {
            return false;
        }

        /**
         * generate_options_table_html function.
         * 後台 - 產生運送項目區塊
         *
         * @access public
         * @return string
         */
        public function generate_shipping_options_table_html()
        {
            ob_start();

            $args = array(
                'id' => $this->id,
                'ecpayLogisticsB2C' => $this->helper->ecpayLogistics['B2C'],
                'shipping_options' => $this->shipping_options
            );
            wc_get_template('admin/ECPay-admin-settings-options-table.php', $args, '', ECPAY_PLUGIN_PATH . 'templates/');

            return ob_get_clean();
        }

        /**
         * process_shipping_options function.
         * 後台 - 更新運送項目
         *
         * @access public
         * @return void
         */
        public function process_shipping_options()
        {
            // 取得物流類型。避免第一次設定無法取得物流類型問題
            $ecpay_category = $this->category;
            if (empty($ecpay_category) === true) {
                if (isset($_POST['woocommerce_ecpay_shipping_category']) === true) {
                    $ecpay_category = sanitize_text_field($_POST['woocommerce_ecpay_shipping_category']);
                }
            }

            $options = array();
            if (isset($this->helper->ecpayLogistics[$ecpay_category]) === true) {
                foreach ($this->helper->ecpayLogistics[$ecpay_category] as $key => $value) {
                    if (array_key_exists($key, $_POST)) {
                        $options[] = $key ;
                    }
                }
            }

            update_option($this->id, $options);
            $this->get_shipping_options();
        }

        /**
         * get_shipping_options function.
         * 取得運送項目
         *
         * @access public
         * @return void
         */
        public function get_shipping_options()
        {
            $this->shipping_options = array_filter( (array) get_option( $this->id ) );
        }

        /**
         * 判斷是否為啟用的運送項目
         *
         * @access public
         * @return void
         */
        public function is_enable_shipping_options($value)
        {
            if (in_array($value, $this->shipping_options)) {
                return true;
            }
            return false;
        }

        /**
         * 前台購物車顯示 option
         *
         * @access public
         * @return void
         */
        public function wcso_review_order_shipping_options()
        {
            global $woocommerce;
            try {
                if ($this->is_avalible_shipping_facade() === true) {
                    // 取得物流子類別
                    $shipping_type = $this->get_session_shipping_type();
                    $sub_type = $this->get_sub_type_facade();

                    // 判斷是否為啟用的物流方法
                    if ($this->is_enable_shipping_options($shipping_type) === false) {
                        $_SESSION['ecpayShippingType'] = '';
                        $this->ecpay_cvs_info_session_clear();
                    }

                    // 按鈕文字
                    $cvsInfo = WC()->session->get('cvsInfo');
                    $buttonText  = (empty($cvsInfo['CVSStoreID'])) ? '電子地圖' : '重選電子地圖';

                    // 建立電子地圖
                    $shipping_name = $this->helper->ecpayLogistics[$this->category];
                    $replyUrl = str_replace( 'http:', $this->isHttps(), add_query_arg('wc-api', 'Ecpay_Receive_Cvs_Info', home_url('/')) );

                    $data = array(
                        'MerchantTradeNo'  => 'no' . date('ymdHis'),
                        'LogisticsSubType' => esc_html($sub_type),
                        'ServerReplyURL'   => $replyUrl,
                        'Device'           => wp_is_mobile()
                    );
                    $html = $this->helper->getCvsMap($data, $buttonText);

                    $args = array(
                        'category'         => $this->category,
                        'html'             => $html,
                        'buttonText'       => $buttonText,
                        'cvsInfo'          => $cvsInfo,
                        'method_title'     => $this->method_title,
                        'shipping_options' => $this->shipping_options,
                        'shipping_type'    => $shipping_type,
                        'shipping_name'    => $shipping_name
                    );
                    wc_get_template('checkout/ECPay-checkout-shipping-options.php', $args, '', ECPAY_PLUGIN_PATH . 'templates/');

                    add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields');
                } else {
                    $this->ecpay_cvs_info_session_clear();
                    wc_get_template('checkout/ECPay-checkout-remove-cvs-form.php', [], '', ECPAY_PLUGIN_PATH . 'templates/');
                }
            }
            catch(Exception $e)
            {
                echo esc_html($e->getMessage());
            }
        }

        /**
         * 是否為綠界物流
         *
         * @return boolean
         */
        private function is_ecpay_shipping()
        {
            global $woocommerce;
            $chosen_method = $woocommerce->session->get('chosen_shipping_methods');
            if (is_array($chosen_method) === true) {
                if (in_array($this->id, $chosen_method) === true) {
                    return true;
                }
            }
            return false;
        }

        /**
         * 綠界物流是否啟用
         *
         * @return boolean
         */
        private function is_ecpay_shipping_enable()
        {
            $gateway_settings = get_option('woocommerce_ecpay_shipping_settings', '');
            if (empty($gateway_settings['enabled']) === false) {
                if ($gateway_settings['enabled'] === 'yes') {
                    return true;
                }
            }
            return false;
        }

        /**
         * 是否在綠界物流設定有效金額範圍內
         *
         * @return boolean
         */
        private function in_ecpay_shipping_amount()
        {
            global $woocommerce;
            $cart_total = intval($woocommerce->cart->total);
            if (($cart_total >= $this->ecpaylogistic_min_amount) ||
                ($cart_total <= $this->ecpaylogistic_max_amount)) {
                return true;
            }
            return false;
        }

        /**
         * 是否為有效綠界物流 Facade
         *
         * @return boolean
         */
        private function is_avalible_shipping_facade()
        {
            if ($this->is_ecpay_shipping() === true) {
                if (is_checkout() === true) {
                    if ($this->is_ecpay_shipping_enable() === true) {
                        if ($this->in_ecpay_shipping_amount()) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        /**
         * 啟用 SESSION
         *
         * @return void
         */
        private function start_session()
        {
            if (isset($_SESSION) === false) {
                session_start();
            }
        }

        /**
         * 由 SESSION 取得物流類別
         *
         * @return string $shipping_type
         */
        private function get_session_shipping_type()
        {
            $this->start_session();
            if (isset($_SESSION['ecpayShippingType']) === true) {
                $shipping_type = sanitize_text_field($_SESSION['ecpayShippingType']);
            } else {
                $shipping_type = '';
            }
            return $shipping_type;
        }

        /**
         * 取得物流子類別
         *
         * @param  string $type
         * @return string $sub_type
         */
        private function get_sub_type($type)
        {
            $shipping_methods = $this->helper->getPaymentCategory($this->category);

            if (array_key_exists($type, $shipping_methods) === true) {
                $sub_type = $shipping_methods[$type];
            } else {
                $sub_type = '';
            }
            return $sub_type;
        }

        /**
         * 取得超商名稱 Facade
         *
         * @return string $sub_type
         */
        private function get_sub_type_facade()
        {
            $session_shipping_type = $this->get_session_shipping_type();

            $sub_type = $this->get_sub_type($session_shipping_type);

            return $sub_type;
        }

        /**
         * 是否為綠界取貨付款
         *
         * @return boolean
         */
        private function is_ecpay_shipping_pay()
        {
            $shipping_type = $this->get_session_shipping_type();
            return (in_array($shipping_type, $this->helper->shippingPayList));
        }

        /**
         * 移除所有非取貨付款金流
         *
         * @param  array $available_gateways
         * @return array $available_gateways
         */
        private function only_ecpay_shipping_pay($available_gateways)
        {
            foreach ($available_gateways as $name => $info) {
                if ($name !== ECPAY_SHIPPING_PAY_ID) {
                    unset($available_gateways[$name]);
                }
            }
            return $available_gateways;
        }

        /**
         * 移除綠界取貨付款金流
         *
         * @param  array $available_gateways
         * @return array $available_gateways
         */
        private function remove_ecpay_shipping_pay($available_gateways)
        {
            if (isset($available_gateways[ECPAY_SHIPPING_PAY_ID]) === true) {
                unset($available_gateways[ECPAY_SHIPPING_PAY_ID]);
            }
            return $available_gateways;
        }

        /**
         * 過濾有效付款方式
         *
         * @param  array $available_gateways
         * @return array $filtered
         */
        public function wcso_filter_available_payment_gateways($available_gateways)
        {
            $filtered = $available_gateways;
            if (is_checkout()) {
                try {
                    if ($this->is_avalible_shipping_facade() === true &&
                        $this->is_ecpay_shipping() === true &&
                        $this->is_ecpay_shipping_pay() === true
                    ) {
                        // 只保留取貨付款金流
                        $filtered = $this->only_ecpay_shipping_pay($available_gateways);
                    } else {
                        // 移除取貨付款金流
                        $filtered = $this->remove_ecpay_shipping_pay($available_gateways);
                    }
                }
                catch(Exception $e)
                {
                    echo esc_html($e->getMessage());
                }
            }
            return $filtered;
        }

        /**
         * 儲存訂單運送方式
         *
         * @param integer $order_id
         * @param array $posted
         * @return void
         */
        public function wcso_field_update_shipping_order_meta( $order_id, $posted )
        {
            global $woocommerce;
            if (is_array($posted['shipping_method']) && in_array($this->id, $posted['shipping_method'])) {
                if ( isset( $_POST['shipping_option'] ) && !empty( $_POST['shipping_option'] ) ) {
                    update_post_meta( $order_id, 'ecPay_shipping', sanitize_text_field( $_POST['shipping_option'] ) );
                    $woocommerce->session->_chosen_shipping_option = sanitize_text_field( $_POST['shipping_option'] );
                }
            } else { //visible  in cart, hidden in checkout
                $chosen_method = $woocommerce->session->get('chosen_shipping_methods');
                $chosen_option= $woocommerce->session->_chosen_shipping_option;
                if (is_array($chosen_method) && in_array($this->id, $chosen_method) && $chosen_option) {
                    update_post_meta( $order_id, 'wcso_shipping_option', sanitize_text_field($woocommerce->session->_chosen_shipping_option ));
                }
            }
        }

        /**
         * 後台訂單頁-運送方式超商欄位
         *
         * @param  object $order
         * @return void
         */
        public function wcso_display_shipping_admin_order_meta($order)
        {
            $shippingMethod = $this->helper->ecpayLogistics[$this->category];
            $ecpayShipping = get_post_meta($order->get_id(), 'ecPay_shipping', true);

            if (array_key_exists($ecpayShipping, $shippingMethod)) {
                $ecpayShippingMethod = $shippingMethod[$ecpayShipping];
            }

            if (get_post_meta($order->get_id()) && isset($ecpayShippingMethod)) {
                echo '<p class="form-field"><strong>' . $this->title . ':</strong> ' . $ecpayShippingMethod . '(' . $ecpayShipping . ')' . '</p>';
            }
        }

        // 接收電子地圖回傳資訊
        public function ecpay_receive_cvs_info()
        {
            $this->set_cvs_info_session();

            // 轉導回結帳頁面
            wp_safe_redirect( esc_url(wc_get_page_permalink('checkout')) );
        }

        // 清除門市資訊 session
        public function ecpay_cvs_info_session_clear()
        {
            $cvsInfo = [
                'CVSStoreName' => '',
                'CVSAddress' => '',
                'CVSTelephone' =>  '',
                'CVSStoreID' => ''
            ];

            WC()->session->set('cvsInfo', $cvsInfo);
        }

        // 前台 - 儲存結帳頁資料至Session
        public function ecpay_get_checkout_session()
        {
            require_once(ECPAY_PLUGIN_PATH . 'ECPayLogisticsSession.php');

            session_start();

            if ( ! is_array($_POST)) {
                return;
            }

            $serviceList = array('ecpayShippingType', 'checkoutInput');
            $checkoutInput = array();
            foreach ($_POST as $key => $value) {
                if (in_array($key, $serviceList)) {
                    // 判斷 sanitize 的變數型態
                    if (is_array($value)) {
                        $checkoutInput[$key] = array_map( 'sanitize_text_field', wp_unslash( $value ) );
                    } else {
                        $checkoutInput[$key] = sanitize_text_field($value);
                    }
                }
            }
            $LogisticsField = 'ECPay_' . key($checkoutInput);
            $LogisticsObj = new $LogisticsField;
            $LogisticsObj->setInput($checkoutInput);
            $LogisticsObj->validate();
            $LogisticsObj->store();

            exit;
        }

        /**
         * Thankyou page
         *
         * @return void
         */
        public function thankyou_page()
        {
            return;
        }

        /**
         * 載入 ECPay-shipping-checkout.js
         *
         * @return void
         */
        public function ecpay_shipping_checkout()
        {
            // 設定結帳用資料
            $this->start_session();
            $checkout = array();
            foreach ($this->checkoutData as $key => $value) {
                if (isset($_SESSION[$value]) === true) {
                    $checkout[$value] = sanitize_text_field($_SESSION[$value]);
                } else {
                    $checkout[$value] = '';
                }
            }

            // 載入js
            wp_enqueue_script(
                'ecpay_shipping_checkout',
                plugins_url( 'js/ECPay-shipping-checkout.js', __FILE__ ),
                array(),
                ECPAY_SHIPPING_PLUGIN_VERSION,
                true
            );

            // 傳遞資料到 ECPay-shipping-checkout.js
            $ecpay_checkout_request = array(
                'category' => $this->category,
                'ajaxUrl' => str_replace( 'http:', $this->isHttps(), add_query_arg('wc-api', '', home_url('/')) ),
                'checkoutData' => json_encode($checkout)
            );
            wp_localize_script( 'ecpay_shipping_checkout', 'ecpay_checkout_request', $ecpay_checkout_request );
        }

        /**
         * 載入 ECPay-shipping-helper.js
         *
         * @return void
         */
        public function ecpay_shipping_helper()
        {
            // 載入js
            wp_register_script(
                'ecpay_shipping_helper',
                plugins_url( 'js/ECPay-shipping-helper.js', __FILE__ ),
                array(),
                ECPAY_SHIPPING_PLUGIN_VERSION,
                true
            );
        }

        /**
         * 判斷URL是否為 https
         *
         * @return string $replace
         */
        public function isHttps()
        {
            global $woocommerce;

            if ($this->helper->isHttps(home_url( add_query_arg( array(), $woocommerce->request ) ))) {
                $replace = 'https:';
            } else {
                $replace = 'http:';
            }

            return $replace;
        }

        /**
         * 取得運送方式
         *
         * @param  array $methods
         * @return array $methods
         */
        public function add_wcso_shipping_methods( $methods )
        {
            $methods[] = $this;
            return $methods;
        }
    }

    new ECPayShippingMethods();
}

// 儲存運送方式至session
add_action( 'wp_ajax_wcso_save_selected', 'save_selected' );
add_action( 'wp_ajax_nopriv_wcso_save_selected', 'save_selected' );

function save_selected()
{
    if ( isset( $_GET['shipping_option'] ) && !empty( $_GET['shipping_option'] ) ) {
        global $woocommerce;
        $selected_option = sanitize_text_field($_GET['shipping_option']);
        $woocommerce->session->_chosen_shipping_option = sanitize_text_field( $selected_option );
    }
    die();
}

if (is_admin()) {
    add_action('plugins_loaded', 'ECPayShippingMethodsInit');
    add_filter('woocommerce_admin_shipping_fields', 'ecpay_custom_admin_shipping_fields' );
} else {
    add_action('woocommerce_shipping_init', 'ECPayShippingMethodsInit');
}

// 後台訂單頁-門市資訊欄位
function ecpay_custom_admin_shipping_fields($fields)
{
    global $post;

    $fields['purchaserStore'] = array(
        'label' => __( '門市名稱', 'purchaserStore' ),
        'value' => get_post_meta( $post->ID, '_shipping_purchaserStore', true ),
        'show'  => true
    );

    $fields['purchaserAddress'] = array(
        'label' => __( '門市地址', 'purchaserAddress' ),
        'value' => get_post_meta( $post->ID, '_shipping_purchaserAddress', true ),
        'show'  => true
    );

    $fields['purchaserPhone'] = array(
        'label' => __( '門市電話', 'purchaserPhone' ),
        'value' => get_post_meta( $post->ID, '_shipping_purchaserPhone', true ),
        'show'  => true
    );

    $fields['CVSStoreID'] = array(
        'label' => __( '門市代號', 'CVSStoreID' ),
        'value' => get_post_meta( $post->ID, '_shipping_CVSStoreID', true ),
        'show'  => true
    );

    return $fields;
}

add_action('plugins_loaded', 'ecpay_shipping_integration_plugin_init', 0);

// 金流主架構: 綠界科技超商取貨付款
function ecpay_shipping_integration_plugin_init()
{
    # Make sure WooCommerce is setted.
    if (!class_exists('WC_Payment_Gateway')) {
        add_action( 'admin_notices', 'ecpay_wc_shipping_render_wc_inactive_notice' );
        return;
    }

    class WC_Gateway_Ecpay_Logis extends WC_Payment_Gateway
    {
        private $helper = null;

        public function __construct()
        {
            # Load the translation
            $this->id = ECPAY_SHIPPING_PAY_ID;
            $this->icon = '';
            $this->has_fields = false;
            $this->method_title = '綠界科技超商取貨付款';
            $this->method_description = "若使用綠界科技超商取貨，請開啟此付款方式";

            $this->init_form_fields();
            $this->init_settings();
            $this->title = $this->get_option( 'title' );

            $this->ecpay_payment_methods = $this->get_option('ecpay_payment_methods');

            # Helper
            $this->helper = ECPayCustomFeatures::getHelper();

            # Register a action to save administrator settings
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            # Register a action to redirect to ECPay payment center
            add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );

            # Register a action to process the callback
            add_action('woocommerce_api_wc_gateway_ecpay_logis', array($this, 'receive_response'));
        }

        /**
         * Initialise Gateway Settings Form Fields
         */
        public function init_form_fields ()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title'   => __( 'Enable/Disable', 'woocommerce' ),
                    'type'    => 'checkbox',
                    'label'   => '啟用綠界科技超商取貨付款',
                    'default' => 'yes'
                ),
                'title' => array(
                    'title'       => __( 'Title', 'woocommerce' ),
                    'type'        => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'default'     => "綠界科技超商取貨付款",
                    'desc_tip'    => true,
                )
            );
        }

        /**
         * Check the payment method and the chosen payment
         */
        public function validate_fields()
        {
            return true;
        }

        /**
         * Process the payment
         */
        public function process_payment($order_id)
        {
            # Update order status
            $order = wc_get_order( $order_id );
            $order->update_status( 'on-hold', '綠界科技超商取貨' );
            $order->reduce_order_stock();
            WC()->cart->empty_cart();

            # 自動開立發票
            $ecpayShipping = get_post_meta( $order->get_id(), 'ecPay_shipping', true );
            if ($this->helper->isCollection($ecpayShipping) == EcpayIsCollection::YES) {
                $this->auto_invoice($order->get_id());
            }

            return array(
                'result'    => 'success',
                'redirect'  => $this->get_return_url( $order )
            );
        }

        /**
         * Process the callback
         */
        public function receive_response()
        {
            // 判斷 sanitize 的變數型態
            if (is_array($_REQUEST)) {
                $response = array_map( 'sanitize_text_field', wp_unslash( $_REQUEST ) );
            } else {
                $response = sanitize_text_field($_REQUEST);
            }

            // 設定Helper MerchantID
            $this->helper->setMerchantId($response['MerchantID']);

            // 取得訂單編號
            $MerchantTradeNo = $this->helper->getMerchantTradeNo($response['MerchantTradeNo']);

            // 綠界科技的物流交易編號
            if (isset($response['AllPayLogisticsID'])) {
                $this->storeLogisticMeta($response);
            }

            // 取得訂單
            $order = wc_get_order( $MerchantTradeNo );

            // 新增訂單備註
            $order->add_order_note(esc_html(print_r($response, true)));

            // 解析回傳狀態碼
            $status = $this->helper->receiveResponse($response['RtnCode']);
            if ($status != 99) {
                if ($status == 0) {
                    $order->update_status( 'ecpay', "商品已出貨" );
                }
                if (get_post_meta( $MerchantTradeNo, '_payment_method', true ) == 'ecpay_shipping_pay') {
                    if ($status == 1) {
                        // 更新訂單狀態
                        $order->update_status( 'processing', "處理中" );
                    }
                }
            }

            $this->helper->responseSuccess();
        }

        private function storeLogisticMeta(array $response)
        {
            $tradeNo = $this->helper->getMerchantTradeNo($response['MerchantTradeNo']);

            $metaKeys = array('AllPayLogisticsID', 'CVSPaymentNo', 'CVSValidationNo');
            foreach ($metaKeys as $key) {
                update_post_meta($tradeNo, "_{$key}", sanitize_text_field($response[$key]));
            }
        }

        // 自動開立發票
        private function auto_invoice($order_id)
        {
            // call invoice model
            $invoice_active_ecpay   = 0 ;

            // 取得目前啟用的外掛
            $active_plugins = (array) get_option( 'active_plugins', array() );

            // 加入其他站點啟用的外掛
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

            // 判斷ECPay發票模組是否有啟用
            foreach ($active_plugins as $key => $value) {
                if ((strpos($value, '/woocommerce-ecpayinvoice.php') !== false)) {
                    $invoice_active_ecpay = 1;
                }
            }

            // 自動開立發票
            if ($invoice_active_ecpay == 1) { // ecpay
                $aConfig_Invoice = get_option('wc_ecpayinvoice_active_model') ;

                if (isset($aConfig_Invoice) && $aConfig_Invoice['wc_ecpay_invoice_enabled'] == 'enable' && $aConfig_Invoice['wc_ecpay_invoice_auto'] == 'auto' ) {
                    do_action('ecpay_auto_invoice', $order_id);
                }
            }
        }

        // thankyou page
        public function thankyou_page()
        {
            return;
        }
    }

    /**
     * Add the Gateway Plugin to WooCommerce
     * */
    function woocommerce_add_ecpay_plugin2($methods)
    {
        $methods[] = 'WC_Gateway_Ecpay_Logis';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_ecpay_plugin2');
}

// 後台設定頁-class不存在時使用的提示
function ecpay_wc_shipping_render_wc_inactive_notice()
{

    $message = sprintf(
        /* translators: %1$s and %2$s are <strong> tags. %3$s and %4$s are <a> tags */
        __( '%1$sWooCommerce ECPay Shipping is inactive%2$s as it requires WooCommerce. Please %3$sactivate WooCommerce version 2.5.5 or newer%4$s', 'woocommerce' ),
        '<strong>',
        '</strong>',
        '<a href="' . admin_url( 'plugins.php' ) . '">',
        '&nbsp;&raquo;</a>'
    );

    printf( '<div class="error"><p>%s</p></div>', $message );
}

add_action('woocommerce_checkout_process', 'ecpay_checkout_field_process');

// 前台結帳頁-檢查是否有選擇超商門市、物流欄位防呆
function ecpay_checkout_field_process()
{
    // Check if set, if its not set add an error.
    global $woocommerce;
    $shipping_method = $woocommerce->session->get( 'chosen_shipping_methods' );

    if ( ECPayShippingOptions::hasVirtualProducts() !== true ) {

        // 物流選擇綠界且門市資料為空時才提示
        if ($shipping_method[0] == "ecpay_shipping" && (! $_POST['purchaserStore']) ) {
            wc_add_notice( __( '請選擇取貨門市' ), 'error' );
        }

        // 物流選擇綠界時，檢查[收件人姓名]、[收件人電話]欄位
        if ($shipping_method[0] == "ecpay_shipping") {

            $shipping_name      = sanitize_text_field($_POST['billing_last_name']) .
                                  sanitize_text_field($_POST['billing_first_name']);
            $shipping_phone     = sanitize_text_field($_POST['billing_phone']);

            // 是否運送到不同地址
            if ($_POST['ship_to_different_address'] == '1') {
                $shipping_name  = sanitize_text_field($_POST['shipping_last_name']) .
                                  sanitize_text_field($_POST['shipping_first_name']);
            }

            // 正規表示式比對
            if ( (!preg_match('/'. ECPayShippingOptions::getRegex('ReceiverName') .'/u', $shipping_name))) {
                wc_add_notice( __( '收件人的姓名 請輸入中文2~5個字或英文4~10個字' ), 'error' );
            }

            if ( !preg_match('/'. ECPayShippingOptions::getRegex('ReceiverCellPhone') .'/', $shipping_phone)) {
                wc_add_notice( __( '收件人的聯絡電話 請輸入09開頭的手機號碼共10碼' ), 'error' );
            }
        }
    }
}

add_filter('woocommerce_update_order_review_fragments', 'ecpay_check_checkout_payment_method', 10, 1);

// 前台結帳頁-判斷可以顯示的付款方式
function ecpay_check_checkout_payment_method($value)
{
    global $woocommerce;
    $cartTotalAmount = intval($woocommerce->cart->total);
    $availableGateways = WC()->payment_gateways->get_available_payment_gateways();
    if (is_array($availableGateways)) {
        $paymentGateways = array_keys($availableGateways);
    }

    if ( ! in_array(ECPAY_SHIPPING_PAY_ID, $paymentGateways)) {
        return $value;
    }

    $ecpayShippingType = array(
        'FAMI_Collection',
        'UNIMART_Collection' ,
        'HILIFE_Collection'
    );

    $paymentMethods = array();
    if (!empty($_SESSION['ecpayShippingType'])) {
        if (in_array($_SESSION['ecpayShippingType'], $ecpayShippingType)) {
            foreach ($paymentGateways as $key => $gateway) {
                if ($gateway !== ECPAY_SHIPPING_PAY_ID) {
                    array_push($paymentMethods, '<li class="wc_payment_method payment_method_' . $gateway . '">');
                }
            }
        }
    } else {
        array_push($paymentMethods, '<li class="wc_payment_method payment_method_ecpay_shipping_pay">');
    }

    if (is_array($paymentMethods) && $cartTotalAmount > 0) {
        $hide = ' style="display: none;"';
        foreach ($paymentMethods as $key => $paymentMethod) {
            $value['.woocommerce-checkout-payment'] = substr_replace($value['.woocommerce-checkout-payment'], $hide, strpos($value['.woocommerce-checkout-payment'], $paymentMethod) + strlen($paymentMethod) - 1, 0);
        }
    }

    return $value;
}

add_action('woocommerce_after_checkout_validation', 'ecpay_validate_payment_after_checkout');

// 前台結帳頁-檢查付款方式
function ecpay_validate_payment_after_checkout()
{
    $shippingMethod = sanitize_text_field($_POST['shipping_method'][0]);
    $paymentMethod  = sanitize_text_field($_POST['payment_method']);

    // 結帳時物流不是選擇綠界，但金流卻選擇綠界取貨付款時，顯示提示訊息
    if ($shippingMethod !== ECPAY_SHIPPING_ID) {
        if ($paymentMethod === ECPAY_SHIPPING_PAY_ID) {
            wc_add_notice("請選擇付款方式", 'error');
        }
    }
}

add_action('woocommerce_checkout_update_order_meta', 'ecpay_checkout_field_save' );

// 前台結帳頁-儲存訂單門市資訊
function ecpay_checkout_field_save( $order_id )
{
    // save custom field to order
    if ( !empty($_POST['purchaserStore']) && !empty($_POST['purchaserAddress']) ) {
        update_post_meta( $order_id, '_shipping_purchaserStore'  , sanitize_text_field( $_POST['purchaserStore'] ) );
        update_post_meta( $order_id, '_shipping_purchaserAddress', sanitize_text_field( $_POST['purchaserAddress'] ) );
        update_post_meta( $order_id, '_shipping_purchaserPhone'  , sanitize_text_field( $_POST['purchaserPhone'] ) );
        update_post_meta( $order_id, '_shipping_CVSStoreID'      , sanitize_text_field( $_POST['CVSStoreID'] ) );
    }
}

add_action( 'woocommerce_review_order_before_payment', 'action_woocommerce_review_order_before_payment', 10, 0 );

// 前台結帳頁-隱藏ECPay/O'Pay付款方式
// (woo)define the woocommerce_review_order_before_payment callback
function action_woocommerce_review_order_before_payment()
{
    wc_get_template('checkout/ECPay-checkout-hide-payment-methods.php', array(), '', ECPAY_PLUGIN_PATH . 'templates/');
}

// 訂單詳細資料頁-顯示超商取貨門市資訊
add_action('woocommerce_order_details_after_order_table', 'ecpay_custom_order_detail_shipping_address');

// 訂單Email-顯示超商取貨門市資訊
add_action('woocommerce_email_after_order_table', 'ecpay_custom_order_detail_shipping_address');

function ecpay_custom_order_detail_shipping_address($order)
{
    $_purchaserStore = (array_key_exists('_shipping_purchaserStore', get_post_meta($order->get_id()))) ? get_post_meta( $order->get_id(), '_shipping_purchaserStore', true ) : get_post_meta( $order->get_id(), '_purchaserStore', true );
    if ( !empty($_purchaserStore) ) {
        $ecpayShipping = get_post_meta( $order->get_id(), 'ecPay_shipping', true );
        $shippingStore = array(
            'HILIFE'            => '萊爾富',
            'HILIFE_Collection' => '萊爾富取貨付款',
            'FAMI'              => '全家',
            'FAMI_Collection'   => '全家取貨付款',
            'UNIMART'           => '統一超商',
            'UNIMART_Collection'=> '統一超商寄貨便取貨付款'
        );
        if (array_key_exists($ecpayShipping, $shippingStore)) {
            $ecpayShippingStore = $shippingStore[$ecpayShipping];
            $_purchaserAddress = (array_key_exists('_shipping_purchaserAddress', get_post_meta($order->get_id()))) ? get_post_meta( $order->get_id(), '_shipping_purchaserAddress', true ) : get_post_meta( $order->get_id(), '_purchaserAddress', true );
            $_purchaserPhone = (array_key_exists('_shipping_purchaserPhone', get_post_meta($order->get_id()))) ? get_post_meta( $order->get_id(), '_shipping_purchaserPhone', true ) : get_post_meta( $order->get_id(), '_purchaserPhone', true );

            $order->set_shipping_company(sanitize_text_field($_purchaserPhone));
            $order->set_shipping_address_1(sanitize_text_field($_purchaserAddress));
            $order->set_shipping_address_2('');
            $order->set_shipping_first_name(sanitize_text_field($ecpayShippingStore) . '&nbsp;' . sanitize_text_field($_purchaserStore));
            $order->set_shipping_last_name('');
            $order->set_shipping_city('');
            $order->set_shipping_state('');
            $order->set_shipping_postcode('');
            $order->set_shipping_country('');
        }
    }
}

class ECPayShippingOptions
{
    // 判斷是否為虛擬商品
    public static function hasVirtualProducts()
    {
        global $woocommerce;

        $hasVirtualProducts = false;
        $virtualProducts = 0;
        $products = $woocommerce->cart->get_cart();
        foreach ( $products as $product ) {
            $isVirtual = get_post_meta( $product['product_id'], '_virtual', true );

            if ( $isVirtual == 'yes' ) {
                $virtualProducts++;
            } else {
                return false;
            }
        }

        if ( count($products) == $virtualProducts ) {
            $hasVirtualProducts = true;
        }

        return $hasVirtualProducts;
    }

    // 取得正規表示式
    public static function getRegex($field)
    {
        $pattern = '';

        switch ($field) {
            // [寄件人姓名] : 中文2~5個字、英文4~10個字
            case 'SenderName':
                $pattern = '^[\u4e00-\u9fff\u3400-\u4dbf]{2,5}|[a-zA-Z]{4,10}$';
                break;
            // [收件人姓名] : 中文2~5個字、英文4~10個字 (PHP寫法)
            case 'ReceiverName':
                $pattern = '^([\x{4e00}-\x{9fff}\x{3400}-\x{4dbf}]{2,5}|[a-zA-Z]{4,10})$';
                break;
            // [寄件人手機]、[收件人手機] : 只允許數字，09開頭共10碼
            case 'SenderCellPhone':
            case 'ReceiverCellPhone':
                $pattern = '^09[0-9]{8}$';
                break;
            default:
                break;
        }
        return $pattern;
    }
}

/**
 * class ECPayCustomFeatures
 * 自訂通用功能
 */
class ECPayCustomFeatures
{
    // 取得Helper
    public static function getHelper()
    {
        $helper = new ECPayLogisticsHelper();

        # 設定目錄路徑
        $helper->dirPath = ECPAY_PLUGIN_URL;

        # 設定時區
        $helper->setTimezone(get_option('timezone_string'));

        # 設定訂單狀態
        $data = array(
            'Pending'    => 'wc-pending',
            'Processing' => 'wc-processing',
            'OnHold'     => 'wc-on-hold',
            'Ecpay'      => 'wc-ecpay',
        );
        $helper->setOrderStatus($data);

        return $helper;
    }
}

?>