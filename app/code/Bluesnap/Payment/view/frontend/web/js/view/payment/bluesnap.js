/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        var config = window.checkoutConfig.payment,
            hostedFieldsType = 'bluesnap_hostedfields';

        if (config[hostedFieldsType].isActive) {
            rendererList.push(
                {
                    type: hostedFieldsType,
                    component: 'Bluesnap_Payment/js/view/payment/method-renderer/hosted-fields'
                }
            );
        }

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
