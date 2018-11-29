<?php

namespace Bluesnap\Payment\Gateway\Response;

class TokenHandler extends \Bluesnap\Payment\Gateway\Response\AbstractHandler
{

    public function handle(array $handlingSubject, array $response)
    {
        $location = $response['header']['Location'];
        if ($location) {
            $urlParts = explode('/', $location);
            $token = end($urlParts);
            $handlingSubject['data']->setToken($token);
						$this->session->setData('pftoken',$token);
        } else {
            throw new \Magento\Framework\Exception\NotFoundException(__('Location not found in response header.'));
        }
    }
}
