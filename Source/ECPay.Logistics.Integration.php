<?php
/**
 * ECPay 物流 SDK 
 *
 * @author		https://www.ecpay.com.tw
 * @version		1.0.181001
 */

/**
 *  物流類型
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class LogisticsType {
	const CVS = 'CVS';// 超商取貨
	const HOME = 'Home';// 宅配
}

/**
 *  物流子類型
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class LogisticsSubType {
	const TCAT = 'TCAT';// 黑貓(宅配)
	const ECAN = 'ECAN';// 宅配通
	const FAMILY = 'FAMI';// 全家
	const UNIMART = 'UNIMART';// 統一超商
	const HILIFE = 'HILIFE';// 萊爾富
	const FAMILY_C2C = 'FAMIC2C';// 全家店到店
	const UNIMART_C2C = 'UNIMARTC2C';// 統一超商寄貨便
	const HILIFE_C2C = 'HILIFEC2C';// 萊爾富富店到店
}

/**
 *  是否代收貨款
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class IsCollection {
	const YES = 'Y';// 貨到付款
	const NO = 'N';// 僅配送
}

/**
 *  使用設備
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class Device {
	const PC = 0;// PC
	const MOBILE = 1;// 行動裝置
}

/**
 *  測試廠商編號
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class ECPayTestMerchantID {
	const B2C = '2000132';// B2C
	const C2C = '2000933';// C2C
}

/**
 *  正式環境網址
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class ECPayURL {
	const CVS_MAP = 'https://logistics.ecpay.com.tw/Express/map';// 電子地圖
	const SHIPPING_ORDER = 'https://logistics.ecpay.com.tw/Express/Create';// 物流訂單建立
	const HOME_RETURN_ORDER = 'https://logistics.ecpay.com.tw/Express/ReturnHome';// 宅配逆物流訂單
	const UNIMART_RETURN_ORDER = 'https://logistics.ecpay.com.tw/express/ReturnUniMartCVS';// 超商取貨逆物流訂單(統一超商B2C)
	const HILIFE_RETURN_ORDER = 'https://logistics.ecpay.com.tw/express/ReturnHiLifeCVS';// 超商取貨逆物流訂單(萊爾富超商B2C)
	const FAMILY_RETURN_ORDER = 'https://logistics.ecpay.com.tw/express/ReturnCVS';// 超商取貨逆物流訂單(全家超商B2C)
	const FAMILY_RETURN_CHECK = 'https://logistics.ecpay.com.tw/Helper/LogisticsCheckAccoounts';// 全家逆物流核帳(全家超商B2C)
	const UNIMART_UPDATE_LOGISTICS_INFO = 'https://logistics.ecpay.com.tw/Helper/UpdateShipmentInfo';// 統一修改物流資訊(全家超商B2C)
	const UNIMART_UPDATE_STORE_INFO = 'https://logistics.ecpay.com.tw/Express/UpdateStoreInfo';// 更新門市(統一超商C2C)
	const UNIMART_CANCEL_LOGISTICS_ORDER = 'https://logistics.ecpay.com.tw/Express/CancelC2COrder';// 取消訂單(統一超商C2C)
	const QUERY_LOGISTICS_INFO = 'https://logistics.ecpay.com.tw/Helper/QueryLogisticsTradeInfo/V2';// 物流訂單查詢
	const PRINT_TRADE_DOC = 'https://logistics.ecpay.com.tw/helper/printTradeDocument';// 產生托運單(宅配)/一段標(超商取貨)
	const PRINT_UNIMART_C2C_BILL = 'https://logistics.ecpay.com.tw/Express/PrintUniMartC2COrderInfo';// 列印繳款單(統一超商C2C)
	const PRINT_FAMILY_C2C_BILL = 'https://logistics.ecpay.com.tw/Express/PrintFAMIC2COrderInfo';// 全家列印小白單(全家超商C2C)
	const Print_HILIFE_C2C_BILL = 'https://logistics.ecpay.com.tw/Express/PrintHILIFEC2COrderInfo';// 萊爾富列印小白單(萊爾富超商C2C)
	const CREATE_TEST_DATA = 'https://logistics.ecpay.com.tw/Express/CreateTestData';// 產生 B2C 測標資料
}

/**
 *  測試環境網址
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class ECPayTestURL {
const CVS_MAP = 'https://logistics-stage.ecpay.com.tw/Express/map';// 電子地圖
	const SHIPPING_ORDER = 'https://logistics-stage.ecpay.com.tw/Express/Create';// 物流訂單建立
	const HOME_RETURN_ORDER = 'https://logistics-stage.ecpay.com.tw/Express/ReturnHome';// 宅配逆物流訂單
	const UNIMART_RETURN_ORDER = 'https://logistics-stage.ecpay.com.tw/express/ReturnUniMartCVS';// 超商取貨逆物流訂單(統一超商B2C)
	const HILIFE_RETURN_ORDER = 'https://logistics-stage.ecpay.com.tw/express/ReturnHiLifeCVS';// 超商取貨逆物流訂單(萊爾富超商B2C)
	const FAMILY_RETURN_ORDER = 'https://logistics-stage.ecpay.com.tw/express/ReturnCVS';// 超商取貨逆物流訂單(全家超商B2C)
	const FAMILY_RETURN_CHECK = 'https://logistics-stage.ecpay.com.tw/Helper/LogisticsCheckAccoounts';// 全家逆物流核帳(全家超商B2C)
	const UNIMART_UPDATE_LOGISTICS_INFO = 'https://logistics-stage.ecpay.com.tw/Helper/UpdateShipmentInfo';// 統一修改物流資訊(全家超商B2C)
	const UNIMART_UPDATE_STORE_INFO = 'https://logistics-stage.ecpay.com.tw/Express/UpdateStoreInfo';// 更新門市(統一超商C2C)
	const UNIMART_CANCEL_LOGISTICS_ORDER = 'https://logistics-stage.ecpay.com.tw/Express/CancelC2COrder';// 取消訂單(統一超商C2C)
	const QUERY_LOGISTICS_INFO = 'https://logistics-stage.ecpay.com.tw/Helper/QueryLogisticsTradeInfo/V2';// 物流訂單查詢
	const PRINT_TRADE_DOC = 'https://logistics-stage.ecpay.com.tw/helper/printTradeDocument';// 產生托運單(宅配)/一段標(超商取貨)
	const PRINT_UNIMART_C2C_BILL = 'https://logistics-stage.ecpay.com.tw/Express/PrintUniMartC2COrderInfo';// 列印繳款單(統一超商C2C)
	const PRINT_FAMILY_C2C_BILL = 'https://logistics-stage.ecpay.com.tw/Express/PrintFAMIC2COrderInfo';// 全家列印小白單(全家超商C2C)
	const Print_HILIFE_C2C_BILL = 'https://logistics-stage.ecpay.com.tw/Express/PrintHILIFEC2COrderInfo';// 萊爾富列印小白單(萊爾富超商C2C)
	const CREATE_TEST_DATA = 'https://logistics-stage.ecpay.com.tw/Express/CreateTestData';// 產生 B2C 測標資料
}

/**
 *  溫層
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class Temperature {
	const ROOM = '0001';// 常溫
	const REFRIGERATION = '0002';// 冷藏
	const FREEZE = '0003';// 冷凍
}

/**
 *  距離
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class Distance {
	const SAME = '00';// 同縣市
	const OTHER = '01';// 外縣市
	const ISLAND = '02';// 離島
}

/**
 *  規格
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class Specification {
	const CM_60 = '0001';// 60cm
	const CM_90 = '0002';// 90cm
	const CM_120 = '0003';// 120cm
	const CM_150 = '0004';// 150cm
}

/**
 *  預計取件時段
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class ScheduledPickupTime {
	const TIME_9_12 = '1';// 9~12時
	const TIME_12_17 = '2';// 12~17時
	const TIME_17_20 = '3';// 17~20時
	const UNLIMITED = '4';// 不限時
}

/**
 *  預定送達時段
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class ScheduledDeliveryTime {
	const TIME_9_12 = '1';// 9~12時
	const TIME_12_17 = '2';// 12~17時
	const TIME_17_20 = '3';// 17~20時
	const UNLIMITED = '4';// 不限時
	const TIME_20_21 = '5';// 20~21時(需限定區域)
	const TIME_9_17 = '12';// 早午 9~17
	const TIME_9_12_17_20 = '13';// 早晚 9~12 & 17~20
	const TIME_13_20 = '23';// 午晚 13~20
}

/**
 *  門市類型
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
abstract class StoreType {
	const RECIVE_STORE = '01';// 取件門市
	const RETURN_STORE = '02';// 退件門市
}



/**
 *  物流 SDK
 *
 * @author		https://www.ecpay.com.tw
 * @category	Options
 * @version		1.0.1012
 */
class ECPayLogistics {
	public $ServiceURL = '';
	public $HashKey = '';
	public $HashIV = '';
	public $Send = array();
	public $SendExtend = '';
	public $PostParams = array();
	public $Encode = 'UTF-8';
	
	public function __construct() {}
	
	/**
	 *  電子地圖
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @param		String $ButtonDesc 按鈕顯示名稱
	 * @param		String $Target 表單 action 目標
	 * @return		String
	 * @version		1.0.1012
	 */
	public function CvsMap($ButtonDesc = '電子地圖', $Target = '_self') {
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'MerchantTradeNo' => '',
			'LogisticsSubType' => '',
			'IsCollection' => '',
			'ServerReplyURL' => '',
			'ExtraData' => '',
			'Device' => Device::PC
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		$this->PostParams['LogisticsType'] = LogisticsType::CVS;
		
		// 參數檢查
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('CVS_MAP');
		$this->ValidateLogisticsSubType();
		$this->ValidateIsCollection();
		$this->ValidateURL('ServerReplyURL', $this->PostParams['ServerReplyURL']);
		$this->ValidateString('ExtraData', $this->PostParams['ExtraData'], 20, true);
		$this->ValidateDevice(true);
		
		return $this->GenPostHTML($ButtonDesc, $Target);
	}
	
