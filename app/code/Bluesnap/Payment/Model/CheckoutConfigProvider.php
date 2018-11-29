<?php

namespace Bluesnap\Payment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    protected $assetRepo;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->assetRepo = $assetRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return array(
            'bluesnap_hostedfields' => array(
                'creditcard_images' => array(
                    'Generic' => $this->assetRepo->getUrl('Bluesnap_Payment::images/generic-card.png'),
                    'AmericanExpress' => $this->assetRepo->getUrl('Bluesnap_Payment::images/amex.png'),
                    'DinersClub' => $this->assetRepo->getUrl('Bluesnap_Payment::images/diners.png'),
                    'Discover' => $this->assetRepo->getUrl('Bluesnap_Payment::images/discover.png'),
                    'JCB' => $this->assetRepo->getUrl('Bluesnap_Payment::images/jcb.png'),
                    'MaestroUK' => $this->assetRepo->getUrl('Bluesnap_Payment::images/maestro.png'),
                    'MasterCard' => $this->assetRepo->getUrl('Bluesnap_Payment::images/mastercard.png'),
                    'Visa' => $this->assetRepo->getUrl('Bluesnap_Payment::images/visa.png'),
                    'Solo' => $this->assetRepo->getUrl('Bluesnap_Payment::images/solo.png'),
                    'CarteBleue' => $this->assetRepo->getUrl('Bluesnap_Payment::images/cb.png'),
                    'ChinaUnionPay' => $this->assetRepo->getUrl('Bluesnap_Payment::images/cup.png'),
                )
            ),
            'bluesnap_vault' => array(
                'creditcard_images' => array(
                    'AMEX' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/amex.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                    'JCB' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/jcb.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                    'VISA' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/visa.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                    'MASTERCARD' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/mastercard.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                    'DISCOVER' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/discover.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                    'DINERS' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/diners.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                    'SOLO' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/solo.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                    'MAESTR_UK' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/maestro.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                    'CARTE_BLEUE' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/cb.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                    'CHINA_UNION_PAY' => array(
                        'url' => $this->assetRepo->getUrl('Bluesnap_Payment::images/cup.png'),
                        'width' => '32',
                        'height' => '25'
                    ),
                )
            )
        );
    }
}

