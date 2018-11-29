/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'underscore',
        'ko',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Payment/js/view/payment/cc-form',
        'mage/url',
        'mage/storage',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/model/customer',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/action/select-payment-method'
    ],
    function (
        $,
        _,
        ko,
        paymentService,
        Component,
        url,
        storage,
        fullScreenLoader,
        customer,
        priceUtils,
        selectPaymentMethodAction
    ) {
        'use strict';

        var cardImages = window.checkoutConfig.bluesnap_hostedfields.creditcard_images;
        var mode = window.checkoutConfig.payment.bluesnap_hostedfields.mode;
        var vaultEnabled = window.checkoutConfig.payment.bluesnap_hostedfields.vault_enabled;
        var dccConfig = window.checkoutConfig.payment.bluesnap_dcc;

        return Component.extend({
            defaults: {
                active: false,
                card: {
                    src: cardImages['Generic'],
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
                currentCurrency: '',
                baseCurrency: '',
                currencyOptions: [],
                dccPrice: false,
                saveCard: customer.isLoggedIn() && vaultEnabled, // If the customer is logged in, default this to true. Otherwise turn it off
                isCustomerLoggedIn: customer.isLoggedIn(),
                isVaultEnabled: vaultEnabled,
                isDccEnabled: dccConfig.isActive,
                template: 'Bluesnap_Payment/payment/hosted-fields',
                imports: {
                    onActiveChange: 'active'
                }
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

                if (mode == 'production') {
                    this.underSSL(location.protocol === 'https:');
                } else {
                    this.underSSL(true);
                }

                this.currencyOptions(dccConfig.availableCurrencies);
                this.currentCurrency(dccConfig.selectedCurrency);
                this.baseCurrency(dccConfig.baseCurrency);
                this.currencyChange(this, null);

                return this;
            },

            isActive: function () {
                var active = this.getCode() === this.isChecked();

                this.active(active);

                return active;
            },

            onActiveChange: function(isActive)
            {
                if (!isActive) {
                    return;
                }

                this.creditCardFormVisible(false);
                this.canPlaceOrder(false);
                this.initHostedFields();
                this.currencyChange(this, null);
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
                                "color": "#555"
                            }
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
                        src: cardImages['Generic'],
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
                    this.messageContainer.addErrorMessage({'message': message});
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
                        src: cardImages['Generic'],
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
                    this.messageContainer.addErrorMessage({'message': message});
                }
            },

            hostedFieldsType: function(tagId, cardType) {
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
                var serviceUrl = url.build('bluesnap/hostedfields/token');
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
                            self.messageContainer.addErrorMessage({message: response.message});
                            self.showRetryInit(true);
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

            getCode: function() {
                return 'bluesnap_hostedfields';
            },

            placeOrderClick: function() {
                var self = this;
                fullScreenLoader.startLoader();
                bluesnap.submitCredentials( function(cardData){
                    self.ccType(cardData.ccType);
                    self.ccLast4Digits(cardData.last4Digits);
                    self.ccExpiry(cardData.exp);

                    self.placeOrder();
                    fullScreenLoader.stopLoader();
                });
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'token': this.token(),
                        'cc_type': this.ccType(),
                        'cc_expiry': this.ccExpiry(),
                        'cc_last_4_digits': this.ccLast4Digits(),
                        'save_cc': this.saveCard()
                    }
                };
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
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

            isRadioButtonVisible: ko.computed(function () {
                // If there are any vaulted cards stored, then early exit - we know that we need to show the radio button
                var vaultedCardsCount = _.size(window.checkoutConfig.payment.vault);
                if (vaultedCardsCount >= 1) {
                    return true;
                }

                var paymentMethods = paymentService.getAvailablePaymentMethods();

                var cnt = 0;
                _.each(paymentMethods, function(paymentMethod) {
                    // Ignore bluesnap vault. This is because it always appears in the available payment methods, which can
                    // then leads to a situation where we only have one option (bluesnap credit card) which displays
                    // as a radio button.
                    if (paymentMethod.method != 'bluesnap_vault') {
                        cnt++;
                    }
                });

                if (cnt == 1) {
                    // If bluesnap hostedfields is the only payment method, then select it. This will then trigger the rest
                    // of our logic which retrieves the token and displays the form.
                    selectPaymentMethodAction({
                        'method': 'bluesnap_hostedfields'
                    });
                }

                return cnt !== 1;
            }),

            isDCCAvailable: function() {
                return this.dccPrice() && this.currentCurrency() != this.baseCurrency();
            }
        });
    }
);

