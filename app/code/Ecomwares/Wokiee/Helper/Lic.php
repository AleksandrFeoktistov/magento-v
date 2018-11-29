<?php
namespace Ecomwares\Wokiee\Helper;

class Lic extends \Magento\Framework\App\Helper\AbstractHelper {
    protected $_messageManager;
    protected $configWriter;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->configWriter = $configWriter;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    public function validateLicense() {
        //get license code
        $licCode = $this->scopeConfig->getValue('wokiee_lic/license/lic_code');
        $storeUrl = $this->scopeConfig->getValue('web/unsecure/base_url');

        //validate licenseCode
        $isValid = $this->getApiData($licCode, $storeUrl);
        if ($isValid) {
            if($isValid['code'] > 10) {
                if($isValid['code'] == 21) {
                    $this->_messageManager->addError( $isValid['msg'] );
                }
                return false;
            } else {
                //if valid update status
                $this->configWriter->save('wokiee_lic/license/status',  1);
                return true;
            }
        } else {
            return false;
        }
    }

    public function getApiData($code, $storeUrl)
    {
        ////////////////////////////////////
        // CHANGE THEME ID here
        ////////////////////////////////////
        $url = 'https://verify.tonytemplates.com/verify?lic=' . $code . '&url=' . $storeUrl . '&themeId=17782705';
        $curl = curl_init($url);
        $header = array();
        $header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:41.0) Gecko/20100101 Firefox/41.0';
        $header[] = 'timeout: 60';
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        $responce = curl_exec($curl);
        curl_close($curl);
        $responceDecode = json_decode($responce, true);

        return $responceDecode;
    }

    public function clearLic() {
        $this->configWriter->save('wokiee_lic/license/status', 0);
        $this->configWriter->save('wokiee_lic/license/lic_code', '');
    }

    public function getStatus() {
        return $this->scopeConfig->getValue('wokiee_lic/license/status');
    }
}