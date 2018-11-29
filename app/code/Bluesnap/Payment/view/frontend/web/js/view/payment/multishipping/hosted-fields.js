define(
    [
        'jquery',
        'uiComponent',
        'mage/url',
        'mage/storage',
        'mage/translate',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Ui/js/modal/alert',
        'Magento_Catalog/js/price-utils'
    ],
    function (
        $,
        Component,
        url,
        storage,
        $t,
        fullScreenLoader,
        alert,
        priceUtils
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                active: false,
                card: {
                    src: '',
                    alt: ''
                },
                token: '',
                ccType: '',
                ccExpiry: '',
                ccLast4Digits: '',
                creditCardFormVisible: false,
                canPlaceOrder: false,
                showRetryInit: false,
                underSSL: true,
                saveCard: false,
                isCustomerLoggedIn: false,
                isVaultEnabled: false,
                currentCurrency: '',
                baseCurrency: '',
                currencyOptions: [],
                dccPrice: false,
                isDccEnabled: false
            },

            /**
             * Set list of observable attributes
             *
             * @returns {exports.initObservable}
             */
            initObservable: function () {
                this._super()
                    .observe([
                        'active',
                        'underSSL',
                        'card',
                        'token',
                        'ccType',
                        'ccExpiry',
                        'ccLast4Digits',
                        'creditCardFormVisible',
                        'canPlaceOrder',
                        'showRetryInit',
                        'saveCard',
                        'underSSL',
                        'currencyOptions',
                        'currentCurrency',
                        'baseCurrency',
                        'dccPrice',
                        'isDccEnabled'
                    ]);

                if (this.paymentConfig.bluesnap_hostedfields.mode == 'production') {
                    this.underSSL(location.protocol === 'https:');
                } else {
                    this.underSSL(true);
                }

                this.currencyOptions(this.paymentConfig.bluesnap_dcc.availableCurrencies);
                this.currentCurrency(this.paymentConfig.bluesnap_dcc.selectedCurrency);
                this.baseCurrency(this.paymentConfig.bluesnap_dcc.baseCurrency);
                this.currencyChange(this, null);
                this.isDccEnabled(this.paymentConfig.bluesnap_dcc.isActive);

                this.initHostedFields();

                var self = this;
                var followThrough = false;
                $('#multishipping-billing-form').on('submit', function(e) {
                    var form = this;
                    var selected = $('input[name="payment[method]"]:checked').val();
                    if (selected == 'bluesnap_hostedfields') {
                        if (!followThrough) {
                            e.preventDefault();
                            bluesnap.submitCredentials(function(cardData){
                                self.ccType(cardData.ccType);
                                self.ccLast4Digits(cardData.last4Digits);
                                self.ccExpiry(cardData.exp);

                                followThrough = true;
                                form.submit();
                                fullScreenLoader.stopLoader();
                            });
                        }
                    }
                });

                return this;
            },

            initHostedFields: function() {
                var self = this;
                self.getToken(function(token) {
                    self.token(token);
                    self.creditCardFormVisible(true);
                    self.canPlaceOrder(true);
                    self.showRetryInit(false);

                    var bsObj = {
                        hostedPaymentFields: {
                            ccn: "ccn", // name cannot contain spaces or special characters
                            cvv: "cvv", // name cannot contain spaces or special characters
                            exp: "exp"  // name cannot contain spaces or special characters
                        },
                        onFieldEventHandler: {
                            //tagId returns: "ccn", "cvv", "exp"
                            onFocus: function(tagId) {}, // Handle focus
                            onBlur: function(tagId) {}, // Handle blur
                            onError: self.hostedFieldsError.bind(self),
                            onEmpty: self.hostedFieldsEmpty.bind(self),
                            onType: self.hostedFieldsType.bind(self),
                            onValid: self.hostedFieldsValid.bind(self)
                        },
                        style: {
                            "input": {
                                "font-size": "16px",
                                "font-family": "Helvetica Neue,Helvetica,Arial,sans-serif",
                                "line-height": "1.42857143",
                                "color": "#555" }/* ,
                             ".valid": {
                             "color": "green"
                             },
                             ".invalid": {
                             "color": "red"
                             }
                             */
                        },
                        ccnPlaceHolder: "1234 5678 9012 3456", //for example
                        cvvPlaceHolder: "123", //for example
                        expPlaceHolder: "MM/YYYY" //for example
                    };
                    bluesnap.hostedPaymentFieldsCreation(token, bsObj);
                });
            },

            hostedFieldsError: function(tagId, errorCode) {
                fullScreenLoader.stopLoader();
                var message = false;
                if (errorCode == '001') {
                    // Invalid card number
                    $('#ccn-validation-invalid').show();
                    $('#ccn-validation-empty').hide();
                    $('div[data-bluesnap="ccn"]').parent().addClass('validation-failed');
                    this.card({
                        src: this.cardImages['Generic'],
                        alt: ""
                    })
                } else if (errorCode == '002') {
                    // Invalid CVV
                    $('#cvv-validation-invalid').show();
                    $('#cvv-validation-empty').hide();
                    $('div[data-bluesnap="cvv"]').parent().addClass('validation-failed');
                } else if (errorCode == '003') {
                    // Invalid expiry date
                    $('#exp-validation-invalid').show();
                    $('#exp-validation-empty').hide();
                    $('div[data-bluesnap="exp"]').parent().addClass('validation-failed');
                } else if (errorCode == '004') {
                    message = "Session expired please restart your checkout process.";
                    this.creditCardFormVisible(false);
                    this.canPlaceOrder(false);
                    this.showRetryInit(true);
                } else if (errorCode == '005') {
                    message = "Internal server error please try again later";
                } else {
                    message = "An unknown error has occurred. Please try again later.";
                }

                if (message) {
                    alert({'content': message});
                }
            },

            hostedFieldsEmpty: function(tagId, errorCode) {
                fullScreenLoader.stopLoader();
                var message = false;
                var el = false;
                if (errorCode == '001') {
                    // Invalid card number
                    $('#ccn-validation-empty').show();
                    $('#ccn-validation-invalid').hide();
                    el = $('div[data-bluesnap="ccn"]');
                    el.parent().addClass('validation-failed');
                    el.parent().removeClass('validation-passed');
                    this.card({
                        src: this.cardImages['Generic'],
                        alt: ""
                    })
                } else if (errorCode == '002') {
                    // Invalid CVV
                    $('#cvv-validation-empty').show();
                    $('#cvv-validation-invalid').hide();
                    el = $('div[data-bluesnap="cvv"]');
                    el.parent().addClass('validation-failed');
                    el.parent().removeClass('validation-passed');
                } else if (errorCode == '003') {
                    // Invalid expiry date
                    $('#exp-validation-empty').show();
                    $('#exp-validation-invalid').hide();
                    el = $('div[data-bluesnap="exp"]');
                    el.parent().addClass('validation-failed');
                    el.parent().removeClass('validation-passed');
                } else if (errorCode == '004') {
                    message = "Session expired please restart your checkout process.";
                    this.creditCardFormVisible(false);
                    this.canPlaceOrder(false);
                    this.showRetryInit(true);
                } else if (errorCode == '005') {
                    message = "Internal server error please try again later";
                } else {
                    message = "An unknown error has occurred. Please try again later.";
                }

                if (message) {
                    alert({'content': message});
                }
            },

            hostedFieldsType: function(tagId, cardType) {
                var cardImages = this.cardImages;
                // cardType will give card type, and only applies to ccn: CarteBleue, Visa, MasterCard, AmericanExpress, Discover, DinersClub, JCB, Solo, MaestroUK, ChinaUnionPay
                if (cardImages[cardType]) {
                    this.card({
                        src: cardImages[cardType],
                        alt: cardType
                    });
                } else {
                    this.card({
                        src: cardImages['Generic'],
                        alt: cardType
                    })
                }
            },

            hostedFieldsValid: function(tagId) {
                $('#'+tagId+'-validation-empty').hide();
                $('#'+tagId+'-validation-invalid').hide();
                var el = $('div[data-bluesnap="'+tagId+'"]');
                el.parent().addClass('validation-passed');
                el.parent().removeClass('validation-failed');
            },

            getToken: function(onSuccess) {
                var serviceUrl = this.tokenUrl;
                var self = this;
                fullScreenLoader.startLoader();
                storage.post(
                    serviceUrl
                ).done(
                    function (response) {
                        fullScreenLoader.stopLoader();
                        if (response.result == "success") {
                            onSuccess(response.token);
                        } else {
                            alert({content: response.message});
                            self.showRetryInit(true);
                        }
                    }
                ).fail(
                    function (response) {
                        fullScreenLoader.stopLoader(true);
                        alert({content: "Unable to initialise payment method. Please try again, if the problem persists please contact us."});
                        self.showRetryInit(true);
                    }
                );
            },

            getCode: function() {
                return this.code;
            },

            getCvvImageHtml: function() {
                return '<img src="' + this.getCvvImageUrl() +
                    '" alt="' + $t('Card Verification Number Visual Reference') +
                    '" title="' + $t('Card Verification Number Visual Reference') +
                    '" />';
            },

            getCvvImageUrl: function () {
                return this.paymentConfig.ccform.cvvImageUrl[this.getCode()];
            },

            getCurrencyOptionText: function(item) {
                return item.label + ' (' + item.value + ')';
            },

            getCurrencyValue: function(item) {
                return item.value;
            },

            currencyChange: function(self, event) {
                var serviceUrl = self.currencySwitchUrl + 'currency/' + self.currentCurrency();
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
                            alert({content: 'Unable to switch currency, if the problem persists please contact us.'});
                        }
                    }
                ).fail(
                    function (response) {
                        fullScreenLoader.stopLoader(true);
                        alert({content: "Unable to initialise payment method. Please try again, if the problem persists please contact us."});
                    }
                );
            },

            isDCCAvailable: function() {
                return this.dccPrice() && this.currentCurrency() != this.baseCurrency();
            }
        });
    }
);
