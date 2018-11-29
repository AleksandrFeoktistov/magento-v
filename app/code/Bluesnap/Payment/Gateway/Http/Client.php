<?php
namespace Bluesnap\Payment\Gateway\Http;

use Magento\Payment\Gateway\Http\ClientInterface;

/**
 * This class actually implements the CURL request. I would use Magento\Payment\Gateway\Http\Client\Zend however that doesn't
 * offer any request other than GET or POST (and I need PUT).
 *
 * Class Client
 * @package Bluesnap\Payment\Gateway\Http
 */
class Client implements ClientInterface
{
    protected $logger;

    public function __construct(
        \Magento\Payment\Model\Method\Logger $logger
    ) {
        $this->logger = $logger;
    }


    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     * @return array
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     */
    public function placeRequest(\Magento\Payment\Gateway\Http\TransferInterface $transferObject)
    {
        $log = array();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $transferObject->getUri());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $transferObject->getHeaders());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $transferObject->getMethod());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Don't output to screen
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);

        switch ($transferObject->getMethod()) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $transferObject->getBody());
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_POSTFIELDS, $transferObject->getBody());
                break;
        }

        try {
            $rawResponse = curl_exec($ch);

            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);

            $log['request'] = $verboseLog;

            if ($requestBody = $transferObject->getBody()) {
                $requestBody = simplexml_load_string($requestBody);
                $log['request_body'] = $this->formatXml($requestBody);
            }

            if (!$rawResponse) {
                throw new \Magento\Payment\Gateway\Http\ClientException(__('Failed to make API request. Reason: ' . curl_error($ch)));
            }

            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $responseHeader = substr($rawResponse, 0, $headerSize);

            $response = array();
            $response['raw_header'] = $responseHeader;
            $response['header'] = $this->parseHeaders($responseHeader);

            $responseBody = substr($rawResponse, $headerSize);
            if ($responseBody) {
                try {
                    $bodyXml = simplexml_load_string($responseBody);
                    $log['response_body'] = $this->formatXml($bodyXml);
                    $response['body'] = $bodyXml;
                } catch (\Exception $e) {
                    // It isn't xml
                    $log['response_body'] = $responseBody;
                }

                $response['raw_body'] =$responseBody;
            }

            curl_close($ch);

            return $response;
        } catch (\Exception $e) {
            throw new \Magento\Payment\Gateway\Http\ClientException(
                __($e->getMessage())
            );
        } finally {
            $this->logger->debug($log);
        }
    }

    protected function parseHeaders($headerText)
    {
        $headers = array();
        foreach (explode("\r\n", $headerText) as $i => $line) {
            if (!$line) {
                continue;
            }
            if ($i === 0) {
                $headers['http_status'] = $line;
                $parts = explode(' ', $line);
                $headers['http_code'] = intval($parts[1]);
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }
        return $headers;

    }

    protected function formatXml($xml)
    {
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML();
    }
}
