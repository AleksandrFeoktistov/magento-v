/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define([
    'jquery',
    'stripejs',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ($, stripejs, globalMessageList, $t) {
    'use strict';

    return {
        apiClient: null,
        config: {},
        checkout: null,
        stripeCardNumber: null,

        /**
         * Get Stripe api client
         * @returns {Object}
         */
        getApiClient: function () {
            if (!this.apiClient) {
                this.apiClient = Stripe(this.getPublishableKey());
            }

            return this.apiClient;
        },

        /**
         * Set configuration
         * @param {Object} config
         */
        setConfig: function (config) {
            this.config = config;
        },

        setup: function () {
            var stripeCardElement = this.getApiClient().elements();

            var style = {
                base: {
                    fontSize: '17px'
                }
            };

            this.stripeCardNumber = stripeCardElement.create('cardNumber', {style: style});
            this.stripeCardNumber.mount(this.config.hostedFields.number.selector);
            this.stripeCardNumber.on('change', this.config.hostedFields.onFieldEvent);

            stripeCardElement
                .create('cardExpiry', {style: style})
                .mount(this.config.hostedFields.expiry.selector);

            stripeCardElement
                .create('cardCvc', {style: style})
                .mount(this.config.hostedFields.cvc.selector);
        },

        /**
         * create source by cart
         * @return {jQuery.Deferred}
         */
        createSourceByCart: function (sourceData) {
            return this.createSource.call(this, this.stripeCardNumber, sourceData);
        },

        /**
         * create source
         * @return {jQuery.Deferred}
         */
        createSource: function () {
            var self = this,
                dfd = $.Deferred();

            this.getApiClient()
                .createSource.apply(this.getApiClient(), arguments)
                .then(function (response) {
                    if (response.error) {
                        self.showError(response.error.message);
                        dfd.reject(response);
                    } else {
                        dfd.resolve(response);
                    }
                });

            return dfd;
        },

        /**
         * @return {jQuery.Deferred}
         */
        retrieveSource: function() {
            var self = this,
                dfd = $.Deferred();

            this.getApiClient()
                .retrieveSource.apply(this.getApiClient(), arguments)
                .then(function (response) {
                    if (response.error) {
                        self.showError(response.error.message);
                        dfd.reject(response);
                    } else {
                        dfd.resolve(response);
                    }
                });

            return dfd;
        },

        /**
         * Get payment name
         * @returns {String}
         */
        getCode: function () {
            return 'tnw_stripe';
        },

        /**
         * Get publishable key
         * @returns {String|*}
         */
        getPublishableKey: function () {
            return window.checkoutConfig.payment[this.getCode()].publishableKey;
        },

        /**
         * Show error message
         *
         * @param {String} errorMessage
         */
        showError: function (errorMessage) {
            globalMessageList.addErrorMessage({
                message: errorMessage
            });
        }
    };
});