	/**
	 *  物流訂單建立
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @param		String $ButtonDesc 按鈕顯示名稱
	 * @param		String $Target 表單 action 目標
	 * @return		String
	 * @version		1.0.1012
	 */
	public function CreateShippingOrder($ButtonDesc = '物流訂單建立', $Target = '_self') {
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'MerchantTradeNo' => '',
			'MerchantTradeDate' => '',
			'LogisticsType' => '',
			'LogisticsSubType' => '',
			'GoodsAmount' => 0,
			'CollectionAmount' => 0,
			'IsCollection' => IsCollection::NO,
			'GoodsName' => '',
			'SenderName' => '',
			'SenderPhone' => '',
			'SenderCellPhone' => '',
			'ReceiverName' => '',
			'ReceiverPhone' => '',
			'ReceiverCellPhone' => '',
			'ReceiverEmail' => '',
			'TradeDesc' => '',
			'ServerReplyURL' => '',
			'ClientReplyURL' => '',
			'LogisticsC2CReplyURL' => '',
			'Remark' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		$MinAmount = 1; // 金額下限
		$MaxAmount = 20000; // 金額上限
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('SHIPPING_ORDER');
		$this->ValidateMerchantTradeDate();
		$this->ValidateLogisticsType();
		$this->ValidateLogisticsSubType();
		
		// 依不同的物流類型(LogisticsType)設定專屬參數並檢查
		switch ($this->PostParams['LogisticsType']) {
			case LogisticsType::CVS:
				$CvsParamList = array(
					'ReceiverStoreID' => '',
					'ReturnStoreID' => ''
				);
				$this->PostParams = $this->GetPostParams($this->SendExtend, $CvsParamList, $this->PostParams);
			
				$this->ValidateMixTypeID('ReceiverStoreID', $this->PostParams['ReceiverStoreID'], 6);
				$this->ValidateMixTypeID('ReturnStoreID', $this->PostParams['ReturnStoreID'], 6, true);
				break;
			case LogisticsType::HOME:
				$HomeParamList = array(
					'SenderZipCode' => '',
					'SenderAddress' => '',
					'ReceiverZipCode' => '',
					'ReceiverAddress' => '',
					'Temperature' => Temperature::ROOM,
					'Distance' => Distance::SAME,
					'Specification' => Specification::CM_60,
					'ScheduledDeliveryTime' => '',
					'ScheduledDeliveryDate' => '',
					'PackageCount' => 0
				);
				$this->PostParams = $this->GetPostParams($this->SendExtend, $HomeParamList, $this->PostParams);
				$this->PostParams['ScheduledPickupTime'] = ScheduledPickupTime::UNLIMITED;
				
				$this->ValidateZipCode('SenderZipCode', $this->PostParams['SenderZipCode']);
				$this->ValidateAddress('SenderAddress', $this->PostParams['SenderAddress'], 6, 60);
				$this->ValidateZipCode('ReceiverZipCode', $this->PostParams['ReceiverZipCode']);
				$this->ValidateAddress('ReceiverAddress', $this->PostParams['ReceiverAddress'], 6, 60);
				$this->ValidateTemperature();
				$this->ValidateDistance();
				$this->ValidateSpecification();
				$this->ValidateScheduledDeliveryTime(true);
				break;
			default:
		}
		
		$this->ValidateAmount('GoodsAmount', $this->PostParams['GoodsAmount']);
		if ($this->PostParams['GoodsAmount'] < $MinAmount or $this->PostParams['GoodsAmount'] > $MaxAmount){
			throw new Exception('Invalid GoodsAmount.');
		}
		
		$this->ValidateIsCollection(true);
		if ($this->PostParams['IsCollection'] == IsCollection::NO) {
			// 若設定為僅配送，清除代收金額
			unset($this->PostParams['CollectionAmount']);
		} else {
			$this->ValidateAmount('CollectionAmount', $this->PostParams['CollectionAmount']);
			if ($this->PostParams['CollectionAmount'] < $MinAmount or $this->PostParams['CollectionAmount'] > $MaxAmount){
				throw new Exception('Invalid CollectionAmount.');
			}
		}
		
		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::HILIFE_C2C or $this->PostParams['LogisticsSubType'] == LogisticsSubType::UNIMART_C2C) {
			// 物流子類型(LogisticsSubType)為萊爾富店到店(HILIFEC2C)、 統一超商交貨便(UNIMARTC2C)時，不可為空
			$this->ValidateString('GoodsName', $this->PostParams['GoodsName'], 60);
		} else {
			$this->ValidateString('GoodsName', $this->PostParams['GoodsName'], 60, true);
		}

		$this->ValidateGoodsName('GoodsName', $this->PostParams['GoodsName'], true);
		
		
		$this->ValidateString('SenderName', $this->PostParams['SenderName'], 10);
		$this->ValidateSenderName('SenderName', $this->PostParams['SenderName'], true);

