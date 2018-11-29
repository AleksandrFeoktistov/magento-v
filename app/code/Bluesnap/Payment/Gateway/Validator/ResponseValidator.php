<?php
namespace Bluesnap\Payment\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Framework\Webapi\Rest\Response as RestResponse;
use Magento\Store\Model\ScopeInterface;

class ResponseValidator extends AbstractValidator
{
    protected $response;
    protected $messageManager;
    protected $state;
    protected $scopeConfig;
    protected $config;
    protected $transportBuilder;
    protected $storeManager;
    protected $registry;
    protected $logger;

    public function __construct(
        \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        RestResponse $response,
        \Bluesnap\Payment\Gateway\Config\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger
    )
    {
        parent::__construct($resultFactory);
        $this->messageManager = $messageManager;
        $this->response = $response;
        $this->state = $state;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->logger = $logger;
    }

    public function validate(array $validationSubject)
    {
        $isValid = true;
        $fails = array();
        $response = $validationSubject['response'];
        $header = $response['header'];
        $rawBody = 'Body empty.';
        if (isset($response['raw_body'])) {
            $rawBody = $response['raw_body'];
        }

        if (!(in_array($header['http_code'], array(200, 201, 204)))) {
            $isValid = false;

            // If the response has some valid XML in the body
            if (isset($response['body']) && $response['body'] ) {
                switch ($response['body']->getName()) {
                    // If the response has come back with a list of messages, then set them as our exception message.
                    case 'messages':
                        $body = $response['body'];
                        $messages = $this->_extractMessages($body);
                        if($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_WEBAPI_REST) {
                            /**
                             * This is an important line.
                             *
                             * Normally we'd throw an exception at this point to detail exactly what went wrong in the request,
                             * especially if it's something the customer can correct. If we do this, because
                             * \Magento\Payment\Gateway\Command\CommandException:101 throws a generic message, and then a similar thing
                             * happens above that in \Magento\Checkout\Model\GuestPaymentInformationManagement::savePaymentInformationAndPlaceOrder,
                             * we lose the reference of our exception, except for a note in system.log.
                             *
                             * A work around is to set the exception directly against the request. \Magento\Framework\Webapi\Rest\Response::_renderMessages
                             * is the point that this error message gets passed back as JSON to the frontend. We can take advantage
                             * of this by basically ensuring that is our exception. However to achieve this we need to make sure
                             * it's the last one in the list. Magento basically will just loop over all exceptions, but overwrites
                             * the message each time. We achieve this via an observer, see: \Bluesnap\Payment\Observer\ChangeExceptionMessage::execute
                             */
                            $this->response->setException(new \Bluesnap\Payment\Model\Exception(__(implode(' ', $messages))));
                        } else {
                            $this->messageManager->addErrorMessage(__(implode(' ', $messages)));
                        }
                        break;
                    // If it comes in the error-message format
                    case 'error-message':
                        if($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_WEBAPI_REST) {
                            // This is an important line, see above for an explanation.
                            $this->response->setException(new \Bluesnap\Payment\Model\Exception(__($response['body']->{'description'})));
                        } else {
                            $this->messageManager->addErrorMessage(__($response['body']->{'description'}));
                        }
                        break;

                    default:
                        // No-op, we'll allow Magento to add the generic failure message
                        break;
                }
            }

            if ($this->config->getDebug() && $errorRecipient = $this->config->getErrorRecipientEmail()) {
                try {
                    $this->sendErrorEmail($validationSubject);
                } catch (\Exception $e) {
                    $this->logger->error('Failed to send error email. ' . $e->getMessage());
                }
            }

            // Log the raw body to the system.log
            $fails []= sprintf('Request returned a response with a non-200 response code. Body content: %s', $rawBody);
        }

        return $this->createResult($isValid, $fails);
    }

