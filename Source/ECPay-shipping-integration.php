<?php
/**
 * @copyright Copyright (c) 2018 Green World FinTech Service Co., Ltd. (https://www.ecpay.com.tw)
 * @version 1.2.181030
 *
 * Plugin Name: ECPay Logistics for WooCommerce
 * Plugin URI: https://www.ecpay.com.tw
 * Description: ECPay Integration Logistics Gateway for WooCommerce
 * Version: 1.2.181030
 * Author: ECPay Green World FinTech Service Co., Ltd. 
 * Author URI:  techsupport@ecpay.com.tw
 */

defined( 'ABSPATH' ) or exit;
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once('ECPay.Logistics.Integration.php');
define('PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('SHIPPING_ID', 'ecpay_shipping');
define('SHIPPING_PAY_ID', 'ecpay_shipping_pay');
require_once('lib/Common.php');

if (!class_exists('ECPayShippingStatus')) {
    class ECPayShippingStatus
    {
        function __construct()
        {
            add_filter('wc_order_statuses', array($this, 'add_statuses'));
            add_action('init', array($this, 'register_status'));
        }

        function register_status()
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

        function add_statuses($order_statuses) 
        {
            $order_statuses['wc-ecpay'] = _x( 'ECPay Shipping', 'Order status', 'woocommerce' );

            return $order_statuses;
        }
    }
    new ECPayShippingStatus();
}

function ECPayShippingMethodsInit()
{

    # Make sure WooCommerce is setted.
    if (!class_exists('WC_Shipping_Method')) {
        add_action( 'admin_notices', 'wc_ecpayshipping_render_wc_inactive_notice' );
        return;
    }

    class ECPayShippingMethods extends WC_Shipping_Method
    {
        public $MerchantID;
        public $HashKey;
        public $HashIV;
        public $ECPay_Logistics = array(
            'B2C' => array(
                'HILIFE'            => '萊爾富',
                'HILIFE_Collection' => '萊爾富取貨付款',
                'FAMI'              => '全家',
                'FAMI_Collection'   => '全家取貨付款',
                'UNIMART'           => '統一超商',
                'UNIMART_Collection'=> '統一超商寄貨便取貨付款'
            ),
            'C2C' => array(
                'HILIFE'            => '萊爾富',
                'HILIFE_Collection' => '萊爾富取貨付款',
                'FAMI'              => '全家',
                'FAMI_Collection'   => '全家取貨付款',
                'UNIMART'           => '統一超商',
                'UNIMART_Collection'=> '統一超商寄貨便取貨付款'
            )
        );

        // 綠界取貨付款列表
        private $shipping_pay_list = array(
            'HILIFE_Collection',
            'FAMI_Collection',
            'UNIMART_Collection'
        );
        private static $paymentFormMethods = array(
            'FAMIC2C'    => 'PrintFamilyC2CBill',
            'UNIMARTC2C' => 'PrintUnimartC2CBill',
            'HILIFEC2C'  => 'PrintHiLifeC2CBill',
        );
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

            if (method_exists($woocommerce->session, 'get') && ($woocommerce->session->get( 'chosen_shipping_methods' ) != null)) {
                $chosen_methods = $woocommerce->session->get( 'chosen_shipping_methods' );
            }

            if (in_array(SHIPPING_ID, $chosen_methods)) {
                add_filter( 'woocommerce_checkout_fields' , array(&$this, 'custom_override_checkout_fields'));
            }

            $this->id = SHIPPING_ID;
            $this->method_title = "綠界科技超商取貨";
            $this->title = "綠界科技超商取貨";
            $this->options_array_label = '綠界科技超商取貨';
            $this->method_description = '';

            add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
            add_action('woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_admin_options'));
            add_action('woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_shipping_options'));

            $this->init();

            // add the action 
            add_action( 'woocommerce_admin_order_data_after_order_details', array(&$this,'action_woocommerce_admin_order_data_after_shipping_address' ));
        }

        /**
         * Init settings
         *
         * @access public
         * @return void
         */
        function init()
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
            $this->SenderPhone  = $this->get_option('sender_phone');
            $this->SenderCellPhone = $this->get_option('sender_cell_phone');
            $this->ecpaylogistic_min_amount = $this->get_option('ecpaylogistic_min_amount');
            $this->ecpaylogistic_max_amount = $this->get_option('ecpaylogistic_max_amount');
            $this->ecpaylogistic_free_shipping_amount = $this->get_option('ecpaylogistic_free_shipping_amount');

            $this->get_shipping_options();

            // 結帳頁 Filter
            
            add_filter('woocommerce_shipping_methods', array(&$this, 'add_wcso_shipping_methods'), 10, 1);
            
            // 隱藏與顯示貨到付款金流
            add_filter('woocommerce_available_payment_gateways', array(&$this, 'wcso_filter_available_payment_gateways'), 10, 1);

            
            // 結帳頁 Hook
            
            // 顯示電子地圖
            add_action('woocommerce_review_order_after_shipping', array(&$this, 'wcso_review_order_shipping_options'));

            // 加入物流必要 JS
            add_action('woocommerce_review_order_before_submit', array(&$this, 'wcso_process_before_submit'));


            add_action('woocommerce_checkout_update_order_meta', array(&$this, 'wcso_field_update_shipping_order_meta'), 10, 2);
            if (is_admin()) {
                add_action( 'woocommerce_admin_order_data_after_shipping_address', array(&$this, 'wcso_display_shipping_admin_order_meta'), 10, 2 );
            }
        }

        private function addPaymentFormFileds($orderInfo, $AL)
        {
            $fields = array('AllPayLogisticsID', 'CVSPaymentNo', 'CVSValidationNo');
            foreach ($fields as $field) {
                if (isset($orderInfo["_{$field}"])) {
                    $AL->Send[$field] = $orderInfo["_{$field}"][0];
                }
            }
        }

        // 後台 - 訂單詳細頁面的產生物流單按鈕
        function action_woocommerce_admin_order_data_after_shipping_address()
        {
            try {
                global $woocommerce, $post;

                //訂單資訊
                $orderInfo = get_post_meta($post->ID);
                if ( ! is_array($orderInfo) ) {
                    return false;
                }
                if ( ! array_key_exists('ecPay_shipping', $orderInfo) ) {
                    return false;
                }
                if ( ! isset($orderInfo['ecPay_shipping'][0]) ) {
                    return false;
                }

                //物流子類型
                $subType = "";
                $shippingMethod = ECPayShippingOptions::paymentCategory($this->category);
                if (array_key_exists($orderInfo['ecPay_shipping'][0], $shippingMethod)) {
                    $subType = $shippingMethod[$orderInfo['ecPay_shipping'][0]];
                    if (isset(self::$paymentFormMethods[$subType])) {
                        $paymentFormMethod = self::$paymentFormMethods[$subType];
                    }
                }

                //是否代收貨款
                $ecpayShipping = $this->shipping_pay_list;
                $IsCollection = (in_array($orderInfo['ecPay_shipping'][0], $ecpayShipping)) ? 'Y' : 'N';

                $orderObj = new WC_Order($post->ID);
                $itemsInfo = $orderObj->get_items();

                //訂單的商品
                $items = array();

                foreach ($itemsInfo as $key => $value) {
                    $items[] = $value['name'];
                }
                
                //訂單金額
                $temp = explode('.', $orderInfo['_order_total'][0]);
                $totalPrice = $temp[0];
                
                $AL = new EcpayLogistics();
                $AL->HashKey = $this->HashKey;
                $AL->HashIV  = $this->HashIV;
                $AL->Send = array(
                    'MerchantID'           => $this->MerchantID,
                    'MerchantTradeNo'      => ($this->testMode == 'yes') ? $post->ID . date("mdHis") : $post->ID,
                    'MerchantTradeDate'    => date('Y/m/d H:i:s'),
                    'LogisticsType'        => LogisticsType::CVS ,
                    'LogisticsSubType'     => $subType,
                    'GoodsAmount'          => (int)$totalPrice,
                    'CollectionAmount'     => (int)$totalPrice,
                    'IsCollection'         => $IsCollection,
                    'GoodsName'            => '網路商品一批',
                    'SenderName'           => $this->SenderName,
                    'SenderPhone'          => $this->SenderPhone,
                    'SenderCellPhone'      => $this->SenderCellPhone,
                    'ReceiverName'         => $this->get_receiver_name($orderInfo),
                    'ReceiverPhone'        => $orderInfo['_billing_phone'][0],
                    'ReceiverCellPhone'    => $orderInfo['_billing_phone'][0],
                    'ReceiverEmail'        => $orderInfo['_billing_email'][0],
                    'TradeDesc'            => '',
                    'ServerReplyURL'       => str_replace( 'http:', (isset($_SERVER['HTTPS']) ? "https:" : "http:"), add_query_arg('wc-api', 'WC_Gateway_Ecpay_Logis', home_url('/')) ),
                    'LogisticsC2CReplyURL' => str_replace( 'http:', (isset($_SERVER['HTTPS']) ? "https:" : "http:"), add_query_arg('wc-api', 'WC_Gateway_Ecpay_Logis', home_url('/')) ),
                    'Remark'               => esc_html($orderObj->get_customer_note()),
                    'PlatformID'           => ''
                );
                
                $AL->SendExtend = array(
                    'ReceiverStoreID' => (array_key_exists('_shipping_CVSStoreID', $orderInfo)) ? $orderInfo['_shipping_CVSStoreID'][0] : ((isset($orderInfo['_CVSStoreID'][0])) ? $orderInfo['_CVSStoreID'][0] : ''),
                    'ReturnStoreID'   => (array_key_exists('_shipping_CVSStoreID', $orderInfo)) ? $orderInfo['_shipping_CVSStoreID'][0] : ((isset($orderInfo['_CVSStoreID'][0])) ? $orderInfo['_CVSStoreID'][0] : '')
                );

                // 狀態為完成or已出貨，後台隱藏建立物流單按鈕
                $postStatus = (null !== get_post_status( $post->ID )) ? get_post_status( $post->ID ) : '';
                if ($postStatus !== 'wc-ecpay' && $postStatus !== 'wc-completed') {
                    echo '</form>';

                    echo $AL->CreateShippingOrder('物流訂單建立', 'Map');
                    echo "<input class='button' type='button' value='建立物流訂單' onclick='create();'>";

                    if ($this->testMode == 'yes') {
                        $serviceUrl = 'https://logistics-stage.ecpay.com.tw/Express/map';
                    } else {
                        $serviceUrl = 'https://logistics.ecpay.com.tw/Express/map';
                    }
                ?>
                    <form id="ecpayChangeStoreForm" method="post" target="ecpay" action="<?php echo $serviceUrl; ?>" style="display:none">
                        <input type="hidden" id="MerchantID" name="MerchantID" value="<?php echo $this->MerchantID?>" />
                        <input type="hidden" id="MerchantTradeNo" name="MerchantTradeNo" value="<?php echo $post->ID;?>" />
                        <input type="hidden" id="LogisticsSubType" name="LogisticsSubType" value="<?php echo $subType;?>" />
                        <input type="hidden" id="IsCollection" name="IsCollection" value="N" />
                        <input type="hidden" id="ServerReplyURL" name="ServerReplyURL" value="<?php echo PLUGIN_URL . "/getChangeResponse.php";?>" />
                        <input type="hidden" id="ExtraData" name="ExtraData" value="" />
                        <input type="hidden" id="Device" name="Device" value="0" />
                        <input type="hidden" id="LogisticsType" name="LogisticsType" value="CVS" />
                    </form>
                <?php
                    echo "<input class='button' type='button' onclick='changeStore();' value='變更門市' /><br />";
                } elseif ($postStatus === 'wc-ecpay' && $this->category == 'C2C') {
                    // 後台建立物流訂單之後，產生列印繳款單
                    echo '</form>';

                    if (isset($orderInfo['_AllPayLogisticsID'], $paymentFormMethod) and method_exists($AL, $paymentFormMethod)) {
                        $this->addPaymentFormFileds($orderInfo, $AL);
                        echo $AL->$paymentFormMethod();
                        echo "<input class='button' type='button' value='列印繳款單' onclick='paymentForm();'>";
                    }
                }
            }catch(Exception $e) {
                echo $e->getMessage();
            }

            ?>
            <script type="text/javascript">
                function create() {
                    var ecPayshipping = document.getElementById('ECPayForm');
                    map = window.open('','Map',config='height=500px,width=900px');
                    if (map) {
                        ecPayshipping.submit();
                    }
                }

                function changeStore() {
                    var changeStore = document.getElementById('ecpayChangeStoreForm');
                    map = window.open('','ecpay',config='height=790px,width=1020px');
                    if (map) {
                        changeStore.submit();
                    }
                }

                function paymentForm() {
                    document.getElementById('ECPayForm').submit();
                }

                (function() {
                    document.getElementById('__paymentButton').style.display = 'none';
                })();
            </script>
            <?php
        }

        /**
         * 取得收件者姓名
         * @param  array    $orderInfo    訂單資訊
         * @return string                 收件者姓名
         */
        private function get_receiver_name($orderInfo)
        {
            $receiverName = '';
            if (array_key_exists('_shipping_first_name', $orderInfo) && array_key_exists('_shipping_last_name', $orderInfo)) {
                $receiverName = $orderInfo['_shipping_last_name'][0] . $orderInfo['_shipping_first_name'][0];
            } else {
                $receiverName = $orderInfo['_billing_last_name'][0] . $orderInfo['_billing_first_name'][0];
            }
            return $receiverName;
        }

        function custom_override_checkout_fields($fields)
        {
            if ( ECPayShippingOptions::hasVirtualProducts() !== true ) {
                $this->fill_checkout_info();
                $fields = $this->custom_checkout_fields($fields);
            }
            return $fields;
        }

        // 填入結帳資料
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

        private function custom_checkout_fields($fields)
        {
            $fields['billing']['purchaserStore'] = array(
                'label' => __( '超商取貨門市名稱', 'purchaserStore' ),
                'default'       => isset($_REQUEST['CVSStoreName']) ? $_REQUEST['CVSStoreName'] : '',
                'required'      => true,
                'class'         => array('hidden')
            );
            $fields['billing']['purchaserAddress'] = array(
                'label' => __( '超商取貨門市地址', 'purchaserAddress' ),
                'default'       => isset($_REQUEST['CVSAddress']) ? $_REQUEST['CVSAddress'] : '',
                'required'      => true,
                'class'         => array('hidden')
            );
            $fields['billing']['purchaserPhone'] = array(
                'label' => __( '超商取貨門市電話', 'purchaserPhone' ),
                'default'       => isset($_REQUEST['CVSTelephone']) ? $_REQUEST['CVSTelephone'] : '',
                'class'         => array('hidden'),
            );
            $fields['billing']['CVSStoreID'] = array(
                'label' => __( '超商取貨門市代號', 'CVSStoreID' ),
                'default'       => isset($_REQUEST['CVSStoreID']) ? $_REQUEST['CVSStoreID'] : '',
                'required'      => true,
                'class'         => array('hidden')
            );
            return $fields;
        }

        /**
        * calculate_shipping function.
        *
        * @access public
        * @param array $package (default: array())
        * @return void
        */
        function calculate_shipping($package = array())
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
        function init_form_fields()
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
                    'default' => 'ECPAY'
                ),
                'sender_cell_phone' => array(
                    'title' => "寄件人手機",
                    'type' => 'text',
                    'default' => ''
                ),
                'sender_phone' => array(
                    'title' => "寄件人電話",
                    'type' => 'text',
                    'default' => ''
                ),
                'ecpaylogistic_min_amount' => array(
                    'title' => "超商取貨最低金額",
                    'type' => 'text',
                    'default' => '10'
                ),
                'ecpaylogistic_max_amount' => array(
                    'title' => "超商取貨最高金額",
                    'type' => 'text',
                    'default' => '19999'
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
        * admin_options function.
        *
        * @access public
        * @return void
        */
        function admin_options() 
        {
            ?>
                <h3><?php echo $this->method_title; ?></h3>
                <p><?php _e( 'Local delivery is a simple shipping method for delivering orders locally.', 'woocommerce'); ?></p>
                <table class="form-table">
                    <?php $this->generate_settings_html(); ?>
                </table>
            <?php
        }

       /**
         * is_available function.
         *
         * @access public
         * @param array $package
         * @return bool
         */
        function is_available($package)
        {
            global $woocommerce;

            if (( $woocommerce->cart->cart_contents_total < $this->ecpaylogistic_min_amount) || ( $woocommerce->cart->cart_contents_total > $this->ecpaylogistic_max_amount)) return false;

            $gateway_settings = get_option( 'woocommerce_ecpay_shipping_settings', '' );
            if (empty( $gateway_settings['enabled'] ) || $gateway_settings['enabled'] === 'no' || $this->enabled == 'no') {
                return false;
            }

            // If post codes are listed, let's use them.
            $codes = '';
            if ($this->codes != '') {
                foreach (explode(',', $this->codes) as $code) {
                    $codes[] = $this->clean($code);
                }
            }

            if (is_array($codes)) {
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
            if ($this->availability == 'specific') {
                $ship_to_countries = $this->countries;
            } else {
                $ship_to_countries = array_keys(WC()->countries->get_shipping_countries());
            }

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
        function clean($code)
        {
            return str_replace('-', '', sanitize_title($code)) . ( strstr($code, '*') ? '*' : '' );
        }
        
        /**
        * validate_shipping_options_table_field function.
        *
        * @access public
        * @param mixed $key
        * @return bool
        */
        function validate_shipping_options_table_field( $key )
        {
            return false;
        }
        
        /**
         * generate_options_table_html function.
         * 後台運送項目
         *
         * @access public
         * @return string
         */
        function generate_shipping_options_table_html()
        {
            ob_start();
            ?>
                <tr valign="top">
                    <th scope="row" class="titledesc">運送項目:</th>
                    <td class="forminp" id="<?php echo $this->id; ?>_options">
                    <table class="shippingrows widefat" cellspacing="0">
                        <tbody>
                        <?php
                            foreach ($this->ECPay_Logistics['B2C'] as $key => $value) {
                        ?>
                            <tr class="option-tr">
                                <td><input type="checkbox" name="<?php echo $key;?>" value="<?php echo $key; ?>" <?php if (in_array($key, $this->shipping_options)) echo 'checked';?>> <?php echo $value; ?></td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                    </td>
                </tr>
            <?php

            return ob_get_clean();
        }
        
        /**
         * process_shipping_options function.
         *
         * @access public
         * @return void
         */
        function process_shipping_options()
        {
            // 取得物流類型。避免第一次設定無法取得物流類型問題
            $ecpay_category = $this->category;
            if (empty($ecpay_category) === true) {
                if (isset($_POST['woocommerce_ecpay_shipping_category']) === true) {
                    $ecpay_category = $_POST['woocommerce_ecpay_shipping_category'];
                }
            }

            $options = array();
            if (isset($this->ECPay_Logistics[$ecpay_category]) === true) {
                foreach ($this->ECPay_Logistics[$ecpay_category] as $key => $value) {
                    if (array_key_exists($key, $_POST)) $options[] = $key ;    
                }
            }
            
            update_option($this->id, $options);
            $this->get_shipping_options();
        }

        /**
        * get_shipping_options function.
        *
        * @access public
        * @return void
        */
        function get_shipping_options()
        {
            $this->shipping_options = array_filter( (array) get_option( $this->id ) );
        }

        //前台購物車顯示option
        function wcso_review_order_shipping_options()
        {
            global $woocommerce;
            try {
                if ($this->is_avalible_shipping_facade() === true) {
                    // 取得物流子類別
                    $shipping_type = $this->get_session_shipping_type();
                    $sub_type = $this->get_sub_type_facade();

                    // 建立電子地圖
                    $shipping_name = $this->ECPay_Logistics[$this->category];
                    $replyUrl = esc_url(wc_get_page_permalink('checkout'));
                    $cvsObj = new EcpayLogistics();
                    $cvsObj->Send  = array(
                        'MerchantID' => $this->MerchantID,
                        'MerchantTradeNo' => 'no' . date('YmdHis'),
                        'LogisticsSubType' => $sub_type,
                        'IsCollection' => IsCollection::NO,
                        'ServerReplyURL' => $replyUrl,
                        'ExtraData' => '',
                        'Device' => '0'
                    );
                    // CvsMap
                    $html = $cvsObj->CvsMap('電子地圖', '_self');
                    $options = '<option>------</option>';
                    foreach ($this->shipping_options as $option) {
                        $selected = ($shipping_type == esc_attr($option)) ? 'selected' : '';
                        $options .= '<option value="' . esc_attr($option) . '" ' . $selected . '>' . $shipping_name[$option] . '</option>';
                    }

                    echo '
                        <input type="hidden" id="category" name="category" value=' . $this->category . '>
                        <tr class="shipping_option">
                            <th>' . $this->method_title . '</th>
                            <td>
                                <select name="shipping_option" class="input-select" id="shipping_option">
                                    ' . $options . '
                                </select>
                                ' . $html . '
                                <p style="font-size: 0.8em;margin: 6px 0px; width: 84%;">
                                    ' . __( '門市名稱', 'purchaserStore' ) . ': <label id="purchaserStoreLabel"></label>
                                </p>
                                <p style="font-size: 0.8em;margin: 6px 0px; width: 84%;">
                                    ' . __( '門市地址', 'purchaserAddress' ) . ': <label id="purchaserAddressLabel"></label>
                                </p>
                                <p style="font-size: 0.8em;margin: 6px 0px; width: 84%;">
                                    ' . __( '門市電話', 'purchaserPhone' ) . ': <label id="purchaserPhoneLabel"></label>
                                </p>
                                <p style="font-size: 0.8em;color: #c9302c; width: 84%;">
                                    使用綠界科技超商取貨，連絡電話請填寫手機號碼。
                                </p>
                            </td>
                        </tr>
                    ';

                    add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields');
                }
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }

        // 是否為綠界物流
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

        // 綠界物流是否啟用
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

        // 是否在綠界物流設定有效金額範圍內
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

        // 是否為有效綠界物流 Facade
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

        // 啟用 SESSION
        private function start_session()
        {
            if (isset($_SESSION) === false) {
                session_start();
            }
        }

        // 由 SESSION 取得物流類別
        private function get_session_shipping_type()
        {
            $this->start_session();
            if (isset($_SESSION['ecpayShippingType']) === true) {
                $shipping_type = $_SESSION['ecpayShippingType'];
            } else {
                $shipping_type = '';
            }
            return $shipping_type;
        }

        // 取得物流子類別
        private function get_sub_type($type)
        {
            $shipping_methods = ECPayShippingOptions::paymentCategory($this->category);

            if (array_key_exists($type, $shipping_methods) === true) {
                $sub_type = $shipping_methods[$type];
            } else {
                // 預設為統一超商取貨
                $sub_type = $shipping_methods['UNIMART'];
            }
            return $sub_type;
        }

        // 取得超商名稱 Facade
        private function get_sub_type_facade()
        {
            $session_shipping_type = $this->get_session_shipping_type();

            $sub_type = $this->get_sub_type($session_shipping_type);

            return $sub_type;
        }

        // 是否為綠界取貨付款
        private function is_ecpay_shipping_pay()
        {
            $shipping_type = $this->get_session_shipping_type();
            return (in_array($shipping_type, $this->shipping_pay_list));
        }

        // 移除所有非取貨付款金流
        private function only_ecpay_shipping_pay($available_gateways)
        {
            foreach ($available_gateways as $name => $info) {
                if ($name !== SHIPPING_PAY_ID) {
                    unset($available_gateways[$name]);
                }
            }
            return $available_gateways;
        }

        // 移除綠界取貨付款金流
        private function remove_ecpay_shipping_pay($available_gateways)
        {
            if (isset($available_gateways[SHIPPING_PAY_ID]) === true) {
                unset($available_gateways[SHIPPING_PAY_ID]);
            }
            return $available_gateways;
        }

        // 過濾有效付款方式
        function wcso_filter_available_payment_gateways($available_gateways)
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
                    echo $e->getMessage();
                }
            }
            return $filtered;
        }

        // 加入物流必要 JS
        function wcso_process_before_submit()
        {
            try {
                $shipping_js_url = plugins_url( 'js/ECPay-shipping-checkout.js?1.2.180423', __FILE__ );

                if ($this->is_avalible_shipping_facade() === true) {
                    // 設定結帳用資料
                    $this->start_session();
                    $checkout = array();
                    foreach ($this->checkoutData as $key => $value) {
                        if (isset($_SESSION[$value]) === true) {
                            $checkout[$value] = $_SESSION[$value];
                        } else {
                            $checkout[$value] = '';
                        }
                    }

                    // 記錄結帳資料至 Session
                    ?>
                    <script>
                        // ecpay_checkout_request is required parameters for ECPay-shipping-checkout.js, 
                        // ECPay-shipping-checkout.js is script that register to be enqueued 'ecpay-shipping-checkout'.
                        var ecpay_checkout_request = {
                            ajaxUrl: '<?php echo PLUGIN_URL . '/getSession.php'; ?>',
                            checkoutData: <?php echo json_encode($checkout); ?>
                        };
                    </script>
                    <?php
                    echo '<script src="' . $shipping_js_url . '"></script>';

                }
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }

        function wcso_field_update_shipping_order_meta( $order_id, $posted )
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
                    update_post_meta( $order_id, 'wcso_shipping_option', $woocommerce->session->_chosen_shipping_option );
                }
            }
        }

        function wcso_display_shipping_admin_order_meta($order)
        {
            $shippingMethod = $this->ECPay_Logistics[$this->category];
            $ecpayShipping = get_post_meta($order->get_id(), 'ecPay_shipping', true);

            if (array_key_exists($ecpayShipping, $shippingMethod)) {
                $ecpayShippingMethod = $shippingMethod[$ecpayShipping];
            }
            
            if (get_post_meta($order->get_id()) && isset($ecpayShippingMethod)) {
                echo '<p class="form-field"><strong>' . $this->title . ':</strong> ' . $ecpayShippingMethod . '(' . $ecpayShipping . ')' . '</p>';
            }
        }

        function thankyou_page()
        {
            return;
        }

        function add_wcso_shipping_methods( $methods )
        {
            $methods[] = $this;
            return $methods;
        }
    }

    new ECPayShippingMethods();
}

