<?php

require_once(ECPAY_PLUGIN_PATH . 'ECPayLogisticsModuleHelper.php');

final class ECPayLogisticsHelper extends ECPayLogisticsModuleHelper
{
    /**
     * @var string SDK class name(required)
     */
    protected $sdkClassName = 'EcpayWooLogistics';

    /**
     * @var string SDK file path(required)
     */
    protected $sdkFilePath = ECPAY_PLUGIN_PATH . 'ECPay.Logistics.Integration.Shell.php';

    /**
     * @var string 目錄路徑
     */
    public $dirPath = '';

    /**
     * @var array 綠界物流項目
     */
    public $ecpayLogistics = array(
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

    /**
     * @var array 綠界取貨付款列表
     */
    public $shippingPayList = array(
        'HILIFE_Collection',
        'FAMI_Collection',
        'UNIMART_Collection'
    );

    /**
     * @var array 綠界C2C列印繳費單功能列表
     */
    public $paymentFormMethods = array(
        'FAMIC2C'    => 'PrintFamilyC2CBill',
        'UNIMARTC2C' => 'PrintUnimartC2CBill',
        'HILIFEC2C'  => 'PrintHiLifeC2CBill',
    );

    /**
     * @var array 訂單狀態
     */
    public $orderStatus = array(
        'pending'    => '', // 等待付款
        'processing' => '', // 處理中(已付款)
        'onHold'     => '', // 保留
        'ecpay'      => '', // ECPay Shipping
    );

    /**
     * ECPayLogisticHelper constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * addPaymentFormFileds function
     * 加入列印繳費單所需要的欄位
     *
     * @param  array $paymentFormFileds
     * @return void
     */
    public function addPaymentFormFileds($paymentFormFileds)
    {
        $fields = array('AllPayLogisticsID', 'CVSPaymentNo', 'CVSValidationNo');
        foreach ($fields as $field) {
            if (isset($paymentFormFileds["{$field}"])) {
                $this->sdk->Send[$field] = $paymentFormFileds["{$field}"];
            }
        }
    }

    /**
     * changeStore function
     * 變更門市
     *
     * @param  array  $data
     * @return string $postHTML
     */
    public function changeStore($data)
    {
        $postHTML  = $this->genPostHTML($data, 'ecpay');
        $postHTML .= "<input class='button' type='button' onclick='ecpayChangeStore();' value='變更門市' /><br />";

        return $postHTML;
    }

    /**
     * createShippingOrder function
     * 建立物流訂單
     *
     * @param  array $data
     * @return void
     */
    public function createShippingOrder($data)
    {
        $this->sdk->HashKey = $data['HashKey'];
        $this->sdk->HashIV  = $data['HashIV'];
        $this->sdk->Send = array(
            'MerchantID'           => $this->getMerchantId(),
            'MerchantTradeNo'      => $this->setMerchantTradeNo($data['MerchantTradeNo']),
            'MerchantTradeDate'    => $this->getDateTime('Y/m/d H:i:s', ''),
            'LogisticsType'        => EcpayLogisticsType::CVS,
            'LogisticsSubType'     => $data['LogisticsSubType'],
            'GoodsAmount'          => $data['GoodsAmount'],
            'CollectionAmount'     => $data['CollectionAmount'],
            'IsCollection'         => $this->isCollection($data['IsCollection']),
            'GoodsName'            => '網路商品一批',
            'SenderName'           => $data['SenderName'],
            'SenderPhone'          => $data['SenderPhone'],
            'SenderCellPhone'      => $data['SenderCellPhone'],
            'ReceiverName'         => $data['ReceiverName'],
            'ReceiverPhone'        => $data['ReceiverPhone'],
            'ReceiverCellPhone'    => $data['ReceiverCellPhone'],
            'ReceiverEmail'        => $data['ReceiverEmail'],
            'TradeDesc'            => '',
            'ServerReplyURL'       => $data['ServerReplyURL'],
            'LogisticsC2CReplyURL' => $data['LogisticsC2CReplyURL'],
            'Remark'               => $this->replaceStrNewLine($data['Remark']),
            'PlatformID'           => '',
        );

        $this->sdk->SendExtend = array(
            'ReceiverStoreID' => $data['ReceiverStoreID'],
            'ReturnStoreID'   => $data['ReturnStoreID']
        );

        return $this->sdk->CreateShippingOrder('物流訂單建立', 'Map');
    }

    /**
     * genPostHTML function
     * 產生 POST 提交表單
     *
     * @param  string $target 表單 action 目標
     * @return string $postHTML
     */
    public function genPostHTML($params, $target = '_self')
    {
        $whiteList = array(
            'formId'    ,
            'serviceURL',
            'postParams',
        );
        $inputs = $this->only($params, $whiteList);

        $postParams = array(
            'MerchantID'       ,
            'MerchantTradeNo'  ,
            'LogisticsSubType' ,
            'IsCollection'     ,
            'ServerReplyURL'   ,
            'ExtraData'        ,
            'Device'           ,
            'LogisticsType'    ,
        );
        $inputs['postParams'] = $this->only($inputs['postParams'], $postParams);

        $postHTML = $this->addNextLine('  <form id="'. $inputs['formId'] .'" method="POST" action="' . $inputs['serviceURL'] . '" target="' . $target . '"  style="display:none">');
        foreach ($inputs['postParams'] as $name => $value) {
            if ($name == 'Device') {
                $postHTML .= $this->addNextLine('    <input type="hidden" name="' . $name . '" value="' . $this->getDevice($value) . '" />');
            } else {
                $postHTML .= $this->addNextLine('    <input type="hidden" name="' . $name . '" value="' . $value . '" />');
            }
        }
        $postHTML .= $this->addNextLine('  </form>');

        return $postHTML;
    }

    /**
     * getCvsMap function
     * 電子地圖
     *
     * @param  array $data
     * @return void  $html
     */
    public function getCvsMap($data, $buttonText = '電子地圖')
    {
        // Filter inputs
        $whiteList = array(
            'MerchantID',
            'MerchantTradeNo' ,
            'LogisticsSubType',
            'IsCollection' ,
            'ServerReplyURL' ,
            'ExtraData',
            'Device',
        );
        $inputs = $this->only($data, $whiteList);

        $this->sdk->Send  = array(
            'MerchantID'       => $this->getMerchantId(),
            'MerchantTradeNo'  => $inputs['MerchantTradeNo'],
            'LogisticsSubType' => $inputs['LogisticsSubType'],
            'IsCollection'     => EcpayIsCollection::NO,
            'ServerReplyURL'   => $inputs['ServerReplyURL'],
            'ExtraData'        => '',
            'Device'           => $this->getDevice($inputs['Device'])
        );

        // CvsMap
        $html = $this->sdk->CvsMap($buttonText, '_self');

        return $html;
    }

    /**
     * getDevice function.
     * 取得裝置類別 : PC or MOBILE
     *
     * @param  bool    是否為mobile裝置
     * @return integer PC = 0 ; MOBILE = 1
     */
    public function getDevice($isMobile)
    {
        // 預設裝置為PC
        $device = EcpayDevice::PC;

        if($isMobile){
            $device = EcpayDevice::MOBILE;
        }
        return $device;
    }

    /**
     * getOrderStatusPending function
     * 取得購物車訂單狀態 - 等待付款
     *
     * @return string 等待付款
     */
    public function getOrderStatusPending()
    {
        return $this->orderStatus['pending'];
    }

    /**
     * getOrderStatusProcessing function
     * 取得購物車訂單狀態 - 處理中(已付款)
     *
     * @return string 處理中(已付款)
     */
    public function getOrderStatusProcessing()
    {
        return $this->orderStatus['processing'];
    }

    /**
     * getOrderStatusOnHold function
     * 取得購物車訂單狀態 - 保留
     *
     * @return string 保留
     */
    public function getOrderStatusOnHold()
    {
        return $this->orderStatus['onHold'];
    }

    /**
     * getOrderStatusEcpay function
     * 取得購物車訂單狀態 - ECPay Shipping
     *
     * @return string ECPay Shipping
     */
    public function getOrderStatusEcpay()
    {
        return $this->orderStatus['ecpay'];
    }

    /**
     * getPaymentCategory function
     * 取得物流子類別清單
     *
     * @param  string $category 物流類型 "B2C" or "C2C"
     * @return array            物流子類別清單
     */
    public function getPaymentCategory($category)
    {
        if ($category == "B2C") {
            return array('FAMI' => EcpayLogisticsSubType::FAMILY,
                'FAMI_Collection' => EcpayLogisticsSubType::FAMILY,
                'UNIMART' => EcpayLogisticsSubType::UNIMART,
                'UNIMART_Collection' => EcpayLogisticsSubType::UNIMART,
                'HILIFE' => EcpayLogisticsSubType::HILIFE,
                'HILIFE_Collection' => EcpayLogisticsSubType::HILIFE
            );
        } else {
            return array(
                'FAMI' => EcpayLogisticsSubType::FAMILY_C2C,
                'FAMI_Collection' => EcpayLogisticsSubType::FAMILY_C2C,
                'UNIMART' => EcpayLogisticsSubType::UNIMART_C2C,
                'UNIMART_Collection' => EcpayLogisticsSubType::UNIMART_C2C,
                'HILIFE' => EcpayLogisticsSubType::HILIFE_C2C,
                'HILIFE_Collection' => EcpayLogisticsSubType::HILIFE_C2C
            );
        }
    }

    /**
     * getReceiverName function
     * 取得收件者姓名
     *
     * @param  array    $orderInfo    訂單資訊
     * @return string                 收件者姓名
     */
    public function getReceiverName($orderInfo)
    {
        $receiverName = '';
        if (array_key_exists('shippingFirstName', $orderInfo) && array_key_exists('shippingLastName', $orderInfo)) {
            $receiverName = $orderInfo['shippingLastName'] . $orderInfo['shippingFirstName'];
        } else {
            $receiverName = $orderInfo['billingLastName'] . $orderInfo['billingFirstName'];
        }
        return $receiverName;
    }

    /**
     * getStatusTable function.
     * 狀態對照表
     *
     * @param  array   $data
     * @var $data['category']     string, required, "B2C" or "C2C"
     * @var $data['orderStatus']  string, required, 訂單狀態
     * @var $data['isCollection'] string, required, 是否為取貨付款 "Y" or "N"
     *
     * @return integer $status       比對狀態
     */
    public function getStatusTable($data)
    {
        // 回傳狀態
        $status = '99';

        // Filter inputs
        $whiteList = array(
            'category'     ,
            'orderStatus'  ,
            'isCollection' ,
        );
        $inputs = $this->only($data, $whiteList);

        // 接收參數
        $category = $inputs['category'];
        $orderStatus = $inputs['orderStatus'];
        $isCollection = $this->isCollection($inputs['isCollection']);

        // 對照狀態
        if (($isCollection == EcpayIsCollection::YES && $orderStatus == $this->getOrderStatusOnHold()) || ($isCollection == EcpayIsCollection::NO && $orderStatus == $this->getOrderStatusProcessing())) {
            // 可建立物流訂單的狀態:
            // 貨到付款並且訂單狀態為保留(訂單成立) 或 除了貨到付款以外的付款方式並且訂單狀態為處理中(已付款)
            $status = 0;
        } elseif ($orderStatus == $this->getOrderStatusEcpay() && $category == 'C2C') {
            // 訂單狀態為ECPay Shipping並且物流類型為C2C
            $status = 1;
        }

        return $status;
    }

    /**
     * isCollection function
     * 是否代收貨款(取貨付款)
     *
     * @param  string  $shippingType
     * @return string  'Y' or 'N'
     */
    public function isCollection($shippingType)
    {
        return (in_array($shippingType, $this->shippingPayList)) ? EcpayIsCollection::YES : EcpayIsCollection::NO;
    }

    /**
     * paymentForm function
     * 產生列印繳款單
     *
     * @param  array  $data
     * @param  string $paymentFormMethod
     * @param  array  $paymentFormFileds
     * @return string
     */
    public function paymentForm($data, $paymentFormMethod, $paymentFormFileds)
    {
        $postHTML = '';

        $this->sdk->HashKey = $data['HashKey'];
        $this->sdk->HashIV  = $data['HashIV'];
        $this->sdk->Send = array(
            'MerchantID'           => $this->getMerchantId(),
            'MerchantTradeNo'      => $this->setMerchantTradeNo($data['MerchantTradeNo']),
            'MerchantTradeDate'    => $this->getDateTime('Y/m/d H:i:s', ''),
            'LogisticsType'        => EcpayLogisticsType::CVS,
            'LogisticsSubType'     => $data['LogisticsSubType'],
            'GoodsAmount'          => $data['GoodsAmount'],
            'CollectionAmount'     => $data['CollectionAmount'],
            'IsCollection'         => $this->isCollection($data['IsCollection']),
            'GoodsName'            => '網路商品一批',
            'SenderName'           => $data['SenderName'],
            'SenderPhone'          => $data['SenderPhone'],
            'SenderCellPhone'      => $data['SenderCellPhone'],
            'ReceiverName'         => $data['ReceiverName'],
            'ReceiverPhone'        => $data['ReceiverPhone'],
            'ReceiverCellPhone'    => $data['ReceiverCellPhone'],
            'ReceiverEmail'        => $data['ReceiverEmail'],
            'TradeDesc'            => '',
            'ServerReplyURL'       => $data['ServerReplyURL'],
            'LogisticsC2CReplyURL' => $data['LogisticsC2CReplyURL'],
            'Remark'               => $data['Remark'],
            'PlatformID'           => '',
        );

        $this->sdk->SendExtend = array(
            'ReceiverStoreID' => $data['ReceiverStoreID'],
            'ReturnStoreID'   => $data['ReturnStoreID']
        );

        if (isset($paymentFormFileds['AllPayLogisticsID'], $paymentFormMethod) and method_exists($this->sdk, $paymentFormMethod)) {
            $this->addPaymentFormFileds($paymentFormFileds);
            $postHTML  = $this->sdk->$paymentFormMethod();
            $postHTML .= "<input class='button' type='button' onclick='ecpayPaymentForm();' value='列印繳款單' /><br />";
        }
        return $postHTML;
    }

    /**
     * receiveResponse function
     * 物流貨態回傳值
     *
     * @param  string  $rtnCode 回傳的狀態碼
     * @return integer $status  對應狀態
     */
    public function receiveResponse($rtnCode)
    {
        $status = 99 ;
        $aSuccessCodes = ['300', '2001', '2067', '3022'];

        // 判斷是否回傳成功狀態
        if (in_array($rtnCode, $aSuccessCodes)) {

            // 300  : 訂單處理中(已收到訂單資料)
            // 2001 : 檔案傳送成功
            if ($rtnCode == '300' || $rtnCode == '2001') {
                $status = 0 ;
            }

            // 2067 : 消費者成功取件
            // 3022 : 買家已到店取貨
            if ($rtnCode == '2067' || $rtnCode == '3022') {
                $status = 1 ;
            }
        }

        return $status;
    }

    /**
     * setOrderStatus function
     * 設定購物車訂單狀態 - 全部
     *
     * @param  array $data
     * @return void
     */
    public function setOrderStatus($data)
    {
        $status = array('Pending', 'Processing', 'OnHold', 'Ecpay');

        foreach($status as $value) {
            $funName = 'setOrderStatus' . $value; // 組合 function name
            $this->$funName($data[$value]);
        }
    }

    /**
     * setOrderStatusPending function
     * 設定購物車訂單狀態 - 等待付款
     *
     * @param  string $value 要儲存的值
     * @return void
     */
    public function setOrderStatusPending($value)
    {
        $this->orderStatus['pending'] = $value;
    }

    /**
     * setOrderStatusProcessing function
     * 設定購物車訂單狀態 - 處理中(已付款)
     *
     * @param  string $value 要儲存的值
     * @return void
     */
    public function setOrderStatusProcessing($value)
    {
        $this->orderStatus['processing'] = $value;
    }

    /**
     * setOrderStatusOnHold function
     * 設定購物車訂單狀態 - 保留
     *
     * @param  string $value 要儲存的值
     * @return void
     */
    public function setOrderStatusOnHold($value)
    {
        $this->orderStatus['onHold'] = $value;
    }

    /**
     * setOrderStatusEcpay function
     * 設定購物車訂單狀態 - ECPay Shipping
     *
     * @param  string $value 要儲存的值
     * @return void
     */
    public function setOrderStatusEcpay($value)
    {
        $this->orderStatus['ecpay'] = $value;
    }

    /**
     * 替換字串換行符號
     *
     * @param  string $subject 被執行動作字串
     * @param  string $replace 替換換行符號的值，預設為空格
     * @return string
     */
    public function replaceStrNewLine($subject, $replace = ' ')
    {
        return str_replace(array("\r", "\n", "\r\n", "\n\r"), $replace, $subject);
    }
}