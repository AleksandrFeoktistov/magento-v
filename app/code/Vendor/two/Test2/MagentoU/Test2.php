<?php
/**  *  * Copyright © 2017 Magento. All rights reserved.  * See COPYING.txt for license details.
 */

 namespace Vendor\two\Test2\MagentoU;

 class Test2 {
    protected $justAParameter;
    protected $data;
    protected $unit1ProductRepository;

    public function __construct (
  //    \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
  //    \Magento\Catalog\Model\ProductFactory $productFactory,
      // \Magento\Checkout\Model\Session $session,
  //    \Vendor\one\Test\Api\ProductRepositoryInterface $unit1ProductRepository,
      $justAParameter = true,
      array $data
    ) {
      $this->justAParameter = $justAParameter;
      $this->data = $data;
    //  $this->unit1ProductRepository = $unit1ProductRepository;
    }
  }
