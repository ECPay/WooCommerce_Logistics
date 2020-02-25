/*
 * ECPay integration shipping setting
 */
jQuery(document).ready(function($) {

    // ecpay_checkout_form is required to continue, ensure the object exists
    if (typeof ecpay_checkout_request === 'undefined') {
        return false;
    }

    var ecpay_checkout_form = {
        $checkout_form: $('form.checkout'),
        $param: {},

        // 初始化
        init: function() {
            var param = {
                shipping: '',
                category: ecpay_checkout_request.category, // 物流類別
                payment: $('[name="payment_method"]'), // 金流
                url: ecpay_checkout_request.ajaxUrl, // 記錄 session 用 URL
            };
            this.$param = param;
            ecpay_checkout_form.ecpay_cvs_shipping_field_hide();
            ecpay_checkout_form.ecpay_shipping_change_payment();
        },

        // 隱藏門市資訊內容
        ecpay_cvs_shipping_field_hide: function() {
            ecpay_checkout_form.ecpay_hide_shipping_field('CVSStoreID');
            ecpay_checkout_form.ecpay_hide_shipping_field('purchaserStore');
            ecpay_checkout_form.ecpay_hide_shipping_field('purchaserAddress');
            ecpay_checkout_form.ecpay_hide_shipping_field('purchaserPhone');
        },

        // 加入選取綠界物流超商 change 事件處理
        init_ecpay_shipping_choose: function() {
            this.$checkout_form.on('change',
                '#shipping_option',
                this.choose_ecpay_shipping
            );
        },

        // 加入選取綠界電子地圖按鍵 click 事件處理
        init_ecpay_shipping_submit: function() {
            this.$checkout_form.on('click',
                '#__paymentButton',
                this.submit_ecpay_shipping
            );
        },

        // 記錄結帳資訊
        ecpay_save_checkout_data: function() {
            var input_value = ecpay_checkout_form.get_input_value();
            var data = {
                checkoutInput: input_value
            };
            ecpay_checkout_form.ecpay_save_data(data);
        },

        // 記錄選擇物流
        set_ecpay_shipping: function() {
            var e = document.getElementById("shipping_option");
            var shipping = e.options[e.selectedIndex].value;
            ecpay_checkout_form.$param.shipping = shipping;
        },

        // 選取綠界物流處理
        choose_ecpay_shipping: function() {
            var shippingMethod = {};

            // 記錄選擇物流
            ecpay_checkout_form.set_ecpay_shipping();

            var param = ecpay_checkout_form.$param;

            if (param.category == 'C2C') {
                shippingMethod = {
                    'FAMI': 'FAMIC2C',
                    'FAMI_Collection': 'FAMIC2C',
                    'UNIMART': 'UNIMARTC2C',
                    'UNIMART_Collection': 'UNIMARTC2C',
                    'HILIFE': 'HILIFEC2C',
                    'HILIFE_Collection': 'HILIFEC2C',
                };
            } else {
                shippingMethod = {
                    'FAMI': 'FAMI',
                    'FAMI_Collection': 'FAMI',
                    'UNIMART': 'UNIMART',
                    'UNIMART_Collection': 'UNIMART',
                    'HILIFE': 'HILIFE',
                    'HILIFE_Collection': 'HILIFE',
                };
            }

            // 變更電子地圖超商
            if (param.shipping in shippingMethod) {
                document.getElementsByName("LogisticsSubType")[0].value = shippingMethod[param.shipping];
                var data = {
                    ecpayShippingType: param.shipping
                };
                ecpay_checkout_form.ecpay_save_data(data);
            }
            ecpay_checkout_form.ecpay_cvs_shipping_field_clear();
            ecpay_checkout_form.ecpay_shipping_change_payment();
        },

        // 電子地圖按鈕 submit 處理
        submit_ecpay_shipping: function() {
            ecpay_checkout_form.ecpay_save_checkout_data(); // 記錄結帳資訊

            // 選擇物流檢查
            if ($('#shipping_option').val() == "------") {
                alert('請選擇物流方式');
                return false;
            }

            // IE 若不使用此 method 將無法跳轉至選擇電子地圖頁
            document.getElementById('ECPayForm').submit();
        },

        // 取得結帳資訊
        get_input_value: function() {
            var billing_first_name = ecpay_checkout_form.ecpay_get_element_value('billing_first_name');
            var billing_last_name = ecpay_checkout_form.ecpay_get_element_value('billing_last_name');
            var billing_company = ecpay_checkout_form.ecpay_get_element_value('billing_company');
            var billing_phone = ecpay_checkout_form.ecpay_get_element_value('billing_phone');
            var billing_email = ecpay_checkout_form.ecpay_get_element_value('billing_email');
            var order_comments = ecpay_checkout_form.ecpay_get_element_value('order_comments');
            var shipping_first_name = '';
            var shipping_last_name = '';
            var shipping_company = '';
            var shipping_to_different_address = '0';
            if ($('#ship-to-different-address').find('input').is(':checked')) {
                shipping_to_different_address = '1';
                shipping_first_name = ecpay_checkout_form.ecpay_get_element_value('shipping_first_name');
                shipping_last_name = ecpay_checkout_form.ecpay_get_element_value('shipping_last_name');
                shipping_company = ecpay_checkout_form.ecpay_get_element_value('shipping_company');
            }
            var data = {
                billing_first_name: billing_first_name,
                billing_last_name: billing_last_name,
                billing_company: billing_company,
                billing_phone: billing_phone,
                billing_email: billing_email,
                order_comments: order_comments,
                shipping_to_different_address: shipping_to_different_address,
                shipping_first_name: shipping_first_name,
                shipping_last_name: shipping_last_name,
                shipping_company: shipping_company
            };
            return data;
        },

        // 記錄資訊至 Session
        ecpay_save_data: function(data) {
            jQuery.ajax({
                url: ecpay_checkout_form.$param.url + "=Ecpay_Get_Checkout_Session",
                type: 'post',
                async: false,
                data: data,
                dataType: 'json',
                success: function(data, textStatus, xhr) {},
                error: function(xhr, textStatus, errorThrown) {}
            });
        },

        // 清除綠界物流資訊
        ecpay_cvs_shipping_field_clear: function() {
            $('#CVSStoreID').val('');
            $('#purchaserStore').val('');
            $('#purchaserAddress').val('');
            $('#purchaserPhone').val('');
            $('#purchaserStoreLabel').html('');
            $('#purchaserAddressLabel').html('');
            $('#purchaserPhoneLabel').html('');
            $('#__paymentButton').html('電子地圖');

            // 清除門市資訊 session
            $.post(ecpay_checkout_form.$param.url  + "=Ecpay_Cvs_Info_Session_Clear");
        },

        // 付款方式變更
        ecpay_shipping_change_payment: function() {
            if (document.getElementById("payment_method_ecpay_shipping_pay") === null ||
                typeof document.getElementById("payment_method_ecpay_shipping_pay") === "undefined"
            ) {
                return;
            }

            // default payment method, get the last item in payment method.
            var payment_method = ecpay_checkout_form.ecpay_default_payment_method();

            // if payment method O'Pay is enable
            if (document.getElementById("payment_method_allpay") !== null &&
                typeof document.getElementById("payment_method_allpay") !== "undefined"
            ) {
                payment_method = 'payment_method_allpay';
            }

            // if payment method ECPay is enable
            if (document.getElementById("payment_method_ecpay") !== null &&
                typeof document.getElementById("payment_method_ecpay") !== "undefined"
            ) {
                payment_method = 'payment_method_ecpay';
            }

            // 取得目前物流方式
            ecpay_checkout_form.set_ecpay_shipping();

            var shipping = ecpay_checkout_form.$param.shipping;
            var payment = ecpay_checkout_form.$param.payment;
            var ecpay_shipping_collection = [
                'HILIFE_Collection',
                'FAMI_Collection',
                'UNIMART_Collection',
            ];
            // 判斷是否為取貨付款
            if (ecpay_shipping_collection.indexOf(shipping) !== -1) {
                ecpay_checkout_form.ecpay_shipping_isCollection(payment_method, payment);
            } else {
                ecpay_checkout_form.ecpay_shipping_isNOTCollection(payment_method, payment);
            }
        },

        // 取貨付款處理
        ecpay_shipping_isCollection: function(payment_method, payment) {
            var ecpay_shipping_payment_id = 'payment_method_ecpay_shipping_pay';
            var ecpay_shipping_payment_class = ecpay_shipping_payment_id;
            var payment_class = ''; // 金流 class
            var display_value = ''; // style display 值
            var select_value = false; // checked 值
            for (var i = 0; i < payment.length; i++) {
                payment_id = payment[i].id;
                payment_class = payment_id;
                if (payment_class === ecpay_shipping_payment_class) {
                    // 選取取貨付款
                    select_value = true;

                    // 顯示取貨付款選項
                    display_value = '';
                } else {
                    // 取消選取其他付款方式
                    select_value = false;

                    // 隱藏其他付款方式
                    display_value = 'none';
                }
                ecpay_checkout_form.ecpay_select_element(ecpay_shipping_payment_id, true);
                ecpay_checkout_form.ecpay_display_payment_by_class(payment_class, display_value);
            }
        },

        // 非取貨付款處理
        ecpay_shipping_isNOTCollection: function(default_payment_method, payment) {
            var ecpay_shipping_payment_id = 'payment_method_ecpay_shipping_pay';
            var ecpay_shipping_payment_class = ecpay_shipping_payment_id;
            var payment_class = ''; // 金流 class
            var display_value = ''; // style display 值
            for (var i = 0; i < payment.length; i++) {
                payment_id = payment[i].id;
                payment_class = payment_id;
                if (payment_class === ecpay_shipping_payment_class) {
                    // 移除選取取貨付款
                    ecpay_checkout_form.ecpay_select_element(ecpay_shipping_payment_id, false);

                    // 設定預設金流
                    ecpay_checkout_form.ecpay_select_element(default_payment_method, true);

                    // 隱藏取貨付款選項
                    display_value = 'none';
                } else {
                    // 顯示其他付款方式
                    display_value = '';
                }
                ecpay_checkout_form.ecpay_display_payment_by_class(payment_class, display_value);
            }
        },

        // 取得預設金流
        ecpay_default_payment_method: function() {
            var payment = ecpay_checkout_form.$param.payment;
            var payments = [];
            for (var i = 0; i < payment.length; i++) {
                if (payment[i].id !== 'payment_method_ecpay_shipping_pay') {
                    payments.push(payment[i].id);
                }
            }

            return payments.pop();
        },

        // 顯示金流(由 class 控制)
        ecpay_display_payment_by_class: function(payment_class, display_value) {
            var full_class = ecpay_checkout_form.ecpay_get_payment_class_name(payment_class);
            var payment_elements = document.getElementsByClassName(full_class);
            payment_elements[0].style.display = display_value;
        },

        // 取得金流 class 名稱
        ecpay_get_payment_class_name: function(payment_class) {
            var payment_main_class = 'wc_payment_method '; // 金流選項主 class
            var class_exists = document.getElementsByClassName(payment_main_class + payment_class).length;
            var full_class = '';
            if (class_exists === 0) {
                full_class = payment_class;
            } else {
                full_class = payment_main_class + payment_class;
            }
            return full_class;
        },

        // 設定預設金流
        ecpay_set_default_payment_method: function(payment_method_id) {
            ecpay_checkout_form.ecpay_select_element(payment_method_id, true);
        },

        // 選取元素
        ecpay_select_element: function(id, value) {
            document.getElementById(id).checked = value;
        },

        // 取得元素值
        ecpay_get_element_value: function(id) {
            return document.getElementById(id).value;
        },

        // 隱藏綠界取貨付款欄位
        ecpay_hide_shipping_field: function(name) {
            $("label[for='" + name + "']").removeAttr("style").hide();
            $("#" + name).removeAttr("style").hide();
        },

    };

    ecpay_checkout_form.init();
    ecpay_checkout_form.init_ecpay_shipping_choose();
    ecpay_checkout_form.init_ecpay_shipping_submit();
});