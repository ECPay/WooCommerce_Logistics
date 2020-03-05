<?php
// SDK外殼，用來處理WooCommerce相容性問題

include_once(ECPAY_PLUGIN_PATH.'ECPay.Logistics.Integration.php');

final class EcpayWooLogistics extends EcpayLogistics
{
    public static function ServerPost($Params, $ServiceURL)
    {
        return EcpayWooIo::ServerPost($Params, $ServiceURL);
    }

    /**
     *  電子地圖
     *
     * @param     string    $ButtonDesc 按鈕顯示名稱
     * @param     string    $Target 表單 action 目標
     * @return    string
     */
    public function CvsMap($ButtonDesc = '電子地圖', $Target = '_self')
    {
        // 參數初始化
        $ParamList = array(
            'MerchantID'       => '',
            'MerchantTradeNo'  => '',
            'LogisticsSubType' => '',
            'IsCollection'     => '',
            'ServerReplyURL'   => '',
            'ExtraData'        => '',
            'Device'           => ''
        );
        $this->PostParams = $this->GetPostParams($this->Send, $ParamList);
        $this->PostParams['LogisticsType'] = EcpayLogisticsType::CVS;

        // 參數檢查
        $this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
        $this->ServiceURL = $this->GetURL('CVS_MAP');
        $this->ValidateLogisticsSubType(true);
        $this->ValidateIsCollection();
        $this->ValidateURL('ServerReplyURL', $this->PostParams['ServerReplyURL']);
        $this->ValidateString('ExtraData', $this->PostParams['ExtraData'], 20, true);
        $this->ValidateDevice();

        return $this->GenCvsPostHTML($ButtonDesc, $Target);
    }

    /**
     *  產生超商電子地圖 POST 提交表單
     *
     * @param     string    $ButtonDesc    按鈕顯示名稱
     * @param     string    $Target        表單 action 目標
     * @return    string
     */
    public function GenCvsPostHTML($ButtonDesc = '', $Target = '_self')
    {
        $PostHTML = $this->AddNextLine('  <form id="ECPayForm" method="POST" action="' . $this->ServiceURL . '" target="' . $Target . '">');
        foreach ($this->PostParams as $Name => $Value) {
            $PostHTML .= $this->AddNextLine('    <input type="hidden" name="' . $Name . '" value="' . $Value . '" />');
        }
        $PostHTML .= $this->AddNextLine('  </form>');

        $PostHTML = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $PostHTML);
        $PostHTML = str_replace('"', "'", $PostHTML);

        return $PostHTML;
    }
}


/**
 * cURL 設定值
 */
abstract class EcpayWooLogisticsCurl {

    /**
     * @var int 逾時時間
     */
    const TIMEOUT = 30;

}

if (!class_exists('EcpayWooIo', true)) {
	final class EcpayWooIo extends EcpayIo
	{
        /**
         * Server Post
         *
         * @param     array    $Params        Post 參數
         * @param     string   $ServiceURL    Post URL
         * @return    void
         */
        public static function ServerPost($Params, $ServiceURL)
        {
            $fields_string = http_build_query($Params);

            $rs = wp_remote_post($ServiceURL, array(
                'method'      => 'POST',
                'timeout'     => EcpayWooLogisticsCurl::TIMEOUT,
                'headers'     => array(),
                'httpversion' => '1.0',
                'sslverify'   => true,
                'body'        => $fields_string
            ));

            if ( is_wp_error($rs) ) {
                throw new Exception($rs->get_error_message());
            }

            return $rs['body'];
		}
	}
}