		$this->ValidatePhoneNumber('SenderPhone', $this->PostParams['SenderPhone'], true);
		$this->ValidateCellphoneNumber('SenderCellPhone', $this->PostParams['SenderCellPhone'], true);
		if ($this->PostParams['LogisticsType'] == LogisticsType::HOME) {
			// 物流類型(LogisticsType)為宅配(Home)時，寄件人電話(SenderPhone)或寄件人手機(SenderCellPhone)不可為空
			if (empty($this->PostParams['SenderPhone']) and empty($this->PostParams['SenderCellPhone'])) {
				throw new Exception('SenderPhone or SenderCellPhone is required when LogisticsType is Home.');
			}
		} else if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::HILIFE_C2C or $this->PostParams['LogisticsSubType'] == LogisticsSubType::UNIMART_C2C) {
			// 物流子類型(LogisticsSubType)為統一超商交貨便(UNIMARTC2C)、萊爾富店到店(HILIFEC2C)時，寄件人手機(SenderCellPhone)不可為空
			if (empty($this->PostParams['SenderCellPhone'])) {
				throw new Exception('SenderCellPhone is required when LogisticsSubType is UNIMARTC2C or HILIFEC2C.');
			}
		}
		
		$this->ValidateString('ReceiverName', $this->PostParams['ReceiverName'], 10);
		$this->ValidateReceiverName('ReceiverName', $this->PostParams['ReceiverName'], true);

		$this->ValidatePhoneNumber('ReceiverPhone', $this->PostParams['ReceiverPhone'], true);
		$this->ValidateCellphoneNumber('ReceiverCellPhone', $this->PostParams['ReceiverCellPhone'], true);
		if ($this->PostParams['LogisticsType'] == LogisticsType::HOME) {
			// 物流類型(LogisticsType)為宅配(Home)時，收件人電話(ReceiverPhone)或收件人手機(ReceiverCellPhone)不可為空
			if (empty($this->PostParams['ReceiverPhone']) and empty($this->PostParams['ReceiverCellPhone'])) {
				throw new Exception('ReceiverPhone or ReceiverCellPhone is required when LogisticsType is Home.');
			}
		} else {
			// 物流子類型(LogisticsSubType)為統一超商(UNIMART)、全家(FAMILY)、萊爾富(HILIFE)、統一超商交貨便(UNIMARTC2C)、全家超商店到店(FAMILYC2C)、萊爾富富店到店(HILIFEC2C)時，收件人手機(ReceiverCellPhone)不可為空
			if (empty($this->PostParams['ReceiverCellPhone'])) {
				throw new Exception('ReceiverCellPhone is required.');
			}
		}

		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::ECAN and $this->PostParams['Temperature'] !== Temperature::ROOM) {
			// 物流子類型為宅配通(ECAN)時，溫層(Temperature)只能用常溫(ROOM)
			throw new Exception('Temperature should be ROOM when LogisticsSubType is ECAN.');
		}

		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::ECAN and date('Ymd', strtotime($this->PostParams['ScheduledDeliveryDate'])) < date('Ymd', strtotime('+3 day'))) {
			// 指定送達日期為該訂單建立時間 + 3 天
			throw new Exception('ScheduledDeliveryDate should be the time that create order + 3 day.');
		}
		
		$this->ValidateEmail('ReceiverEmail', $this->PostParams['ReceiverEmail'], 50, true);
		$this->ValidateString('TradeDesc', $this->PostParams['TradeDesc'], 200, true);
		$this->ValidateURL('ServerReplyURL', $this->PostParams['ServerReplyURL']);
		$this->ValidateURL('ClientReplyURL', $this->PostParams['ClientReplyURL'], 200, true);
		
		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::UNIMART_C2C) {
			// 物流子類型(LogisticsSubType)為統一超商交貨便(UNIMARTC2C)時，此欄位不可為空
			$this->ValidateURL('LogisticsC2CReplyURL', $this->PostParams['LogisticsC2CReplyURL']);
		} else {
			$this->ValidateURL('LogisticsC2CReplyURL', $this->PostParams['LogisticsC2CReplyURL'], 200, true);
		}
		
		$this->ValidateString('Remark', $this->PostParams['Remark'], 200, true);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 物流類型(LogisticsType)為宅配(Home)且溫層(Temperature)為冷凍(0003)時，規格(Specification)不可為 150cm(0004)
		if ($this->PostParams['LogisticsType'] == LogisticsType::HOME and $this->PostParams['Temperature'] == Temperature::FREEZE) {
			if ($this->PostParams['Specification'] == Specification::CM_150) {
				throw new Exception('Specification could not be 150cm(0004) when LogisticsType is Home and Temperature is FREEZE(0003).');
			}
		}
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		return $this->GenPostHTML($ButtonDesc, $Target);
	}

	/**
	 *  幕後物流訂單建立
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function BGCreateShippingOrder() {
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'MerchantTradeNo' => '',
			'MerchantTradeDate' => '',
			'LogisticsType' => '',
			'LogisticsSubType' => '',
			'GoodsAmount' => 0,
			'CollectionAmount' => 0,
			'IsCollection' => IsCollection::NO,
			'GoodsName' => '',
			'SenderName' => '',
			'SenderPhone' => '',
			'SenderCellPhone' => '',
			'ReceiverName' => '',
			'ReceiverPhone' => '',
			'ReceiverCellPhone' => '',
			'ReceiverEmail' => '',
			'TradeDesc' => '',
			'ServerReplyURL' => '',
			'LogisticsC2CReplyURL' => '',
			'Remark' => '',
			'PlatformID' => ''
		);
    
		// 幕後物流訂單建立不可設定Client端回覆網址(ClientReplyURL)
		if (!empty($this->Send['ClientReplyURL'])) {
		throw new Exception('ClientReplyURL should be null.');
		}
    
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		$MinAmount = 1; // 金額下限
		$MaxAmount = 20000; // 金額上限
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('SHIPPING_ORDER');
		$this->ValidateMerchantTradeDate();
		$this->ValidateLogisticsType();
		$this->ValidateLogisticsSubType();
		
		// 依不同的物流類型(LogisticsType)設定專屬參數並檢查
		switch ($this->PostParams['LogisticsType']) {
			case LogisticsType::CVS:
				$CvsParamList = array(
					'ReceiverStoreID' => '',
					'ReturnStoreID' => ''
				);
				$this->PostParams = $this->GetPostParams($this->SendExtend, $CvsParamList, $this->PostParams);
			
				$this->ValidateMixTypeID('ReceiverStoreID', $this->PostParams['ReceiverStoreID'], 6);
				$this->ValidateMixTypeID('ReturnStoreID', $this->PostParams['ReturnStoreID'], 6, true);
				break;
			case LogisticsType::HOME:
				$HomeParamList = array(
					'SenderZipCode' => '',
					'SenderAddress' => '',
					'ReceiverZipCode' => '',
					'ReceiverAddress' => '',
					'Temperature' => Temperature::ROOM,
					'Distance' => Distance::SAME,
					'Specification' => Specification::CM_60,
					'ScheduledDeliveryTime' => '',
					'ScheduledDeliveryDate' => '',
					'PackageCount' => 0
				);
				$this->PostParams = $this->GetPostParams($this->SendExtend, $HomeParamList, $this->PostParams);
				$this->PostParams['ScheduledPickupTime'] = ScheduledPickupTime::UNLIMITED;
				
				$this->ValidateZipCode('SenderZipCode', $this->PostParams['SenderZipCode']);
				$this->ValidateAddress('SenderAddress', $this->PostParams['SenderAddress'], 6, 60);
				$this->ValidateZipCode('ReceiverZipCode', $this->PostParams['ReceiverZipCode']);
				$this->ValidateAddress('ReceiverAddress', $this->PostParams['ReceiverAddress'], 6, 60);
				$this->ValidateTemperature();
				$this->ValidateDistance();
				$this->ValidateSpecification();
				$this->ValidateScheduledDeliveryTime(true);
				break;
			default:
		}
		
		$this->ValidateAmount('GoodsAmount', $this->PostParams['GoodsAmount']);
		if ($this->PostParams['GoodsAmount'] < $MinAmount or $this->PostParams['GoodsAmount'] > $MaxAmount){
			throw new Exception('Invalid GoodsAmount.');
		}
		
		$this->ValidateIsCollection(true);
		if ($this->PostParams['IsCollection'] == IsCollection::NO) {
			// 若設定為僅配送，清除代收金額
			unset($this->PostParams['CollectionAmount']);
		} else {
			$this->ValidateAmount('CollectionAmount', $this->PostParams['CollectionAmount']);
			if ($this->PostParams['CollectionAmount'] < $MinAmount or $this->PostParams['CollectionAmount'] > $MaxAmount){
				throw new Exception('Invalid CollectionAmount.');
			}
		}
		
		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::HILIFE_C2C or $this->PostParams['LogisticsSubType'] == LogisticsSubType::UNIMART_C2C) {
			// 物流子類型(LogisticsSubType)為萊爾富店到店(HILIFEC2C)、 統一超商交貨便(UNIMARTC2C)時，不可為空
			$this->ValidateString('GoodsName', $this->PostParams['GoodsName'], 60);
		} else {
			$this->ValidateString('GoodsName', $this->PostParams['GoodsName'], 60, true);
		}

		$this->ValidateGoodsName('GoodsName', $this->PostParams['GoodsName'], true);
		
		$this->ValidateString('SenderName', $this->PostParams['SenderName'], 10);
		$this->ValidateSenderName('SenderName', $this->PostParams['SenderName'], true);

		$this->ValidatePhoneNumber('SenderPhone', $this->PostParams['SenderPhone'], true);
		$this->ValidateCellphoneNumber('SenderCellPhone', $this->PostParams['SenderCellPhone'], true);
		if ($this->PostParams['LogisticsType'] == LogisticsType::HOME) {
			// 物流類型(LogisticsType)為宅配(Home)時，寄件人電話(SenderPhone)或寄件人手機(SenderCellPhone)不可為空
			if (empty($this->PostParams['SenderPhone']) and empty($this->PostParams['SenderCellPhone'])) {
				throw new Exception('SenderPhone or SenderCellPhone is required when LogisticsType is Home.');
			}
		} else if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::HILIFE_C2C or $this->PostParams['LogisticsSubType'] == LogisticsSubType::UNIMART_C2C) {
			// 物流子類型(LogisticsSubType)為統一超商交貨便(UNIMARTC2C)、萊爾富店到店(HILIFEC2C)時，寄件人手機(SenderCellPhone)不可為空
			if (empty($this->PostParams['SenderCellPhone'])) {
				throw new Exception('SenderCellPhone is required when LogisticsSubType is UNIMARTC2C or HILIFEC2C.');
			}
		}
		
		$this->ValidateString('ReceiverName', $this->PostParams['ReceiverName'], 10);
		$this->ValidateReceiverName('ReceiverName', $this->PostParams['ReceiverName'], true);

		$this->ValidatePhoneNumber('ReceiverPhone', $this->PostParams['ReceiverPhone'], true);
		$this->ValidateCellphoneNumber('ReceiverCellPhone', $this->PostParams['ReceiverCellPhone'], true);
		if ($this->PostParams['LogisticsType'] == LogisticsType::HOME) {
			// 物流類型(LogisticsType)為宅配(Home)時，收件人電話(ReceiverPhone)或收件人手機(ReceiverCellPhone)不可為空
			if (empty($this->PostParams['ReceiverPhone']) and empty($this->PostParams['ReceiverCellPhone'])) {
				throw new Exception('ReceiverPhone or ReceiverCellPhone is required when LogisticsType is Home.');
			}
		} else {
			// 物流子類型(LogisticsSubType)為統一超商(UNIMART)、全家(FAMILY)、萊爾富(HILIFE)、統一超商交貨便(UNIMARTC2C)、全家超商店到店(FAMILYC2C)、萊爾富富店到店(HILIFEC2C)時，收件人手機(ReceiverCellPhone)不可為空
			if (empty($this->PostParams['ReceiverCellPhone'])) {
				throw new Exception('ReceiverCellPhone is required.');
			}
		}
		
		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::ECAN and $this->PostParams['Temperature'] !== Temperature::ROOM) {
			// 物流子類型為宅配通(ECAN)時，溫層(Temperature)只能用常溫(ROOM)
			throw new Exception('Temperature should be ROOM when LogisticsSubType is ECAN.');
		}

		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::ECAN and date('Ymd', strtotime($this->PostParams['ScheduledDeliveryDate'])) < date('Ymd', strtotime('+3 day'))) {
			// 指定送達日期為該訂單建立時間 + 3 天
			throw new Exception('ScheduledDeliveryDate should be the time that create order + 3 day.');
		}

		$this->ValidateEmail('ReceiverEmail', $this->PostParams['ReceiverEmail'], 50, true);
		$this->ValidateString('TradeDesc', $this->PostParams['TradeDesc'], 200, true);
		$this->ValidateURL('ServerReplyURL', $this->PostParams['ServerReplyURL']);
		
		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::UNIMART_C2C) {
			// 物流子類型(LogisticsSubType)為統一超商交貨便(UNIMARTC2C)時，此欄位不可為空
			$this->ValidateURL('LogisticsC2CReplyURL', $this->PostParams['LogisticsC2CReplyURL']);
		} else {
			$this->ValidateURL('LogisticsC2CReplyURL', $this->PostParams['LogisticsC2CReplyURL'], 200, true);
		}
		
		$this->ValidateString('Remark', $this->PostParams['Remark'], 200, true);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 物流類型(LogisticsType)為宅配(Home)且溫層(Temperature)為冷凍(0003)時，規格(Specification)不可為 150cm(0004)
		if ($this->PostParams['LogisticsType'] == LogisticsType::HOME and $this->PostParams['Temperature'] == Temperature::FREEZE) {
			if ($this->PostParams['Specification'] == Specification::CM_150) {
				throw new Exception('Specification could not be 0004 when LogisticsType is Home and Temperature is 0003.');
			}
		}
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);

		// urlencode
		foreach($this->PostParams as $key => $value) {
    		$this->PostParams[$key] = urlencode($value);
		}
		
		// 解析回傳結果
		// 正確：1|MerchantID=XXX&MerchantTradeNo=XXX&RtnCode=XXX&RtnMsg=XXX&AllPayLogisticsID=XXX&LogisticsType=XXX&LogisticsSubType=XXX&GoodsAmount=XXX&UpdateStatusDate=XXX&ReceiverName=XXX&ReceiverPhone=XXX&ReceiverCellPhone=XXX&ReceiverEmail=XXX&ReceiverAddress=XXX&CVSPaymentNo=XXX&CVSValidationNo=XXX &CheckMacValue=XXX
		// 錯誤：0|ErrorMessage
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		$Pieces = explode('|', $Feedback);
		$Result = array();
		$Result['ResCode'] = $Pieces[0];
		if ($Result['ResCode']) {
		$RtnCont = array();
		parse_str($Pieces[1], $RtnCont);
		$Result = array_merge($Result, $RtnCont);
		} else {
		$Result['ErrorMessage'] = $Pieces[1];
		}

		return $Result;
	}

	/**
	 *  回傳 CheckMacValue 檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @param		Array $Feedback ECPay 回傳資料
	 * @version		1.0.1012
	 */
	public function CheckOutFeedback($Feedback = array()) {
		
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		
		if (empty($Feedback)) {
			throw new Exception('Feedback is required.');
		}
		
		if (!isset($Feedback['CheckMacValue'])) {
			throw new Exception('Feedback CheckMacValue is required.');
		} else {
			$FeedbackCheckMacValue = $Feedback['CheckMacValue'];
			unset($Feedback['CheckMacValue']);
			unset($Feedback['ResCode']);
			unset($Feedback['ErrorMessage']);
			$CheckMacValue = ECPay_CheckMacValue::generate($Feedback, $this->HashKey, $this->HashIV);
			if ($CheckMacValue != $FeedbackCheckMacValue) {
				throw new Exception('CheckMacValue verify fail.');
			}
		}
	}

	/**
	 *  宅配逆物流訂單產生
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function CreateHomeReturnOrder() {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',			
			'AllPayLogisticsID' => '',
			'LogisticsSubType' => '',
			'ServerReplyURL' => '',
			'SenderName' => '',
			'SenderPhone' => '',
			'SenderCellPhone' => '',
			'SenderZipCode' => '',
			'SenderAddress' => '',
			'ReceiverName' => '',
			'ReceiverPhone' => '',
			'ReceiverCellPhone' => '',
			'ReceiverZipCode' => '',
			'ReceiverAddress' => '',
			'GoodsAmount' => '',
			'GoodsName' => '',
			'Temperature' => Temperature::ROOM,
			'Distance' => Distance::SAME,
			'Specification' => Specification::CM_60,
			'ScheduledPickupTime' => ScheduledPickupTime::UNLIMITED,
			'ScheduledDeliveryTime' => '',
			'ScheduledDeliveryDate' => '',
			'PackageCount' => 0,
			'Remark' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		$this->PostParams['ScheduledPickupTime'] = ScheduledPickupTime::UNLIMITED; // 預定取件時段(ScheduledPickupTime)固定為不限時
		$IsAllpayLogisticsIdEmpty = false; // 物流交易編號(AllPayLogisticsID)是否為空
		$IsAllowEmpty = true;
		$MinAmount = 1; // 金額下限
		$MaxAmount = 20000; // 金額上限
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('HOME_RETURN_ORDER');
		
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20, true);

		$this->ValidateLogisticsSubType(true);

		// 物流交易編號(AllPayLogisticsID)與物流子類型(LogisticsSubType)擇一不可為空
		if (empty($this->PostParams['AllPayLogisticsID'])) {
			$IsAllpayLogisticsIdEmpty = true;
		}
		if ($IsAllpayLogisticsIdEmpty === true and empty($this->PostParams['LogisticsSubType'])) {
			throw new Exception('One of AllPayLogisticsID and LogisticsSubType is required.');
		}
		
		$this->ValidateURL('ServerReplyURL', $this->PostParams['ServerReplyURL']);
		
		// 物流交易編號(AllPayLogisticsID)為空值時，退貨人姓名(SenderName)不可為空。
		if ($IsAllpayLogisticsIdEmpty) {
			$IsAllowEmpty = false;
		}
		$this->ValidateString('SenderName', $this->PostParams['SenderName'], 10, $IsAllowEmpty);
		
		$this->ValidatePhoneNumber('SenderPhone', $this->PostParams['SenderPhone'], true);
		$this->ValidateCellphoneNumber('SenderCellPhone', $this->PostParams['SenderCellPhone'], true);
		// 物流交易編號(AllPayLogisticsID)為空值時，退貨人電話(SenderPhone)與退貨人手機(SenderCellPhone)擇一不可為空。
		if ($IsAllpayLogisticsIdEmpty) {
			if (empty($this->PostParams['SenderPhone']) and empty($this->PostParams['SenderCellPhone'])) {
				throw new Exception('One of SenderPhone and SenderCellPhone is required.');
			}
		}
		
		// 物流交易編號(AllPayLogisticsID)為空值時，退貨人郵遞區號(SenderZipCode)不可為空。
		$this->ValidateZipCode('SenderZipCode', $this->PostParams['SenderZipCode'], $IsAllowEmpty);
		
		// 物流交易編號(AllPayLogisticsID)為空值時，SenderAddress(SenderAddress)不可為空。
		$this->ValidateAddress('SenderAddress', $this->PostParams['SenderAddress'], 6, 60, $IsAllowEmpty);
		
		// 若物流交易編號(AllPayLogisticsID)為空值時，收件人姓名(ReceiverName)不可為空。
		$this->ValidateString('ReceiverName', $this->PostParams['ReceiverName'], 10, $IsAllowEmpty);
		
		$this->ValidatePhoneNumber('ReceiverPhone', $this->PostParams['ReceiverPhone'], 20, true);
		$this->ValidateCellphoneNumber('ReceiverCellPhone', $this->PostParams['ReceiverCellPhone'], 20, true);
		// 物流交易編號(AllPayLogisticsID)為空值時，收件人電話(ReceiverPhone)與收件人手機(ReceiverCellPhone)擇一不可為空。
		if ($IsAllpayLogisticsIdEmpty) {
			if (empty($this->PostParams['ReceiverPhone']) and empty($this->PostParams['ReceiverCellPhone'])) {
				throw new Exception('One of ReceiverPhone and ReceiverCellPhone is required.');
			}
		}
		
		// 若物流交易編號(AllPayLogisticsID)為空值時，收件人郵遞區號(ReceiverZipCode)不可為空。
    		$this->ValidateZipCode('ReceiverZipCode', $this->PostParams['ReceiverZipCode'], $IsAllowEmpty);
		
		// 若物流交易編號(AllPayLogisticsID)為空值時，收件人地址(ReceiverAddress)不可為空。
		$this->ValidateAddress('ReceiverAddress', $this->PostParams['ReceiverAddress'], 6, 60, $IsAllowEmpty);

		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::ECAN and $this->PostParams['Temperature'] !== Temperature::ROOM) {
			// 物流子類型為宅配通(ECAN)時，溫層(Temperature)只能用常溫(ROOM)
			throw new Exception('Temperature should be ROOM when LogisticsSubType is ECAN.');
		}

		if ($this->PostParams['LogisticsSubType'] == LogisticsSubType::ECAN and date('Ymd', strtotime($this->PostParams['ScheduledDeliveryDate'])) < date('Ymd', strtotime('+3 day'))) {
			// 指定送達日期為該訂單建立時間 + 3 天
			throw new Exception('ScheduledDeliveryDate should be the time that create order + 3 day.');
		}

		$this->ValidateAmount('GoodsAmount', $this->PostParams['GoodsAmount']);
		if ($this->PostParams['GoodsAmount'] < $MinAmount or $this->PostParams['GoodsAmount'] > $MaxAmount){
			throw new Exception('Invalid GoodsAmount.');
		}			
		$this->ValidateString('GoodsName', $this->PostParams['GoodsName'], 60, true);
		$this->ValidateTemperature();
		$this->ValidateDistance();
		$this->ValidateSpecification();
		$this->ValidateScheduledDeliveryTime(true);
		$this->ValidateString('Remark', $this->PostParams['Remark'], 200, true);
		
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		// 解析回傳結果
		// 正確：1|OK
		// 錯誤：0|ErrorMessage
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		$Result = $this->ParseFeedback($Feedback);
		
		return $Result;
	}
	
	/**
	 *  超商取貨逆物流訂單(統一超商B2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function CreateUnimartB2CReturnOrder() {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'ServerReplyURL' => '',
			'GoodsName' => '',
			'GoodsAmount' => 0,
			'SenderName' => '',
			'SenderPhone' => '',
			'Remark' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		$this->PostParams['CollectionAmount'] = 0;
		$this->PostParams['ServiceType'] = 4;// 退貨不付款
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('UNIMART_RETURN_ORDER');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20, true);
		$this->ValidateURL('ServerReplyURL', $this->PostParams['ServerReplyURL']);
		$this->ValidateString('GoodsName', $this->PostParams['GoodsName'], 60, true);
		$this->ValidateAmount('GoodsAmount', $this->PostParams['GoodsAmount']);
		
		$this->ValidateString('SenderName', $this->PostParams['SenderName'], 50);
		$this->ValidateSenderName('SenderName', $this->PostParams['SenderName'], true);

		$this->ValidatePhoneNumber('SenderPhone', $this->PostParams['SenderPhone'], true);
		$this->ValidateString('Remark', $this->PostParams['Remark'], 20, true);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);

		$MinAmount = 1; // 金額下限
		$MaxAmount = 20000; // 金額上限
		if ($this->PostParams['GoodsAmount'] < $MinAmount or $this->PostParams['GoodsAmount'] > $MaxAmount){
			throw new Exception('Invalid GoodsAmount.');
		}	
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		// 解析回傳結果
		// 正確：RtnMerchantTradeNo | RtnOrderNo
		// 錯誤：|ErrorMessage
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		$Pieces = explode('|', $Feedback);
		$Result = array('RtnMerchantTradeNo' => '', 'RtnOrderNo' => '');
		if (empty($Pieces[0])) {
			$Result = array('ErrorMessage' => $Pieces[1]);
		} else {
			$Result['RtnMerchantTradeNo'] = $Pieces[0];
			$Result['RtnOrderNo'] = $Pieces[1];
		}
		
		return $Result;
	}

	/**
	 *  超商取貨逆物流訂單(萊爾富超商B2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function CreateHiLifeB2CReturnOrder() {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'ServerReplyURL' => '',
			'GoodsName' => '',
			'GoodsAmount' => 0,
			'SenderName' => '',
			'SenderPhone' => '',
			'Remark' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		$this->PostParams['CollectionAmount'] = 0;
		$this->PostParams['ServiceType'] = 4;// 退貨不付款
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('HILIFE_RETURN_ORDER');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20, true);
		$this->ValidateURL('ServerReplyURL', $this->PostParams['ServerReplyURL']);
		$this->ValidateString('GoodsName', $this->PostParams['GoodsName'], 60, true);
		$this->ValidateAmount('GoodsAmount', $this->PostParams['GoodsAmount']);
		$this->ValidateString('SenderName', $this->PostParams['SenderName'], 50);
		$this->ValidatePhoneNumber('SenderPhone', $this->PostParams['SenderPhone'], true);
		$this->ValidateString('Remark', $this->PostParams['Remark'], 20, true);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);

		$MinAmount = 1; // 金額下限
		$MaxAmount = 20000; // 金額上限
		if ($this->PostParams['GoodsAmount'] < $MinAmount or $this->PostParams['GoodsAmount'] > $MaxAmount){
			throw new Exception('Invalid GoodsAmount.');
		}	
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		// 解析回傳結果
		// 正確：RtnMerchantTradeNo | RtnOrderNo
		// 錯誤：|ErrorMessage
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		$Pieces = explode('|', $Feedback);
		$Result = array('RtnMerchantTradeNo' => '', 'RtnOrderNo' => '');
		if (empty($Pieces[0])) {
			$Result = array('ErrorMessage' => $Pieces[1]);
		} else {
			$Result['RtnMerchantTradeNo'] = $Pieces[0];
			$Result['RtnOrderNo'] = $Pieces[1];
		}
		
		return $Result;
	}

	/**
	 *  超商取貨逆物流訂單(全家超商B2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function CreateFamilyB2CReturnOrder() {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'ServerReplyURL' => '',
			'GoodsName' => '',
			'GoodsAmount' => 0,
			'SenderName' => '',
			'SenderPhone' => '',
			'Remark' => '',
			'Quantity' => '',
			'Cost' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		$this->PostParams['CollectionAmount'] = 0;
		$this->PostParams['ServiceType'] = 4;// 退貨不付款
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('FAMILY_RETURN_ORDER');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20, true);
		$this->ValidateURL('ServerReplyURL', $this->PostParams['ServerReplyURL']);
		$this->ValidateString('GoodsName', $this->PostParams['GoodsName'], 50, true);
		$this->ValidateAmount('GoodsAmount', $this->PostParams['GoodsAmount']);
		$this->ValidateString('SenderName', $this->PostParams['SenderName'], 50);
		$this->ValidatePhoneNumber('SenderPhone', $this->PostParams['SenderPhone'], true);
		$this->ValidateString('Remark', $this->PostParams['Remark'], 20, true);
		$this->ValidateString('Quantity', $this->PostParams['Quantity'], 50, true);
		$this->ValidateString('Cost', $this->PostParams['Cost'], 50, true);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 檢查商品名稱, 數量 與 成本
		$GoodsNameNumber = count(explode('#', $this->PostParams['GoodsName']));
		$QuantityNumber = count(explode('#', $this->PostParams['Quantity']));
		$CostNumber = count(explode('#', $this->PostParams['Cost']));
		
		if (!empty($this->PostParams['GoodsName']) and !empty($this->PostParams['Quantity'])) {
			if ($GoodsNameNumber != $QuantityNumber) {
				throw new Exception('GoodsName number and Quantity number do not match.');
			}
		}
		
		if (!empty($this->PostParams['Quantity']) and !empty($this->PostParams['Cost'])) {
			if ($GoodsNameNumber != $CostNumber) {
				throw new Exception('Quantity number and Cost number do not match.');
			}
		}
		
		if (!empty($this->PostParams['Cost']) and !empty($this->PostParams['GoodsName'])) {
			if ($GoodsNameNumber != $CostNumber) {
				throw new Exception('Cost number and GoodsName number do not match.');
			}
		}

		$MinAmount = 1; // 金額下限
		$MaxAmount = 20000; // 金額上限
		if ($this->PostParams['GoodsAmount'] < $MinAmount or $this->PostParams['GoodsAmount'] > $MaxAmount){
			throw new Exception('Invalid GoodsAmount.');
		}	
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		// 解析回傳結果
		// 正確：RtnMerchantTradeNo | RtnOrderNo
		// 錯誤：|ErrorMessage
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		$Pieces = explode('|', $Feedback);
		$Result = array('RtnMerchantTradeNo' => '', 'RtnOrderNo' => '');
		if (empty($Pieces[0])) {
			$Result = array('ErrorMessage' => $Pieces[1]);
		} else {
			$Result['RtnMerchantTradeNo'] = $Pieces[0];
			$Result['RtnOrderNo'] = $Pieces[1];
		}
		
		return $Result;
	}
	
	/**
	 *  全家逆物流核帳(全家超商B2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function CheckFamilyB2CLogistics() {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'RtnMerchantTradeNo' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('FAMILY_RETURN_CHECK');
		$this->ValidateID('RtnMerchantTradeNo', $this->PostParams['RtnMerchantTradeNo'], 13);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		// 解析回傳結果
		// 正確：1|OK
		// 錯誤：0|ErrorMessage
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		$Result = $this->ParseFeedback($Feedback);
		
		return $Result;
	}

	/**
	 *  廠商修改出貨日期、取貨門市(統一超商B2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function UpdateUnimartLogisticsInfo() {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'ShipmentDate' => '',
			'ReceiverStoreID' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('UNIMART_UPDATE_LOGISTICS_INFO');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20);
		
		$this->ValidateShipmentDate(true);
		$this->ValidateMixTypeID('ReceiverStoreID', $this->PostParams['ReceiverStoreID'], 6, true);
		if (empty($this->PostParams['ShipmentDate']) and empty($this->PostParams['ReceiverStoreID'])) {
			throw new Exception('ShipmentDate or ReceiverStoreID is required.');
		}
		
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		// 解析回傳結果
		// 正確：1|OK
		// 錯誤：0|ErrorMessage
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		$Result = $this->ParseFeedback($Feedback);
		
		return $Result;
	}

	/**
	 *  更新門市(統一超商C2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function UpdateUnimartStore() {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'CVSPaymentNo' => '',
			'CVSValidationNo' => '',
			'StoreType' => '',
			'ReceiverStoreID' => '',
			'ReturnStoreID' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('UNIMART_UPDATE_STORE_INFO');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20);
		$this->ValidateMixTypeID('CVSPaymentNo', $this->PostParams['CVSPaymentNo'], 15);
		$this->ValidateID('CVSValidationNo', $this->PostParams['CVSValidationNo'], 10);
		$this->ValidateStoreType();
		
		if ($this->PostParams['StoreType'] == StoreType::RECIVE_STORE) {
			$this->ValidateMixTypeID('ReceiverStoreID', $this->PostParams['ReceiverStoreID'], 6);
		} else {
			unset($this->PostParams['ReceiverStoreID']);
		}
	
		if ($this->PostParams['StoreType'] == StoreType::RETURN_STORE) {
			$this->ValidateMixTypeID('ReturnStoreID', $this->PostParams['ReturnStoreID'], 6);
		} else {
			unset($this->PostParams['ReturnStoreID']);
		}
		
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		// 解析回傳結果
		// 正確：1|OK
		// 錯誤：0|ErrorMessage
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		$Result = $this->ParseFeedback($Feedback);
		
		return $Result;
	}
	
	/**
	 *  取消訂單(統一超商C2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function CancelUnimartLogisticsOrder() {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'CVSPaymentNo' => '',
			'CVSValidationNo' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('UNIMART_CANCEL_LOGISTICS_ORDER');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20);
		$this->ValidateMixTypeID('CVSPaymentNo', $this->PostParams['CVSPaymentNo'], 15);
		$this->ValidateID('CVSValidationNo', $this->PostParams['CVSValidationNo'], 10);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		// 解析回傳結果
		// 正確：1|OK
		// 錯誤：0|ErrorMessage
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		$Result = $this->ParseFeedback($Feedback);
		
		return $Result;
	}
	
	/**
	 *  物流訂單查詢
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @return		Array
	 * @version		1.0.1012
	 */
	public function QueryLogisticsInfo() {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		$this->PostParams['TimeStamp'] = strtotime('now');
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('QUERY_LOGISTICS_INFO');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		// 解析回傳結果
    		// 回應訊息：MerchantID=XXX&MerchantTradeNo=XXX&AllPayLogisticsID=XXX&GoodsAmount=XXX&LogisticsType=XXX&HandlingCharge=XXX&TradeDate=XXX&LogisticsStatus=XXX&GoodsName=XXX &CheckMacValue=XXX
		$Result = array();
		$Feedback = ECPay_IO::ServerPost($this->PostParams, $this->ServiceURL);
		parse_str($Feedback, $Result);
		
		return $Result;
	}
	
	/**
	 *  產生托運單(宅配)/一段標(超商取貨)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @param		String $ButtonDesc 按鈕顯示名稱
	 * @param		String $Target 表單 action 目標
	 * @return		String
	 * @version		1.0.1012
	 */
	public function PrintTradeDoc($ButtonDesc = '產生托運單/一段標', $Target = '_blank') {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('PRINT_TRADE_DOC');
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		return $this->GenPostHTML($ButtonDesc, $Target);
	}
	
	/**
	 *  列印繳款單(統一超商C2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @param		String $ButtonDesc 按鈕顯示名稱
	 * @param		String $Target 表單 action 目標
	 * @return		String
	 * @version		1.0.1012
	 */
	public function PrintUnimartC2CBill($ButtonDesc = '列印繳款單(統一超商C2C)', $Target = '_blank') {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'CVSPaymentNo' => '',
			'CVSValidationNo' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('PRINT_UNIMART_C2C_BILL');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20);
		$this->ValidateMixTypeID('CVSPaymentNo', $this->PostParams['CVSPaymentNo'], 15);
		$this->ValidateID('CVSValidationNo', $this->PostParams['CVSValidationNo'], 10);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		return $this->GenPostHTML($ButtonDesc, $Target);
	}

	/**
	 *  全家列印小白單(全家超商C2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @param		String $ButtonDesc 按鈕顯示名稱
	 * @param		String $Target 表單 action 目標
	 * @return		String
	 * @version		1.0.1012
	 */
	public function PrintFamilyC2CBill($ButtonDesc = '全家列印小白單(全家超商C2C)', $Target = '_blank') {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'CVSPaymentNo' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('PRINT_FAMILY_C2C_BILL');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20);
		$this->ValidateMixTypeID('CVSPaymentNo', $this->PostParams['CVSPaymentNo'], 15);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		return $this->GenPostHTML($ButtonDesc, $Target);
	}
	
	/**
	 *  萊爾富列印小白單(萊爾富超商C2C)
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @param		String $ButtonDesc 按鈕顯示名稱
	 * @param		String $Target 表單 action 目標
	 * @return		String
	 * @version		1.0.1012
	 */
	public function PrintHiLifeC2CBill($ButtonDesc = '萊爾富列印小白單(萊爾富超商C2C)', $Target = '_blank') {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'AllPayLogisticsID' => '',
			'CVSPaymentNo' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('Print_HILIFE_C2C_BILL');
		$this->ValidateID('AllPayLogisticsID', $this->PostParams['AllPayLogisticsID'], 20);
		$this->ValidateMixTypeID('CVSPaymentNo', $this->PostParams['CVSPaymentNo'], 15);
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		return $this->GenPostHTML($ButtonDesc, $Target);
	}
	
	/**
	 *  產生 B2C 測標資料
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category	SDK
	 * @param		String $ButtonDesc 按鈕顯示名稱
	 * @param		String $Target 表單 action 目標
	 * @return		String
	 * @version		1.0.1012
	 */
	public function CreateTestData($ButtonDesc = '產生 B2C 測標資料', $Target = '_blank') {
		
		// 參數初始化
		$ParamList = array(
			'MerchantID' => '',
			'ClientReplyURL' => '',
			'LogisticsSubType' => '',
			'PlatformID' => ''
		);
		$this->PostParams = $this->GetPostParams($this->Send, $ParamList);
		
		// 參數檢查
		$this->ValidateHashKey();
		$this->ValidateHashIV();
		$this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
		$this->ServiceURL = $this->GetURL('CREATE_TEST_DATA');
		$this->ValidateLogisticsSubType();
		$this->ValidateID('PlatformID', $this->PostParams['PlatformID'], 10, true);
		
		// 產生 CheckMacValue
		$this->PostParams['CheckMacValue'] = ECPay_CheckMacValue::generate($this->PostParams, $this->HashKey, $this->HashIV);
		
		return $this->GenPostHTML($ButtonDesc, $Target);
	}
	
	/**
	 *  Hash Key 檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @version		1.0.1012
	 */
	private function ValidateHashKey(){
		$Name = 'HashKey'; // 參數名稱
		$Value = $this->HashKey; // 參數內容
		$AllowEmpty = false; // 是否允許空值
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		}
	}

	/**
	 *  Hash IV 檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @version		1.0.1012
	 */
	private function ValidateHashIV(){
		$Name = 'HashIV'; // 參數名稱
		$Value = $this->HashKey; // 參數內容
		$AllowEmpty = false; // 是否允許空值
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		}
	}

	/**
	 *  ID 檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Integer	$MaxLength	參數最大長度
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateID($Name, $Value, $MaxLength = 1, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/^\d{1,' . $MaxLength . '}$/', $Value);
		}
	}
	
	/**
	 *  URL 檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Integer	$MaxLength	參數最大長度
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateURL($Name, $Value, $MaxLength = 200, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/^(http|https):\/\/+/', $Value);
			
			// 長度檢查
			$this->IsOverLength($Name, $this->StringLength($Value, $this->Encode), $MaxLength);
		}
	}
	
	/**
	 *  字串檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Integer	$MaxLength	參數最大長度
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateString($Name, $Value, $MaxLength = 1, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 長度檢查
			$this->IsOverLength($Name, $this->StringLength($Value, $this->Encode), $MaxLength);
		}
	}
	
	/**
	 *  金額檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Integer	$MaxLength	參數最大長度
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateAmount($Name, $Value, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 資料型態檢查
			$this->IsInteger($Name, $Value);
			
			// 格式檢查
			$this->IsValidFormat($Name, '/^\d+$/', $Value);
		}
	}
	
	/**
	 *  電話號碼檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidatePhoneNumber($Name, $Value, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/^\(?\d{2}\)?\-?\d{2,6}\-?\d{2,6}(#\d{1,6}){0,1}$/', $Value);
		}
	}

	/**
	 *  手機號碼檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateCellphoneNumber($Name, $Value, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/^09\d{8}$/', $Value);
		}
	}
	
	/**
	 *  電子郵件檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Integer	$MaxLength	參數最大長度
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateEmail($Name, $Value, $MaxLength = 100, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 長度檢查
			$this->IsOverLength($Name, $this->StringLength($Value, $this->Encode), $MaxLength);
			
			// 格式檢查
			if (!filter_var($Value, FILTER_VALIDATE_EMAIL)) {
				throw new Exception('Invalid ' . $Name . '.');
			}
		}
	}
	
	/**
	 *  郵遞區號檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateZipCode($Name, $Value, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/^\d{3,5}$/', $Value);
		}
	}
	
	/**
	 *  地址檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Integer	$MinLength	參數最小限制長度
	 * @param		Integer	$MaxLength	參數最大限制長度
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateAddress($Name, $Value, $MinLength = 1, $MaxLength = 1, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 長度檢查

			if ($MinLength) {
				// 最小長度限制
				$this->IsBelowLength($Name, $this->StringLength($Value, $this->Encode), $MinLength);
			}

			if ($MaxLength) {
				// 最大長度限制
				$this->IsOverLength($Name, $this->StringLength($Value, $this->Encode), $MaxLength);
			}
		}
	}

	/**
	 *  混合型態 ID 檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateMixTypeID($Name, $Value, $MaxLength = 1, $AllowEmpty = false) {
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/^[0-9a-zA-Z]{1,' . $MaxLength . '}$/', $Value);
		}
	}
	
	
	
	/**
	 *  門市類型檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @version		1.0.1012
	 */
	private function ValidateStoreType() {
		$Name = 'StoreType'; // 參數名稱
		$Value = $this->PostParams['StoreType']; // 參數內容
		$ClassName = 'StoreType'; // 合法資料 Class 名稱
		$AllowEmpty = false; // 是否允許空值
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 內容檢查
			$this->IsLegalValue($Name, $ClassName, $Value);
		}
	}

	/**
	 *  廠商交易編號檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @version		1.0.1012
	 */
	private function ValidateMerchantTradeNo() {
		$Name = 'MerchantTradeNo'; // 參數名稱
		$Value = $this->PostParams['MerchantTradeNo']; // 參數內容
		$AllowEmpty = false; // 是否允許空值
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/^[a-zA-Z0-9]{1,20}$/', $Value);
		}
	}
	
	/**
	 *  物流類型檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @version		1.0.1012
	 */
	private function ValidateLogisticsType() {
		$Name = 'LogisticsType'; // 參數名稱
		$Value = $this->PostParams['LogisticsType']; // 參數內容
		$ClassName = 'LogisticsType'; // 合法資料 Class 名稱
		$AllowEmpty = false; // 是否允許空值
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 內容檢查
			$this->IsLegalValue($Name, $ClassName, $Value);
		}
	}
	
	/**
	 *  物流子類型檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateLogisticsSubType($AllowEmpty = false) {
		$Name = 'LogisticsSubType'; // 參數名稱
		$Value = $this->PostParams['LogisticsSubType']; // 參數內容
		$ClassName = 'LogisticsSubType'; // 合法資料 Class 名稱
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			if (isset($this->PostParams['LogisticsType'])) {
				$LogisticsType = $this->PostParams['LogisticsType'];
				// 宅配物流子類型檢查
				if ($LogisticsType === LogisticsType::HOME) {
					if (
						$Value != LogisticsSubType::TCAT and
						$Value != LogisticsSubType::ECAN
					) {
						throw new Exception('Invalid home delivery ' . $Name . '.');
					}
				}
				
				// 超商取貨物流子類型檢查
				if ($LogisticsType === LogisticsType::CVS) {
					if (
						$Value != LogisticsSubType::FAMILY and
						$Value != LogisticsSubType::UNIMART and
						$Value != LogisticsSubType::HILIFE and
						$Value != LogisticsSubType::FAMILY_C2C and
						$Value != LogisticsSubType::UNIMART_C2C and
						$Value != LogisticsSubType::HILIFE_C2C
					) {
						throw new Exception('Invalid CVS pickup ' . $Name . '.');
					}
				}
			}
			
			// 物流類型無設定時的內容檢查
			$this->IsLegalValue($Name, $ClassName, $Value);
		}
	}
	
	/**
	 *  是否代收貨款檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateIsCollection($AllowEmpty = false) {
		$Name = 'IsCollection'; // 參數名稱
		$Value = $this->PostParams['IsCollection']; // 參數內容
		$ClassName = 'IsCollection'; // 合法資料 Class 名稱
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 內容檢查
			$this->IsLegalValue($Name, $ClassName, $Value);
			
			// 目前物流類型(LogisticsType)宅配(Home)不支援代收貨款(IsCollection = Y)
			if ($this->PostParams['LogisticsType'] == LogisticsType::HOME and $Value == IsCollection::YES) {
				throw new Exception($Name . ' could not be Y, when LogisticsType is Home.');
			}
		}
	}
	
	/**
	 *  使用設備檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateDevice($AllowEmpty = false) {
		$Name = 'Device'; // 參數名稱
		$Value = $this->PostParams['Device']; // 參數內容
		$ClassName = 'Device'; // 合法資料 Class 名稱
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 資料型態檢查
			$this->IsInteger($Name, $Value);
			
			// 內容檢查
			$this->IsLegalValue($Name, $ClassName, $Value);
		}
	}
	
	/**
	 *  廠商交易時間檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @version		1.0.1012
	 */
	private function ValidateMerchantTradeDate() {
		$Name = 'MerchantTradeDate'; // 參數名稱
		$Value = $this->PostParams['MerchantTradeDate']; // 參數內容
		$ClassName = 'MerchantTradeDate'; // 合法資料 Class 名稱
		$AllowEmpty = false; // 是否允許空值
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 日期檢查
			$this->IsDate($Name, 'Y/m/d H:i:s', $Value);
		}
	}

	/**
	 *  溫層檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @version		1.0.1012
	 */
	private function ValidateTemperature() {
		$Name = 'Temperature'; // 參數名稱
		$Value = $this->PostParams['Temperature']; // 參數內容
		$ClassName = 'Temperature'; // 合法資料 Class 名稱
		$AllowEmpty = false; // 是否允許空值
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 內容檢查
			$this->IsLegalValue($Name, $ClassName, $Value);
		}
	}


	/**
	 *  距離檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @version		1.0.1012
	 */
	private function ValidateDistance() {
		$Name = 'Distance'; // 參數名稱
		$Value = $this->PostParams['Distance']; // 參數內容
		$ClassName = 'Distance'; // 合法資料 Class 名稱
		$AllowEmpty = false; // 是否允許空值
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 內容檢查
			$this->IsLegalValue($Name, $ClassName, $Value);
		}
	}
	
	/**
	 *  規格檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @version		1.0.1012
	 */
	private function ValidateSpecification() {
		$Name = 'Specification'; // 參數名稱
		$Value = $this->PostParams['Specification']; // 參數內容
		$ClassName = 'Specification'; // 合法資料 Class 名稱
		$AllowEmpty = false; // 是否允許空值
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 內容檢查
			$this->IsLegalValue($Name, $ClassName, $Value);
		}
	}
		
	/**
	 *  預定送達時段檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateScheduledDeliveryTime($AllowEmpty = false) {
		$Name = 'ScheduledDeliveryTime'; // 參數名稱
		$Value = $this->PostParams['ScheduledDeliveryTime']; // 參數內容
		$ClassName = 'ScheduledDeliveryTime'; // 合法資料 Class 名稱
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 內容檢查
			$this->IsLegalValue($Name, $ClassName, $Value);
		}
	}
	
	/**
	 *  物流訂單出貨日期檢查
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.1012
	 */
	private function ValidateShipmentDate($AllowEmpty = false) {
		$Name = 'ShipmentDate'; // 參數名稱
		$Value = $this->PostParams['ShipmentDate']; // 參數內容
		$ClassName = 'ShipmentDate'; // 合法資料 Class 名稱
		
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 日期檢查
			$this->IsDate($Name, 'Y/m/d', $Value);
		}
	}

	/**
	 *  商品名稱
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.181001
	 */
	private function ValidateGoodsName($Name, $Value, $AllowEmpty = false) {
	
		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/[\^\'`\!＠#%&\*\+\\\"<>\|_\[\]]+/', $Value, true);
		}
	}

	/**
	 *  寄件人、退貨人姓名
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.181001
	 */
	private function ValidateSenderName($Name, $Value, $AllowEmpty = false) {

		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/[\^\'`\!＠#%&\*\+\\\"<>\|_\[\]\.,]+/', $Value, true);
		}
	}

	/**
	 *  收件人姓名 
	 *
	* @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @param		Boolean	$AllowEmpty	是否允許空值
	 * @version		1.0.181001
	 */
	private function ValidateReceiverName($Name, $Value, $AllowEmpty = false) {

		if (empty($Value)) {
			// 是否允許空值
			$this->IsAllowEmpty($Name, $AllowEmpty);
		} else {
			// 格式檢查
			$this->IsValidFormat($Name, '/[\^\'`\!＠#%&\*\+\\\"<>\|_\[\]]+/', $Value, true);
		}
	}

	/**
	 *  是否允許空值
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		boolean	$AllowEmpty	是否允許空值
	 * @return		boolean
	 * @version		1.0.1012
	 */
	private function IsAllowEmpty($Name, $AllowEmpty){
		if (!$AllowEmpty) {
			throw new Exception($Name . ' is required.');
		}
	}
		
	/**
	 *  是否超過長度限制
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name			參數名稱
	 * @param		Integer	$Length			參數長度
	 * @param		Integer	$MaxLength 		參數限制長度
	 * @version		1.0.1012
	 */
	private function IsOverLength($Name, $Length, $MaxLength) {
		if ($Length > $MaxLength) {
			throw new Exception($Name . ' max length is ' . $MaxLength . '.');
		}
	}

	/**
	 *  是否超過長度限制
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name			參數名稱
	 * @param		Integer	$Length			參數長度
	 * @param		Integer	$MinLength 		參數限制長度
	 * @version		1.0.1012
	 */
	private function IsBelowLength($Name, $Length, $MinLength) {
		if ($Length < $MinLength) {
			throw new Exception($Name . ' min length is ' . $MinLength . '.');
		}
	}
	
	/**
	 *  是否為指定格式
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Pattern	格式檢查用正規表示法
	 * @param		String	$Value		參數內容
	 * @version		1.0.1012
	 */
	private function IsValidFormat($Name, $Pattern, $Value, $phase = false) {
		if (!empty($Value)) {
			
			if($phase === false ){
				if (!preg_match($Pattern, $Value)) {
					throw new Exception('Invalid ' . $Name . '.');
				}
			}
			else{
				if (preg_match($Pattern, $Value)) {
					throw new Exception('Invalid ' . $Name . '.');
				}
			}
		}
	}
	
	/**
	 *  是否為數值
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name		參數名稱
	 * @param		String	$Value		參數內容
	 * @version		1.0.1012
	 */
	private function IsInteger($Name, $Value) {
		if (!is_int($Value)) {
			throw new Exception($Name . ' type should be integer.');
		}
	}
	
	/**
	 *  是否為數值
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name			參數名稱
	 * @param		String	$ClassName		合法資料 Class 名稱
	 * @param		String	$Value			參數內容
	 * @version		1.0.1012
	 */
	private function IsLegalValue($Name, $ClassName, $Value) {
		// 取得合法資料內容
		$ReflectionObject = new ReflectionClass($ClassName);
		$ContentList = $ReflectionObject->getConstants();
		unset($ReflectionObject);
		
		// 檢查是否為合法資料
		if (!in_array($Value, $ContentList)) {
			throw new Exception('Illegal ' . $Name . '.');
		}
	}
	
	/**
	 *  是否為正確日期
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Validate
	 * @param		String	$Name			參數名稱
	 * @param		String	$Format			日期格式
	 * @param		String	$Value			參數內容
	 * @version		1.0.1012
	 */
	private function IsDate($Name, $Format, $Value) {
		if (date($Format, strtotime($Value)) != $Value){
			throw new Exception('Invalid ' . $Name . '.');
		}
	}
	
	/**
	 *  取得並過濾 $_POST 參數
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		SDK_Misc
	 * @param		Array	$Source			$_POST 參數來源
	 * @param		Array	$ParamList		合法參數與預設值
	 * @param		Array	$MergeParams	其他待合併參數
	 * @return		Array
	 * @version		1.0.1012
	 */
	private function GetPostParams($Source, $ParamList, $MergeParams = array()) {
		// 過濾非法參數
		$PostParams = array();
		foreach ($ParamList as $Name => $Value) {
			if (isset($Source[$Name])) {
				$PostParams[$Name] = $Source[$Name];
			} else {
				// 若未設定則帶預設值
				$PostParams[$Name] = $Value;
			}
		}
		return array_merge($MergeParams, $PostParams);
	}
	
	/**
	 *  取得 ECPay URL
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category			SDK_Misc
	 * @param		String	$FunctionType	功能名稱
	 * @return		String
	 * @version		1.0.1012
	 */
	private function GetURL($FunctionType) {
		$MerchantID = $this->PostParams['MerchantID'];
		$UrlList = array();
		if ($MerchantID == ECPayTestMerchantID::B2C or $MerchantID == ECPayTestMerchantID::C2C) {
			// 測試環境
			$UrlList = array(
				'CVS_MAP' => ECPayTestURL::CVS_MAP,
				'SHIPPING_ORDER' => ECPayTestURL::SHIPPING_ORDER,
				'HOME_RETURN_ORDER' => ECPayTestURL::HOME_RETURN_ORDER,
				'UNIMART_RETURN_ORDER' => ECPayTestURL::UNIMART_RETURN_ORDER,
				'HILIFE_RETURN_ORDER' => ECPayTestURL::HILIFE_RETURN_ORDER,
				'FAMILY_RETURN_ORDER' => ECPayTestURL::FAMILY_RETURN_ORDER,
				'FAMILY_RETURN_CHECK' => ECPayTestURL::FAMILY_RETURN_CHECK,
				'UNIMART_UPDATE_LOGISTICS_INFO' => ECPayTestURL::UNIMART_UPDATE_LOGISTICS_INFO,
				'UNIMART_UPDATE_STORE_INFO' => ECPayTestURL::UNIMART_UPDATE_STORE_INFO,
				'UNIMART_CANCEL_LOGISTICS_ORDER' => ECPayTestURL::UNIMART_CANCEL_LOGISTICS_ORDER,
				'QUERY_LOGISTICS_INFO' => ECPayTestURL::QUERY_LOGISTICS_INFO,
				'PRINT_TRADE_DOC' => ECPayTestURL::PRINT_TRADE_DOC,
				'PRINT_UNIMART_C2C_BILL' => ECPayTestURL::PRINT_UNIMART_C2C_BILL,
				'PRINT_FAMILY_C2C_BILL' => ECPayTestURL::PRINT_FAMILY_C2C_BILL,
				'Print_HILIFE_C2C_BILL' => ECPayTestURL::Print_HILIFE_C2C_BILL,
				'CREATE_TEST_DATA' => ECPayTestURL::CREATE_TEST_DATA,
			);
		} else {
			// 正式環境
			$UrlList = array(
				'CVS_MAP' => ECPayURL::CVS_MAP,
				'SHIPPING_ORDER' => ECPayURL::SHIPPING_ORDER,
				'HOME_RETURN_ORDER' => ECPayURL::HOME_RETURN_ORDER,
				'UNIMART_RETURN_ORDER' => ECPayURL::UNIMART_RETURN_ORDER,
				'HILIFE_RETURN_ORDER' => ECPayURL::HILIFE_RETURN_ORDER,
				'FAMILY_RETURN_ORDER' => ECPayURL::FAMILY_RETURN_ORDER,
				'FAMILY_RETURN_CHECK' => ECPayURL::FAMILY_RETURN_CHECK,
				'UNIMART_UPDATE_LOGISTICS_INFO' => ECPayURL::UNIMART_UPDATE_LOGISTICS_INFO,
				'UNIMART_UPDATE_STORE_INFO' => ECPayURL::UNIMART_UPDATE_STORE_INFO,
				'UNIMART_CANCEL_LOGISTICS_ORDER' => ECPayURL::UNIMART_CANCEL_LOGISTICS_ORDER,
				'QUERY_LOGISTICS_INFO' => ECPayURL::QUERY_LOGISTICS_INFO,
				'PRINT_TRADE_DOC' => ECPayURL::PRINT_TRADE_DOC,
				'PRINT_UNIMART_C2C_BILL' => ECPayURL::PRINT_UNIMART_C2C_BILL,
				'PRINT_FAMILY_C2C_BILL' => ECPayURL::PRINT_FAMILY_C2C_BILL,
				'Print_HILIFE_C2C_BILL' => ECPayURL::Print_HILIFE_C2C_BILL,
				'CREATE_TEST_DATA' => ECPayURL::CREATE_TEST_DATA,
			);
		}
		
		return $UrlList[$FunctionType];
	}
	
	/**
	 *  加入換行字元
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Misc
	 * @param		String	$Content	內容
	 * @return		String
	 * @version		1.0.1012
	 */
	private function AddNextLine($Content) {
		return $Content . PHP_EOL;
	}
	
	/**
	 *  產生自動/手動 POST 提交表單
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		SDK_Misc
	 * @param		String	$ButtonDesc	按鈕顯示名稱
	 * @param		String	$Target		表單 action 目標
	 * @return		String
	 * @version		1.0.1012
	 */
	private function GenPostHTML($ButtonDesc = '', $Target = '_self') {
		$PostHTML = $this->AddNextLine('<div style="text-align:center;">');
		$PostHTML .= $this->AddNextLine('  <form id="ECPayForm" method="POST" action="' . $this->ServiceURL . '" target="' . $Target . '">');
		foreach ($this->PostParams as $Name => $Value) {
			$PostHTML .= $this->AddNextLine('    <input type="hidden" name="' . $Name . '" value="' . $Value . '" />');
		}
		if (!empty($ButtonDesc)) {
			// 手動
			$PostHTML .= $this->AddNextLine('    <input type="submit" id="__paymentButton" value="' . $ButtonDesc . '" />');
		} else {
			// 自動
			$PostHTML .= $this->AddNextLine('    <script>document.getElementById("ECPayForm").submit();</script>');
		}
		$PostHTML .= $this->AddNextLine('  </form>');
		$PostHTML .= $this->AddNextLine('</div>');
		
		return $PostHTML;
	}
	
	/**
	 *  依編碼方式取得字串長度
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		Misc
	 * @param		String	$RetriveString	字串內容
	 * @param		String	$Encode 		字串編碼
	 * @return		Integer
	 * @version		1.0.1012
	 */
	private function StringLength($RetriveString, $Encode) {
		return mb_strlen($RetriveString, $Encode);
	}
	
	/**
	
	 *  解析 ECPay 回傳結果
	 *
	 * @author		https://www.ecpay.com.tw
	 * @category		SDK_Misc
	 * @param		String	$Feedback		回傳結果
	 * @param		Array	$FeedbackList	合法回傳參數
	 * @param		String	$Separator		分隔符號
	 * @return		Array
	 * @version		1.0.1012
	 */
	private function ParseFeedback($Feedback, $FeedbackList = array('RtnCode', 'RtnMsg'), $Separator = '|') {
		$Pieces = explode($Separator, $Feedback);
		$Feedback = array();
		// 回傳參數檢查
		if (count($Pieces) == count($FeedbackList)) {
			foreach ($FeedbackList as $Idx => $Name) {
				$Feedback[$Name] = $Pieces[$Idx];
			}
		} else {
			$Feedback['ParseMsg'] = 'Unknown feedback : ' . $Feedback;
		}
		return $Feedback;
	}
}