add_action( 'wp_ajax_wcso_save_selected', 'save_selected' );
add_action( 'wp_ajax_nopriv_wcso_save_selected', 'save_selected' );

function save_selected()
{
    if ( isset( $_GET['shipping_option'] ) && !empty( $_GET['shipping_option'] ) ) {
        global $woocommerce;
        $selected_option = $_GET['shipping_option'];
        $woocommerce->session->_chosen_shipping_option = sanitize_text_field( $selected_option );
    }
    die();
}

if (is_admin()) {
    add_action('plugins_loaded', 'ECPayShippingMethodsInit');
    add_filter('woocommerce_admin_shipping_fields', 'ECPay_custom_admin_shipping_fields' );
} else {
    add_action('woocommerce_shipping_init', 'ECPayShippingMethodsInit');
}

function ECPay_custom_admin_shipping_fields($fields)
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

add_action('woocommerce_checkout_update_order_meta', 'checkout_field_save' );

function checkout_field_save( $order_id )
{
    // save custom field to order 
    if ( !empty($_POST['purchaserStore']) && !empty($_POST['purchaserAddress']) ) {
        update_post_meta( $order_id, '_shipping_purchaserStore'  , wc_clean( $_POST['purchaserStore'] ) );
        update_post_meta( $order_id, '_shipping_purchaserAddress', wc_clean( $_POST['purchaserAddress'] ) );
        update_post_meta( $order_id, '_shipping_purchaserPhone'  , wc_clean( $_POST['purchaserPhone'] ) );
        update_post_meta( $order_id, '_shipping_CVSStoreID'  , wc_clean( $_POST['CVSStoreID'] ) );
    }
}

