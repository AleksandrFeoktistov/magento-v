define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/view/utils/dom-observer',
    'mage/translate',
    'mage/url',
    'mage/storage'
], function ($, Class, alert, domObserver, $t, url, storage) {
    'use strict';
    var tokenStore;
    return Class.extend({

        defaults: {
            $selector: null,
            selector: 'edit_form',
            container: 'payment_form_bluesnap_hostedfields',
            active: false,
            initialise: false,
            token: '',
            ccType: '',
            ccExpiry: '',
            ccLast4Digits: '',
            card: {
                src: '',
                alt: ''
            },
            bluesnap: null,
            imports: {
                onActiveChange: 'active'
            }
        },

        /**
         * Set list of observable attributes
         * @returns {exports.initObservable}
         */
        initObservable: function () {
            var self = this;
            self.initialise = true;

            self.$selector = $('#' + self.selector);
            this._super()
                .observe([
                    'active',
                    'token',
                    'ccType',
                    'ccExpiry',
                    'ccLast4Digits',
                    'card'
                ]);

            // re-init payment method events
            self.$selector.off('changePaymentMethod.' + this.code)
                .on('changePaymentMethod.' + this.code, this.changePaymentMethod.bind(this));

            self.token.subscribe(function(val) { $('#bluesnap_hosted_fields_token').val(val); });
            self.ccType.subscribe(function(val) { $('#bluesnap_hosted_fields_cc_type').val(val); });
            self.ccExpiry.subscribe(function(val) { $('#bluesnap_hosted_fields_cc_expiry').val(val); });
            self.ccLast4Digits.subscribe(function(val) { $('#bluesnap_hosted_fields_cc_last_4_digits').val(val); });

            self.card.subscribe(function(val) {
                var element = $('#bluesnap_hosted_fields_cctype_icon');
                element.attr('src', val.src);
                element.attr('alt', val.alt);
            });

            // listen block changes
            domObserver.get('#' + self.container, function () {
                if (!self.initialise) {
                    self.initHostedFields();
                }
            });


            if (this.mode == "production" && location.protocol !== 'https:') {
                $('#invalid-ssl-message').show();
                $('#payment_form_bluesnap_hostedfields .admin__field').hide();
            }

            self.initialise = false;
            return this;
        },

        /**
         * Enable/disable current payment method
         * @param {Object} event
         * @param {String} method
         * @returns {exports.changePaymentMethod}
         */
        changePaymentMethod: function (event, method) {
            this.active(method === this.code);

            return this;
        },

        /**
         * Triggered when payment changed
         * @param {Boolean} isActive
         */
        onActiveChange: function (isActive) {
            if (!isActive) {
                this.$selector.off('submitOrder.bluesnap_hostedfields');

                return;
            }
            this.disableEventListeners();
            window.order.addExcludedPaymentMethod(this.code);
            this.enableEventListeners();

            this.initHostedFields();
        },

        initHostedFields: function() {
            var self = this;
            self.getToken(function (token) {
                self.token(token);

                var bsObj = {
                    hostedPaymentFields: {
                        ccn: "ccn", // name cannot contain spaces or special characters
                        cvv: "cvv", // name cannot contain spaces or special characters
                        exp: "exp"  // name cannot contain spaces or special characters
                    },
                    onFieldEventHandler: {
                        //tagId returns: "ccn", "cvv", "exp"
                        onFocus: function (tagId) {
                        }, // Handle focus
                        onBlur: function (tagId) {
                        }, // Handle blur
                        onError: self.hostedFieldsError.bind(self),
                        onEmpty: self.hostedFieldsEmpty.bind(self),
                        onType: self.hostedFieldsType.bind(self),
                        onValid: self.hostedFieldsValid.bind(self)
                    },
                    style: {
                        "input": {
                            "font-size": "14px",
                            "font-family": "Helvetica Neue,Helvetica,Arial,sans-serif",
                            "line-height": "1.42857143",
                            "color": "#303030"
                        },
                        ".valid": {
                            "color": "#14ba57"
                        },
                        ".invalid": {
                            "color": "#ee7d7d"
                        }
                    },
                    ccnPlaceHolder: "1234 5678 9012 3456", //for example
                    cvvPlaceHolder: "123", //for example
                    expPlaceHolder: "MM/YYYY" //for example
                };
                bluesnap.hostedPaymentFieldsCreation(token, bsObj);

                // Init the card image to the generic one
                self.card({src: self.cc_images['Generic'], alt: ''});

            });
        },

        hostedFieldsError: function(tagId, errorCode) {
            $('body').trigger('processStop');
            var message = false;
            if (errorCode == '001') {
                // Invalid card number
                $('#ccn-validation-invalid').show();
                $('#ccn-validation-empty').hide();
                $('div[data-bluesnap="ccn"]').addClass('validation-failed');
                this.card({
                    src: this.cc_images['Generic'],
                    alt: ""
                })
            } else if (errorCode == '002') {
                // Invalid CVV
                $('#cvv-validation-invalid').show();
                $('#cvv-validation-empty').hide();
                $('div[data-bluesnap="cvv"]').addClass('validation-failed');
            } else if (errorCode == '003') {
                // Invalid expiry date
                $('#exp-validation-invalid').show();
                $('#exp-validation-empty').hide();
                $('div[data-bluesnap="exp"]').addClass('validation-failed');
            } else if (errorCode == '004') {
                message = "Session expired please restart your checkout process.";
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
            $('body').trigger('processStop');
            var message = false;
            var el = false;
            if (errorCode == '001') {
                // Invalid card number
                $('#ccn-validation-empty').show();
                $('#ccn-validation-invalid').hide();
                el = $('div[data-bluesnap="ccn"]');
                el.addClass('validation-failed');
                el.removeClass('validation-passed');
                this.card({
                    src: this.cc_images['Generic'],
                    alt: ""
                })
            } else if (errorCode == '002') {
                // Invalid CVV
                $('#cvv-validation-empty').show();
                $('#cvv-validation-invalid').hide();
                el = $('div[data-bluesnap="cvv"]');
                el.addClass('validation-failed');
                el.removeClass('validation-passed');
            } else if (errorCode == '003') {
                // Invalid expiry date
                $('#exp-validation-empty').show();
                $('#exp-validation-invalid').hide();
                el = $('div[data-bluesnap="exp"]');
                el.addClass('validation-failed');
                el.removeClass('validation-passed');
            } else if (errorCode == '004') {
                message = "Session expired please restart your checkout process.";
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
            if (this.cc_images[cardType]) {
                this.card({
                    src: this.cc_images[cardType],
                    alt: cardType
                });
            } else {
                this.card({
                    src: this.cc_images['Generic'],
                    alt: cardType
                })
            }
        },

        hostedFieldsValid: function(tagId) {
            $('#'+tagId+'-validation-empty').hide();
            $('#'+tagId+'-validation-invalid').hide();
            var el = $('div[data-bluesnap="'+tagId+'"]');
            el.addClass('validation-passed');
            el.removeClass('validation-failed');
        },

        getToken: function(onSuccess) {
            if(typeof tokenStore != 'undefined'){
                onSuccess(tokenStore);
                return;
            }
            var serviceUrl = this.token_url;
            var self = this;
            $('body').trigger('processStart');
            storage.get(
                serviceUrl
            ).done(
                function (response) {
                    $('body').trigger('processStop');
                    if (response.result == "success") {
                        tokenStore = response.token;
                        onSuccess(response.token);
                    } else {
                        alert({content: response.message});
                    }
                }
            ).fail(
                function (response) {
                    $('body').trigger('processStop');
                    alert({content: "Unable to initialise payment method. Please try again, if the problem persists please contact us."});

                }
            );
        },

        /**
         * Show alert message
         * @param {String} message
         */
        error: function (message) {
            alert({
                content: message
            });
        },

        /**
         * Enable form event listeners
         */
        enableEventListeners: function () {
            this.$selector.on('submitOrder.bluesnap_hostedfields', this.submitOrder.bind(this));
        },

        /**
         * Disable form event listeners
         */
        disableEventListeners: function () {
            this.$selector.off('submitOrder');
            this.$selector.off('submit');
        },


        /**
         * Trigger order submit
         */
        submitOrder: function () {
            var self = this;
            self.$selector.validate().form();
            self.$selector.trigger('afterValidate.beforeSubmit');
            $('body').trigger('processStop');

            // validate parent form
            if (self.$selector.validate().errorList.length) {
                return false;
            }

            bluesnap.submitCredentials( function(cardData){
                self.ccType(cardData.ccType);
                self.ccLast4Digits(cardData.last4Digits);
                self.ccExpiry(cardData.exp);

                self.placeOrder();
            });
        },

        /**
         * Place order
         */
        placeOrder: function () {
            $('#' + this.selector).trigger('realOrder');
        },
    });
});
