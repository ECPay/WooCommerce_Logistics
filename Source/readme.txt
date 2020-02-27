=== ECPay Logistics for WooCommerce ===
Contributors: ecpaytechsupport
Tags: ecommerce, e-commerce, store, sales, sell, shop, cart, checkout, logistics, ecpay
Requires at least: 5.2
Tested up to: 5.3
Requires PHP: 5.6 or later
Stable tag: 1.3.1910240
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

綠界科技物流外掛套件

== Description ==

綠界科技物流外掛套件，提供合作特店以及個人會員使用開放原始碼商店系統時，無須自行處理複雜的檢核，直接透過安裝設定外掛套件，便可以較快速的方式介接綠界科技的物流系統。

= 物流模組 =
綠界科技物流提供會員方便快速的商品運送機制，目前超商取貨服務提供「全家便利商店」、「統一超商」、「萊爾富」。

= 物流寄送型態 =
- 大宗寄倉超商取貨今日自行將包裹送往指定物流中心，買家明天超商取貨。
- 超商門市寄貨/取貨，今日自行就近至鄰近超商門市寄件，買家後天超商取貨。

= 注意事項 =
- 若須同時使用綠界科技WooCommerce金流模組，除了更新綠界科技WooCommerce物流模組外，綠界科技WooCommerce金流模組也請同步更新才能正常使用。

= 聯絡我們 =
  綠界技術客服信箱: techsupport@ecpay.com.tw


== Installation ==

= 系統需求 =

- PHP version 5.6 or greater
- MySQL version 5.5 or greater

= 自動安裝 =
1. 登入至您的 WordPress dashboard，拜訪 "Plugins menu" 並點擊 "Add"。
2. 在"search field"中輸入"ECPay Logistics for WooCommerce"，然後點擊搜尋。
3. 點擊 "安裝" 即可進行安裝。

= 手動安裝 =
詳細說明請參閱 [綠界科技物流外掛套件安裝導引文件](https://github.com/ECPay/WooCommerce_Logistics )。


== Frequently Asked Questions ==


== Changelog ==

v1.3.2002120
更新 SDK
修正相容性至 WordPress Version 5.3.2
修正相容性至 WooCommerce Version 3.9.1
修正前台購物車頁面選擇非綠界時電子地圖顯示異常
修正後台建立物流訂單時遇到檢查碼錯誤問題

v1.3.1910240
修正模組符合 WordPress 官方審查規則

v1.3.1910180
調整檔案路徑

v1.3.1910160
調整版號規則
修正前台結帳頁選擇門市異常問題
修正前台結帳頁電子地圖按鈕文字切換

v1.3.191014
更新 SDK
修正模組符合 WordPress 官方審查規則

v1.3.191004
調整後台訂單詳細資料頁，完成選擇門市後自動更新資料庫並重整頁面
修正前台結帳頁選擇門市異常問題
修正模組符合 WordPress 官方審查規則

v1.3.190911
更新 SDK
修正弱點
修正模組符合 WordPress 官方審查規則

v1.3.190617
更新 SDK
修正相容性至 WordPress Version 5.2.1
修正相容性至 WooCommerce Version 3.6.2
新增後台超商取貨金額欄位檢查，範圍為 1~19,999 元
新增後台寄件者姓名及電話欄位防呆
新增前台收件者姓名及電話欄位防呆
新增手機版電子地圖
修正後台顯示建立物流訂單按鈕時間點

v1.2.181030
更新 SDK
修正收件者姓名異常問題

v1.2.181005
修正姓名順序

v1.2.180911
修正發票自動開立異常問題

v1.2.180626
修正Safari相容性問題

v1.2.180612
修正電子地圖超商連結異常問題

v1.2.180530
修正後台外觀>選單無法正常顯示問題

v1.2.180423
修正電子地圖超商異常問題

v1.2.180417
修正選完電子地圖部份結帳資訊被清空問題
修正部份檔案路徑異常問題
修正後台plugin顯示異常問題

v1.2.0315
調整物流API參數 GoodsAmount，物流子類型為 UNIMART/UNIMARTC2C時，商品金額範圍可為 1~20,000 元。

v1.2.0223
修正未設定綠界物流超商取貨付款時,結帳無付款方式可選的問題

v1.2.0208
優化物流訂單狀態顯示訊息

V1.2.0131
調整物流取得相對應的金流方式

V1.2.0103
優化結帳頁email格式調整

V1.1.1219
物流優化及部份問題修正

V1.1.1018
修正選完超商門市會跳回商店首頁問題

v1.1.0920
修正結帳頁選完超商門市，會員姓名及公司名稱會被清除的問題

v1.1.0801
Official release

