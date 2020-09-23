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
        },

        // 加入選取綠界物流超商 change 事件處理
        init_ecpay_shipping_choose: function() {
            this.$checkout_form.on('change',
                '#shipping_option',
                this.choose_ecpay_shipping
            );
        },

        // 記錄選擇物流
        set_ecpay_shipping: function() {
            var e = document.getElementById("shipping_option");
            var shipping = e.options[e.selectedIndex].value;
            ecpay_checkout_form.$param.shipping = shipping;

            var data = {
                ecpayShippingType: ecpay_checkout_form.$param.shipping
            };
            ecpay_checkout_form.ecpay_save_data(data);
        },

        // 選取綠界物流處理
        choose_ecpay_shipping: function() {

            // 變更超商時觸發重整金流方式
            jQuery('#shipping_option').trigger("update_checkout");

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
        }

    };

    ecpay_checkout_form.init();
    ecpay_checkout_form.init_ecpay_shipping_choose();
});