add_action('plugins_loaded', 'ecpay_shipping_integration_plugin_init', 0);

function ecpay_shipping_integration_plugin_init()
{
    # Make sure WooCommerce is setted.
    if (!class_exists('WC_Payment_Gateway')) {
        add_action( 'admin_notices', 'wc_ecpayshipping_render_wc_inactive_notice' );
        return;
    }

    class WC_Gateway_Ecpay_Logis extends WC_Payment_Gateway
    {
        public function __construct()
        {
            # Load the translation
            $this->id = SHIPPING_PAY_ID;
            $this->icon = '';
            $this->has_fields = false;
            $this->method_title = '綠界科技超商取貨付款';
            $this->method_description = "若使用綠界科技超商取貨，請開啟此付款方式";

            $this->init_form_fields();
            $this->init_settings();
            $this->title = $this->get_option( 'title' );

            $this->ecpay_payment_methods = $this->get_option('ecpay_payment_methods');
            
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
            $response = $_REQUEST;

            $MerchantTradeNo = ECPayShippingOptions::getMerchantTradeNo($response);

            if (isset($response['AllPayLogisticsID'])) {
                $this->storeLogisticMeta($response);
            }

            if (!empty($response['CVSStoreName']) && !empty($response['CVSAddress']))
                $this->receive_changeStore_response($response);
            
            $order = wc_get_order( $MerchantTradeNo );


            $order->add_order_note(print_r($response, true));

            if ($response['RtnCode'] == '300' || $response['RtnCode'] == '2001') {
                $order->update_status( 'ecpay', "商品已出貨" );
            }

            if (get_post_meta( $MerchantTradeNo, '_payment_method', true ) == 'ecpay_shipping_pay') {
                if ($response['RtnCode'] == '2067' || $response['RtnCode'] == '3022') {
                    $order->update_status( 'processing', "處理中" );

                    // call invoice model
                    $invoice_active_ecpay   = 0 ;
                    $invoice_active_allpay  = 0 ;

                    $active_plugins = (array) get_option( 'active_plugins', array() );

                    $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

                    foreach ($active_plugins as $key => $value) {
                        if ((strpos($value, '/woocommerce-ecpayinvoice.php') !== false)) {
                            $invoice_active_ecpay = 1;
                        }

                        if ((strpos($value, '/woocommerce-allpayinvoice.php') !== false)) {
                            $invoice_active_allpay = 1;
                        }
                    }

                    if ($invoice_active_ecpay == 0 && $invoice_active_allpay == 1) { // allpay
                        if ( is_file( get_home_path() . '/wp-content/plugins/allpay_invoice/woocommerce-allpayinvoice.php') ) {
                            $aConfig_Invoice = get_option('wc_allpayinvoice_active_model') ;

                            if (isset($aConfig_Invoice) && $aConfig_Invoice['wc_allpay_invoice_enabled'] == 'enable' && $aConfig_Invoice['wc_allpay_invoice_auto'] == 'auto' ) {
                                do_action('allpay_auto_invoice', $order->get_id(), $ecpay_feedback['SimulatePaid']);
                            }
                        }
                    } elseif ($invoice_active_ecpay == 1 && $invoice_active_allpay == 0) { // ecpay
                        if ( is_file( get_home_path() . '/wp-content/plugins/ecpay_invoice/woocommerce-ecpayinvoice.php') ) {
                            $aConfig_Invoice = get_option('wc_ecpayinvoice_active_model') ;

                            if (isset($aConfig_Invoice) && $aConfig_Invoice['wc_ecpay_invoice_enabled'] == 'enable' && $aConfig_Invoice['wc_ecpay_invoice_auto'] == 'auto' ) {
                                do_action('ecpay_auto_invoice', $order->get_id(), $ecpay_feedback['SimulatePaid']);
                            }
                        }
                    }
                }
            }

            echo '1|OK';
            exit;
        }

        private function storeLogisticMeta(array $response)
        {
            $tradeNo = ECPayShippingOptions::getMerchantTradeNo($response);

            $metaKeys = array('AllPayLogisticsID', 'CVSPaymentNo', 'CVSValidationNo');
            foreach ($metaKeys as $key) {
                update_post_meta($tradeNo, "_{$key}", $response[$key]);
            }
        }

        public function receive_changeStore_response($response = array())
        {
            $MerchantTradeNo = ECPayShippingOptions::getMerchantTradeNo($response);
            
            $order = wc_get_order( $MerchantTradeNo );
            $order_status = $order->get_status();

            $order->add_order_note("會員已更換門市", 0, false );
            
            //訂單更新門市訊息
            update_post_meta($MerchantTradeNo, '_CVSStoreID', $response['CVSStoreID']);
            update_post_meta($MerchantTradeNo, '_purchaserStore', $response['CVSStoreName']);
            update_post_meta($MerchantTradeNo, '_purchaserAddress', $response['CVSAddress']);
            update_post_meta($MerchantTradeNo, '_purchaserPhone', $response['CVSTelephone']);
            update_post_meta($MerchantTradeNo, '_shipping_CVSStoreID', $response['CVSStoreID']);
            update_post_meta($MerchantTradeNo, '_shipping_purchaserStore', $response['CVSStoreName']);
            update_post_meta($MerchantTradeNo, '_shipping_purchaserAddress', $response['CVSAddress']);
            update_post_meta($MerchantTradeNo, '_shipping_purchaserPhone', $response['CVSTelephone']);

            ?>
            <script type="text/javascript">
            <!--
                window.close();
                alert('門市已更換，請重新整理頁面');
            //-->
            </script> 
            <?php
            exit;
        }

        function thankyou_page()
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

function wc_ecpayshipping_render_wc_inactive_notice()
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

add_action('woocommerce_checkout_process', 'checkout_field_process');

function checkout_field_process()
{
    // Check if set, if its not set add an error.
    global $woocommerce;
    $shipping_method = $woocommerce->session->get( 'chosen_shipping_methods' );

    if ( ECPayShippingOptions::hasVirtualProducts() !== true ) {
        if ($shipping_method[0] == "ecpay_shipping" && (! $_POST['purchaserStore']) )
            wc_add_notice( __( '請選擇取貨門市' ), 'error' );
    }
}

add_action('woocommerce_order_details_after_order_table', 'checkout_field_update_order_receipt', 10, 1 );

function checkout_field_update_order_receipt($order)
{
    $obj = new WC_Order($order->get_id());
    $shipping = $obj->get_items('shipping');
    
    $is_ecpayShipping = 'N';
    foreach ($shipping as $key => $value) {
        if ($value['method_id'] == 'ecPay_shipping') $is_ecpayShipping = 'Y';
    }
    
    if ($is_ecpayShipping == 'N') return;
    
    $ECPayShipping = array(
        'FAMI_Collection' => 'FAMI',
        'HILIFE_Collection' => 'HILIFE',
        'UNIMART_Collection' => 'UNIMART'
    );
    $LogisticsSubType = $ECPayShipping[get_post_meta( $order->get_id(), 'ecPay_shipping' ,true)];

    echo __( '門市代號', 'CVSStoreID' ) . ': ';
    echo  (array_key_exists('_shipping_CVSStoreID', get_post_meta($order->get_id()))) ? get_post_meta( $order->get_id(), '_shipping_CVSStoreID', true ) . '<br>' : get_post_meta( $order->get_id(), '_CVSStoreID', true ) . '<br>';
    echo __( '門市名稱', 'purchaserStore' ) . ': ';
    echo  (array_key_exists('_shipping_purchaserStore', get_post_meta($order->get_id()))) ? get_post_meta( $order->get_id(), '_shipping_purchaserStore', true ) . '<br>' : get_post_meta( $order->get_id(), '_purchaserStore', true ) . '<br>';
    echo __( '門市地址', 'purchaserAddress' ) . ': ';
    echo  (array_key_exists('_shipping_purchaserAddress', get_post_meta($order->get_id()))) ? get_post_meta( $order->get_id(), '_shipping_purchaserAddress', true ) . '<br>' : get_post_meta( $order->get_id(), '_purchaserAddress', true ) . '<br>';
    echo __( '門市電話', 'purchaserPhone' ) . ': ' ;
    echo  (array_key_exists('_shipping_purchaserPhone', get_post_meta($order->get_id()))) ? get_post_meta( $order->get_id(), '_shipping_purchaserPhone', true ) . '<br>' : get_post_meta( $order->get_id(), '_purchaserPhone', true ) . '<br>';
        
    if (!is_checkout()) echo '<input type="button" id="changeStore" value="更換門市" >';

    ?>
    <form id="ecpay<?php echo $order->get_id();?>" method="post" target="ecpay" action="https://logistics.ecpay.com.tw/Express/map" style="display:none">
        <input type="hidden" id="MerchantID" name="MerchantID" value="2000132" />
        <input type="hidden" id="MerchantTradeNo" name="MerchantTradeNo" value="<?php echo $order->get_id() . date('mdHis');?>" />
        <input type="hidden" id="LogisticsSubType" name="LogisticsSubType" value="<?php echo $LogisticsSubType;?>" />
        <input type="hidden" id="IsCollection" name="IsCollection" value="N" />
        <input type="hidden" id="ServerReplyURL" name="ServerReplyURL" value="<?php echo add_query_arg('wc-api', 'WC_Gateway_Ecpay_Logis', home_url('/'))?>" />
        <input type="hidden" id="ExtraData" name="ExtraData" value="" />
        <input type="hidden" id="Device" name="Device" value="0" />
        <input type="hidden" id="LogisticsType" name="LogisticsType" value="CVS" />
    </form>
    
    <script type="text/javascript">
        document.getElementById("changeStore").onclick = function() {
            map = window.open('','ecpay',config='height=790px,width=1020px');
            if (map) {
               document.getElementById("ecpay<?php echo $order->get_id();?>").submit();
            }
        }
    </script>
    <?php
}

add_filter('woocommerce_update_order_review_fragments', 'checkout_payment_method', 10, 1);

function checkout_payment_method($value)
{
    $value = check_checkout_payment_method($value);

    $CVSField = array(
        'purchaserStore' => '<label id="purchaserStoreLabel">',
        'purchaserAddress' => '<label id="purchaserAddressLabel">',
        'purchaserPhone' => '<label id="purchaserPhoneLabel">'
    );
    parse_str($_POST['post_data'], $postData);

    if (is_array($postData) && array_key_exists('CVSStoreID', $postData) && $postData['shipping_method'][0] === SHIPPING_ID) {
        foreach ($CVSField as $key => $valueLabel) {
            $value['.woocommerce-checkout-review-order-table'] = substr_replace($value['.woocommerce-checkout-review-order-table'], $postData[$key], strpos($value['.woocommerce-checkout-review-order-table'], $valueLabel) + strlen($valueLabel), 0);
        }
    }

    return $value;
}

function check_checkout_payment_method($value)
{
    global $woocommerce;
    $cartTotalAmount = intval($woocommerce->cart->total);
    $availableGateways = WC()->payment_gateways->get_available_payment_gateways();
    if (is_array($availableGateways)) {
        $paymentGateways = array_keys($availableGateways);
    }

    if ( ! in_array(SHIPPING_PAY_ID, $paymentGateways)) {
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
                if ($gateway !== SHIPPING_PAY_ID) {
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

add_action('woocommerce_after_checkout_validation', 'validate_payment_after_checkout');

function validate_payment_after_checkout()
{
    $shippingMethod = $_POST['shipping_method'][0];
    $paymentMethod = $_POST['payment_method'];

    if ($shippingMethod !== SHIPPING_ID) {
        if ($paymentMethod === SHIPPING_PAY_ID) {
            wc_add_notice("請選擇付款方式", 'error');
        }
    }
}

add_action( 'woocommerce_pay_order_after_submit', 'action_woocommerce_review_order_before_payment', 10, 0 );

function action_woocommerce_review_order_before_payment()
{
    ?>
    <script>
        var product_total = document.getElementsByClassName('wc_payment_method');
        var disabled_payment_method_ecpay = [
            'wc_payment_method payment_method_ecpay_shipping_pay',
            'wc_payment_method payment_method_allpay',
            'wc_payment_method payment_method_allpay_dca',
            'wc_payment_method payment_method_ecpay',
            'wc_payment_method payment_method_ecpay_dca'
        ];
        for (var i = 0; i < product_total.length; i++) {
            if (disabled_payment_method_ecpay.indexOf(product_total[i].className) !== -1) {
                document.getElementsByClassName(product_total[i].className)[0].style.display = 'none';
            }
        }
    </script>
    <?php
}

// 前台訂單明細頁面 顯示超商取貨門市資訊
add_action('woocommerce_order_details_after_order_table', 'custom_order_detail_shipping_address');

// 訂單Email顯示超商取貨門市資訊
add_action('woocommerce_email_after_order_table', 'custom_order_detail_shipping_address');

function custom_order_detail_shipping_address($order)
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

            $order->set_shipping_company($_purchaserPhone);
            $order->set_shipping_address_1($_purchaserAddress);
            $order->set_shipping_address_2('');
            $order->set_shipping_first_name($ecpayShippingStore . '&nbsp;' . $_purchaserStore);
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
    static function paymentCategory($category)
    {
        if ($category == "B2C") {
            return array('FAMI' => LogisticsSubType::FAMILY,
                'FAMI_Collection' => LogisticsSubType::FAMILY,
                'UNIMART' => LogisticsSubType::UNIMART,
                'UNIMART_Collection' => LogisticsSubType::UNIMART,
                'HILIFE' => LogisticsSubType::HILIFE,
                'HILIFE_Collection' => LogisticsSubType::HILIFE
            );
        } else {
            return array(
                'FAMI' => LogisticsSubType::FAMILY_C2C,
                'FAMI_Collection' => LogisticsSubType::FAMILY_C2C,
                'UNIMART' => LogisticsSubType::UNIMART_C2C,
                'UNIMART_Collection' => LogisticsSubType::UNIMART_C2C,
                'HILIFE' => LogisticsSubType::HILIFE_C2C,
                'HILIFE_Collection' => LogisticsSubType::HILIFE_C2C
            );
        }
    }

    static function hasVirtualProducts()
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

    static function getMerchantTradeNo($response)
    {
        //若為測試模式，拆除時間參數
        return (($response['MerchantID'] == '2000132') || ($response['MerchantID'] == '2000933')) ? strrev(substr(strrev($response['MerchantTradeNo']), 10)) : $response['MerchantTradeNo'];
    }
}
?>