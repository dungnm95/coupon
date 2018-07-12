/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Multi_Coupon/js/action/set-coupon-code',
    'Multi_Coupon/js/action/cancel-coupon'
], function ($, ko, Component, quote, setCouponCodeAction, cancelCouponAction) {
    'use strict';

    var totals = quote.getTotals(),
        couponCode = ko.observable(null),
        ApplyCode = ko.observable(null),
        isApplied;

    if (totals()) {
        couponCode(totals()['coupon_code']);
    }
    isApplied = ko.observable(couponCode() != null);

    return Component.extend({
        defaults: {
            template: 'Multi_Coupon/payment/discount'
        },
        couponCode: couponCode,
        ApplyCode: ApplyCode,

        /**
         * Applied flag
         */
        isApplied: isApplied,

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = '#discount-form';

            return $(form).validation() && $(form).validation('isValid');
        },

        /**
         * Coupon code application procedure
         */

        apply: function () {

            var list_code = '';
            var check_coupon_code = setCouponCodeAction(ApplyCode(), isApplied);
            var list_old_coupon = '';
            if (couponCode() != null && couponCode() != '') {
                list_old_coupon = couponCode();
            }
            couponCode(list_old_coupon);
            setTimeout(function(){
                if(check_coupon_code.status != 200){
                        list_code = list_old_coupon;
                }else{
                    if (list_old_coupon != '') {
                        var check_duplicate = false;
                        var array_list_coupon = list_old_coupon.split(",");
                        array_list_coupon.forEach(function (item) {
                            if(item == ApplyCode()){
                                check_duplicate = true;
                            }
                        });
                        if(check_duplicate){
                            list_code = list_old_coupon;
                        }else{
                            list_code = list_old_coupon + ',' + ApplyCode();
                        }

                    } else {
                        list_code = ApplyCode();
                    }
                }
                couponCode(list_code);
                setCouponCodeAction(couponCode(), isApplied);
                ApplyCode('');
            }, 800);


        },

        /**
         * Cancel using coupon
         */
        cancel: function (code) {

            var new_list_coupon = '';
            if (couponCode().indexOf(',') == -1) {
                new_list_coupon = '';
            } else {
                new_list_coupon = couponCode().replace(',' + code, '');
            }
            couponCode(new_list_coupon);
            if (new_list_coupon != '') {
                setCouponCodeAction(new_list_coupon, isApplied);
            } else {
                cancelCouponAction(isApplied);
            }


        },


    });
});
