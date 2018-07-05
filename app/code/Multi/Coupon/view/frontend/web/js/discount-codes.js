/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('mage.discountCode1', {
        options: {
        },

        /** @inheritdoc */
        _create: function () {
            this.couponCode = $(this.options.couponCodeSelector);
            this.removeCoupon = $(this.options.removeCouponSelector);
            this.inputRemoveCoupon = $(this.options.inputCouponSelector);

            $(this.options.applyButton).on('click', $.proxy(function () {
                this.couponCode.attr('data-validate', '{required:true}');
                this.removeCoupon.attr('value', '0');
                $(this.element).validation().submit();
            }, this));

            $(this.options.cancelButton).on('click', $.proxy(function (event) {
                this.couponCode.removeAttr('data-validate');
                this.removeCoupon.attr('value', '1');
                var coupon_code_remove = $(event.target).parent().children('span').text();
                this.inputRemoveCoupon.attr('value', coupon_code_remove);
                this.element.submit();
            }, this));
        }
    });

    return $.mage.discountCode;
});