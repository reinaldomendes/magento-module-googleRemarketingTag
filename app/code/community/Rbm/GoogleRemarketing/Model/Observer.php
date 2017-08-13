<?php

class Rbm_GoogleRemarketing_Model_Observer{

  /**
  * @event sales_model_service_quote_submit_success
  */
  public function setOrderOnSuccessPage($observer){
        $orderIds = $observer->getEvent()->getOrderIds();        
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $helper = Mage::helper('rbmGoogleRemarketing');
        if ($helper) {
            $currentOrder = Mage::getModel('sales/order')->load(current($orderIds));
            $helper->setCurrentOrder($currentOrder);
        }
  }
}
