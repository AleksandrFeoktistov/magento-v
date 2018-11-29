define([
    'jquery',
    'Magento_Vault/js/view/payment/method-renderer/vault',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/create-billing-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/action/set-billing-address',
    'mage/storage',
    'mage/url',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/quote',
], function (
    $,
    VaultComponent,
    globalMessageList,
    fullScreenLoader,
    createBillingAddress,
    selectBillingAddress,
    setBillingAddressAction,
    storage,
    url,
    checkoutData,
    customerData,
    priceUtils,
    quote
) {
    'use strict';

    var dccConfig = window.checkoutConfig.payment.bluesnap_dcc;

    return VaultComponent.extend({
        defaults: {
            template: 'Bluesnap_Payment/payment/vault',
            modules: {
                hostedFields: '${ $.parentName }.bluesnap_hostedfields'
            },
            address: false,
            currentCurrency: '',
            baseCurrency: '',
            currencyOptions: [],
            dccPrice: false,
            isDccEnabled: dccConfig.isActive
        },

        initObservable: function () {
            this._super()
                .observe([
                    'address',
                    'currencyOptions',
                    'currentCurrency',
                    'dccPrice',
                    'baseCurrency',
                    'isDccEnabled'
                ]);

            this.currencyOptions(dccConfig.availableCurrencies);
            this.currentCurrency(dccConfig.selectedCurrency);
            this.currencyChange(this, null);
            this.baseCurrency(dccConfig.baseCurrency);

            return this;
        },


        selectPaymentMethod: function() {
            this._super();

            this.initBillingAddress();
            this.currencyChange(this, null);

            return true;
        },

        initBillingAddress: function() {
            fullScreenLoader.startLoader();
            var self = this;
            self.address(false);
            var serviceUrl = url.build('bluesnap/vault/getBilling/public_hash/' + self.publicHash);
            storage.post(
                serviceUrl
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    if (response.result == "success") {
                        self.address(response.address);
                    }
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader(true);
                }
            );
        },

        getMaskedCard: function () {
            return this.details.cc_last_4_digits;
        },

        getExpirationDate: function () {
            return this.details.cc_expiry
        },

        getCardType: function () {
            return this.details.cc_type;
        },

        getToken: function() {
            return this.publicHash;
        },

        getIcons: function (type) {
            return window.checkoutConfig.bluesnap_vault.creditcard_images.hasOwnProperty(type) ?
                window.checkoutConfig.bluesnap_vault.creditcard_images[type]
                : false;
        },

        getCountryName: function (countryId) {
            var countryData = customerData.get('directory-data');
            console.log(countryData());
            console.log(countryId);
            return countryData()[countryId] != undefined ? countryData()[countryId].name : '';
        },

        openHostedFields: function() {
            $('#bluesnap_hostedfields').click();
        },

        getCurrencyOptionText: function(item) {
            return item.label + ' (' + item.value + ')';
        },

        getCurrencyValue: function(item) {
            return item.value;
        },

        currencyChange: function(self, event) {
            var serviceUrl = url.build('bluesnap/dcc/currencyswitch/currency/' + self.currentCurrency());
            self.dccPrice(false);
            fullScreenLoader.startLoader();
            storage.post(
                serviceUrl
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    if (response.result == "success") {
                        var price = priceUtils.formatPrice(response.price, response.newPriceFormat);
                        self.dccPrice(price);
                    } else {
                        self.messageContainer.addErrorMessage({message: 'Unable to switch currency, if the problem persists please contact us.'});
                    }
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader(true);
                    self.messageContainer.addErrorMessage({message: "Unable to initialise payment method. Please try again, if the problem persists please contact us."});
                    self.showRetryInit(true);
                }
            );
        },

        isDCCAvailable: function() {
            return this.dccPrice() && this.currentCurrency() != this.baseCurrency();
        },

         placeOrderClick: function() {
             var billingAddress = createBillingAddress(this.address());
             quote.billingAddress(billingAddress);
             this.placeOrder();
         }
    });
});