    /**
     * @param $body
     * @return array
     */
    protected function _extractMessages($body)
    {
        $messages = array();

        foreach ($body->{'message'} as $message) {
            switch ((string)$message->{'error-name'}) {
                case 'INSUFFICIENT_FUNDS' && $message->{'code'} == '14002':
                case 'GENERAL_PAYMENT_PROCESSING_ERROR' && $message->{'code'} == '14002':
                    $description = 'Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.';
                    break;
                case 'PAYMENT_GENERAL_FAILURE' && $message->{'code'} == '10000':
                    $description = 'A general payment failure has occurred.';
                    break;
                case 'CALL_ISSUER' && $message->{'code'} == '14002':
                    $description = 'Payment processing failure due to an unspecified error. Please contact the issuing bank.';
                    break;
                case 'PROCESSING_GENERAL_DECLINE' && $message->{'code'} == '14002':
                    $description = 'Payment processing failure due to an unspecified error returned. Retry the transaction and if problem continues contact the issuing bank.';
                    break;
                case 'THE_ISSUER_IS_UNAVAILABLE_OR_OFFLINE' && $message->{'code'} == '14002':
                    $description = 'Payment processing failure because the issuer is unavailable or offline.';
                    break;
                case 'PARTIAL_REFUND_CREATED_LESS_THAN_24_HOURS_AGO' && $message->{'code'} == '14020':
                case 'INVOICE_ALREADY_FULLY_REFUNDED' && $message->{'code'} == '14022':
                case 'REFUND_IN_PROCESS' && $message->{'code'} == '14023':
                case 'PARTIAL_REFUND_NOT_SUPPORTED' && $message->{'code'} == '14024':
                    $description = $message->description . ' Please contact merchants@bluesnap.com for further assistance.';
                    break;
                case 'FRAUD_DETECTED' && $message->{'code'} == '15011':
                    $storeName = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    $storeName = $storeName ? $storeName : 'the merchant';
                    $description = 'The request cannot be fulfilled for the current shopper. Please contact ' . $storeName . ' for further details.';
                    break;
                case 'NO_AVAILABLE_PROCESSORS':
                    $description = 'Unfortunately your selected Card brand and Currency combination is not available. Please try a different card or contact our support team';
                    break;
                case 'INCORRECT_INFORMATION':
                    $description = 'Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.';
                    break;
                case 'VALIDATION_GENERAL_FAILURE':
                    // We need to return a custom validation message if the user inputs an state which is not supported by Bluesnap.
                    if (isset($message->{'invalid-property'})) {
                        $invalidProperty = $message->{'invalid-property'};
                        if ($invalidProperty->{'name'} == 'state') {
                            $messages = array('Invalid State input. Please note, we do not support \'Federated states of Micronesia\', \'Marshall Islands\', \'Northern Mariana Islands\' or \'palau\'');
                            return $messages;
                        }
                    }
                    // The error message is different if the user is a returning shopper. We must also cater to this case.
                    if (strpos($message->{'description'}, 'Invalid State provided') !== false) {
                        $messages = array('Invalid State input. Please note, we do not support \'Federated states of Micronesia\', \'Marshall Islands\', \'Northern Mariana Islands\' or \'palau\'');
                        return $messages;
                    }

                    if ($message->{'code'} == '10001' && substr( $message->{'description'}, 0, 15 ) === "This card brand") {
                        return array('Your payment could not be processed at this time as this card brand is not supported. Please select a new payment method and try again.');
                    }

                    if ($message->{'code'} == '10001') {
                        $message10001 = array('Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.');
                    } else {
                        $description = (string)$message->description;
                    }
                    break;
                default:
                    $description = (string)$message->description;
            }

            if(isset($message10001)) continue;
            if (!in_array($description, $messages)) {
                $messages [] = $description;
            }
        }
        if(isset($message10001)) return $message10001;
        return $messages;
    }

    protected function prettifyArray($array)
    {
        $result = [];
        foreach ($array as $key => $value)
        {
            $result []= sprintf('%s: %s', $key, $value);
        }
        return implode("\n", $result);
    }

    protected function prettifyXml($xml)
    {
        try {
            $parsedXml = simplexml_load_string($xml);
            $dom = dom_import_simplexml($parsedXml)->ownerDocument;
            $dom->formatOutput = true;
            return $dom->saveXML();
        } catch (\Exception $e) {
            return $xml;
        }
    }

    /**
     * @param array $validationSubject
     */
    protected function sendErrorEmail(array $validationSubject)
    {
        $request = $this->registry->registry('request_detail');
        $response = $validationSubject['response'];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('bluesnap_debug_error_email_template')
            ->setTemplateOptions(array(
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()
            ))
            ->setTemplateVars(array(
                'request_url' => $request['url'],
                'request_method' => $request['method'],
                'request_headers' => $this->prettifyArray($request['headers']),
                'request_xml' => $this->prettifyXml($request['body']),
                'response_headers' => $response['raw_header'],
                'response_xml' => $this->prettifyXml($response['raw_body'])
            ))
            ->setFrom(array(
                "name" => $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE),
                "email" => $this->scopeConfig->getValue('trans_email/ident_support/name', ScopeInterface::SCOPE_STORE)
            ))
            ->addTo(
                $this->scopeConfig->getValue('payment/bluesnap_hostedfields/error_recipient', ScopeInterface::SCOPE_STORE)
            )
            ->getTransport();

        $transport->sendMessage();
    }
}
