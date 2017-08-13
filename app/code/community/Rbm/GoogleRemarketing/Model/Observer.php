<?php

class Rbm_GoogleRemarketing_Model_Observer{

  /**
  * @event sales_model_service_quote_submit_success
  */
  public function quoteSubmitSuccess($evt){
    $order = $evt->getOrder();
    Mage::helper('rbmGoogleRemarketing')->setCurrentOrder($order);
  }
}
