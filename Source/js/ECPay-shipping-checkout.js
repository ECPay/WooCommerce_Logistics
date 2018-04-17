/*
 * ECPay integration shipping setting
*/
jQuery(document).ready(function($) {
    
    // ecpay_checkout_form is required to continue, ensure the object exists
    if ( typeof ecpay_checkout_request === 'undefined' ) {
        return false;
    }

    var ecpay_checkout_form = {
        $checkout_form: $( 'form.checkout' ),
        $param: {},
        init: function() {
            var param = {
                shipping: '',
                category: $( '#category' ).val(),
                payment: $( '[name="payment_method"]' ),
                url: ecpay_checkout_request.ajaxUrl,
            };
            this.$param = param;
            ecpay_checkout_form.ecpay_cvs_shipping_field_hide();
            ecpay_checkout_form.set_checkout_field();
            ecpay_checkout_form.set_ecpay_cvs_shipping_btn();
        },
        ecpay_cvs_shipping_field_hide: function() {
            $( '#CVSStoreID' ).removeAttr('style').hide();
            $( '#purchaserStore' ).removeAttr('style').hide();
            $( '#purchaserAddress' ).removeAttr('style').hide();
            $( '#purchaserPhone' ).removeAttr('style').hide();
        },
        set_checkout_field: function() {
            var checkoutData = ecpay_checkout_request.checkoutData;
            Object.keys(checkoutData).map(function(key) {
                if (
                    document.getElementById(key) !== null &&
                    typeof document.getElementById(key) !== "undefined"
                ) {
                    document.getElementById(key).value = checkoutData[key];
                }
            });
        },
        set_ecpay_cvs_shipping_btn: function() {
            if ($( '#CVSStoreID' ).val() !== '') {
                $( '#__paymentButton' ).val('重選電子地圖');
            } else {
                $( '#__paymentButton' ).val('電子地圖');
            }
        },
        init_checkout: function() {
            this.$checkout_form.on( 'change',
                '#billing_first_name, #billing_last_name, #billing_company, #billing_phone, #billing_email, #shipping_first_name, #shipping_last_name, #shipping_company, #order_comments',
                this.submit_checkout
            );
        },
        init_ecpay_shipping_choose: function() {
            this.$checkout_form.on( 'change',
                '#shipping_option',
                this.choose_ecpay_shipping
            );
        },
        init_ecpay_shipping_submit: function() {
            this.$checkout_form.on( 'click',
                '#__paymentButton',
                this.submit_ecpay_shipping
            );
        },
        submit_checkout: function() {
            var input_value = ecpay_checkout_form.get_input_value();
            var data = {
                checkoutInput: input_value
            };
            ecpay_checkout_form.submit(data);
        },
        set_ecpay_shipping: function() {
            var e = document.getElementById("shipping_option");
            var shipping = e.options[e.selectedIndex].value;
            ecpay_checkout_form.$param.shipping = shipping;
        },
        choose_ecpay_shipping: function() {
            var shippingMethod = {};
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
            if (param.shipping in shippingMethod) {
                $( '#LogisticsSubType' ).val(shippingMethod[param.shipping]);
                var data = {
                    ecpayShippingType: param.shipping
                };
                ecpay_checkout_form.submit(data);
            }
            ecpay_checkout_form.ecpay_cvs_shipping_field_clear();
            ecpay_checkout_form.ecpay_shipping_change_payment();
        },
        submit_ecpay_shipping: function() {
            if ($( '#shipping_option' ).val() == "------") {
                alert('請選擇物流方式');
                return false;
            }
            document.getElementById('ECPayForm').submit();
        },
        get_input_value: function() {
            var billing_first_name  = $( '#billing_first_name' ).val(),
                billing_last_name   = $( '#billing_last_name' ).val(),
                billing_company     = $( '#billing_company' ).val(),
                billing_phone       = $( '#billing_phone' ).val(),
                billing_email       = $( '#billing_email' ).val(),
                shipping_first_name = billing_first_name,
                shipping_last_name  = billing_last_name,
                shipping_company    = billing_company,
                order_comments      = $( 'textarea#order_comments' ).val();

            if ( $( '#ship-to-different-address' ).find( 'input' ).is( ':checked' ) ) {
                shipping_first_name = $( '#shipping_first_name' ).val();
                shipping_last_name  = $( '#shipping_last_name' ).val();
                shipping_company    = $( '#shipping_company' ).val();
            }
            var data = {
                billing_first_name  : billing_first_name,
                billing_last_name   : billing_last_name,
                billing_company     : billing_company,
                billing_phone       : billing_phone,
                billing_email       : billing_email,
                shipping_first_name : shipping_first_name,
                shipping_last_name  : shipping_last_name,
                shipping_company    : shipping_company,
                order_comments      : order_comments
            };
            return data;
        },
        submit: function(data) {
            jQuery.ajax({
                url: ecpay_checkout_form.$param.url,
                type: 'post',
                data: data,
                dataType: 'json',
                success: function(data, textStatus, xhr) {},
                error: function(xhr, textStatus, errorThrown) {}
            });
        },
        ecpay_cvs_shipping_field_clear: function() {
            $( '#CVSStoreID' ).val('');
            $( '#purchaserStore' ).val('');
            $( '#purchaserAddress' ).val('');
            $( '#purchaserPhone' ).val('');
            $( '#purchaserStoreLabel' ).html('');
            $( '#purchaserAddressLabel' ).html('');
            $( '#purchaserPhoneLabel' ).html('');
        },
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

            var shipping = ecpay_checkout_form.$param.shipping;
            var payment = ecpay_checkout_form.$param.payment;
            var ecpay_shipping_collection = [
                'HILIFE_Collection',
                'FAMI_Collection',
                'UNIMART_Collection',
            ];
            if (ecpay_shipping_collection.indexOf(shipping) !== -1) {
                ecpay_checkout_form.ecpay_shipping_isCollection(payment_method, payment);
            } else {
                ecpay_checkout_form.ecpay_shipping_isNOTCollection(payment_method, payment);
            }
        },
        ecpay_shipping_isCollection: function(payment_method, payment) {
            for (var i = 0; i < payment.length; i++) {
                if (payment[i].id !== 'payment_method_ecpay_shipping_pay') {
                    payment[i].style.display = 'none';

                    checkclass = document.getElementsByClassName('wc_payment_method ' + payment[i].id).length;

                    if (checkclass === 0) {
                        var x = document.getElementsByClassName(payment[i].id);
                        x[0].style.display = 'none';
                    } else {
                        var x = document.getElementsByClassName('wc_payment_method ' + payment[i].id);
                        x[0].style.display = 'none';
                    }
                } else {
                    checkclass = document.getElementsByClassName('wc_payment_method ' + payment[i].id).length;

                    if (checkclass === 0) {
                        var x = document.getElementsByClassName(payment[i].id);
                        x[0].style.display = '';
                    } else {
                        var x = document.getElementsByClassName('wc_payment_method ' + payment[i].id);
                        x[0].style.display = '';
                    }
                }
            }
            document.getElementById(payment_method).checked = false;
            document.getElementById('payment_method_ecpay_shipping_pay').checked = true;
            document.getElementById('payment_method_ecpay_shipping_pay').style.display = '';
        },
        ecpay_shipping_isNOTCollection: function(payment_method, payment) {
            for (var i = 0; i < payment.length; i++) {
                if (payment[i].id !== 'payment_method_ecpay_shipping_pay') {
                    payment[i].style.display = '';

                    checkclass = document.getElementsByClassName('wc_payment_method ' + payment[i].id).length;

                    if (checkclass === 0) {
                        var x = document.getElementsByClassName(payment[i].id);
                        x[0].style.display = '';
                    } else {
                        var x = document.getElementsByClassName('wc_payment_method ' + payment[i].id);
                        x[0].style.display = '';
                    }
                } else {
                    checkclass = document.getElementsByClassName('wc_payment_method ' + payment[i].id).length;

                    if (checkclass === 0) {
                        var x = document.getElementsByClassName(payment[i].id);
                        x[0].style.display = 'none';
                    } else {
                        var x = document.getElementsByClassName('wc_payment_method ' + payment[i].id);
                        x[0].style.display = 'none';
                    }

                    document.getElementById(payment_method).checked = true;
                    document.getElementById('payment_method_ecpay_shipping_pay').checked = false;
                    document.getElementById('payment_method_ecpay_shipping_pay').style.display = 'none';
                }
            }
        },
        ecpay_default_payment_method: function() {
            var payment = ecpay_checkout_form.$param.payment;
            var payments = [];
            for (var i = 0; i < payment.length; i++) {
                if (payment[i].id !== 'payment_method_ecpay_shipping_pay') {
                    payments.push(payment[i].id);
                }
            }

            return payments.pop();
        }
    };

    ecpay_checkout_form.init();
    ecpay_checkout_form.init_checkout();
    ecpay_checkout_form.init_ecpay_shipping_choose();
    ecpay_checkout_form.init_ecpay_shipping_submit();
});
