<?php

class Rbm_GoogleRemarketing_Model_System_Config_Source_ProductIdentifier{
  const PRODUCT_ID = 'id';
  const PRODUCT_SKU = 'sku';
  protected $_options = array(
      array(
        'label' => 'Product Id',
        'value' => self::PRODUCT_ID,
    ),
    array(
      'label' => 'Product Sku',
      'value' => self::PRODUCT_SKU
    ),

  );

  public function toOptionArray(){
    return $this->_options;
  }
}