if (!class_exists('ECPay_CheckMacValue', true)) {
	class ECPay_CheckMacValue
	{
		/**
		* 產生檢查碼
		*/
		static function generate($arParameters = array(), $HashKey = '', $HashIV = ''){

			$sMacValue = '' ;

			if(isset($arParameters)){
				
				unset($arParameters['CheckMacValue']);
				uksort($arParameters, array('ECPay_CheckMacValue','merchantSort'));

				// 組合字串
				$sMacValue = 'HashKey=' . $HashKey ;
				foreach($arParameters as $key => $value)
				{
					$sMacValue .= '&' . $key . '=' . $value ;
				}

				$sMacValue .= '&HashIV=' . $HashIV ;

				// URL Encode編碼     
				$sMacValue = urlencode($sMacValue);

				// 轉成小寫
				$sMacValue = strtolower($sMacValue);

				// 取代為與 dotNet 相符的字元
				$sMacValue = ECPay_CheckMacValue::Replace_Symbol($sMacValue);

				// 編碼
				$sMacValue = md5($sMacValue);

				$sMacValue = strtoupper($sMacValue);
			}

			return $sMacValue ;
		}

		/**
		* 自訂排序使用
		*/
		private static function merchantSort($a,$b){
			return strcasecmp($a, $b);
		}

	    /**
		* 參數內特殊字元取代
		* 傳入	$sParameters	參數
		* 傳出	$sParameters	回傳取代後變數
		*/
		static function Replace_Symbol($sParameters){
			if(!empty($sParameters)){
				
				$sParameters = str_replace('%2D', '-', $sParameters);
				$sParameters = str_replace('%2d', '-', $sParameters);
				$sParameters = str_replace('%5F', '_', $sParameters);
				$sParameters = str_replace('%5f', '_', $sParameters);
				$sParameters = str_replace('%2E', '.', $sParameters);
				$sParameters = str_replace('%2e', '.', $sParameters);
				$sParameters = str_replace('%21', '!', $sParameters);
				$sParameters = str_replace('%2A', '*', $sParameters);
				$sParameters = str_replace('%2a', '*', $sParameters);
				$sParameters = str_replace('%28', '(', $sParameters);
				$sParameters = str_replace('%29', ')', $sParameters);
			}

			return $sParameters ;
		}

		/**
		* 參數內特殊字元還原
		* 傳入	$sParameters	參數
		* 傳出	$sParameters	回傳取代後變數
		*/
		static function Replace_Symbol_Decode($sParameters){
			if(!empty($sParameters)){
				
				$sParameters = str_replace('-', '%2d', $sParameters);
				$sParameters = str_replace('_', '%5f', $sParameters);
				$sParameters = str_replace('.', '%2e', $sParameters);
				$sParameters = str_replace('!', '%21', $sParameters);
				$sParameters = str_replace('*', '%2a', $sParameters);
				$sParameters = str_replace('(', '%28', $sParameters);
				$sParameters = str_replace(')', '%29', $sParameters);
				$sParameters = str_replace('+', '%20', $sParameters);
			}

			return $sParameters ;
		}
	}
}

if (!class_exists('ECPay_IO', true)) {
	class ECPay_IO
	{
		static function ServerPost($parameters ,$ServiceURL){

		    $sSend_Info = '' ;

		    // 組合字串
			foreach($parameters as $key => $value)
			{
				if( $sSend_Info == '')
				{
					$sSend_Info .= $key . '=' . $value ;
				}
				else
				{
					$sSend_Info .= '&' . $key . '=' . $value ;
				}
			}

		    $ch = curl_init();

		    if (FALSE === $ch) {
		        throw new Exception('curl failed to initialize');
		    }
		    
		    curl_setopt($ch, CURLOPT_URL, $ServiceURL);
		    curl_setopt($ch, CURLOPT_HEADER, FALSE);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		    curl_setopt($ch, CURLOPT_POST, TRUE);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $sSend_Info);
		    $rs = curl_exec($ch);

		    if (FALSE === $rs) {
		        throw new Exception(curl_error($ch), curl_errno($ch));
		    }

		    curl_close($ch);

		    return $rs;
		}
	}
}

?>