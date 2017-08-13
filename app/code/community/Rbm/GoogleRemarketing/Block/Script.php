<?php

/**
 * @category   Rbm
 * @package    Rbm_GoogleRemarketing
 * @author     reinaldorock@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Rbm_GoogleRemarketing_Block_Script extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        if('' == $this->getTemplate()){
            $this->setTemplate('');
        }
        
    }


    public function getGooglePageTypeArray(){
        $helper = Mage::helper('rbmGoogleRemarketing');
        $result = [
            'ecomm_pagetype' => $helper->getEcommPageType()
        ];
        $ecommProdId = $helper->getEcommProdId();
        if($ecommProdId) {
            $result['ecomm_prodid'] = $ecommProdId;
        }
        $ecommTotalValue = $helper->getEcommTotalValue();
        if($ecommTotalValue) {
            $result['ecomm_totalvalue'] = $ecommTotalValue;
        }
        
        return $result;
        
//    return [
//        ecomm_prodid: 'REPLACE_WITH_VALUE',
//          ecomm_pagetype: 'REPLACE_WITH_VALUE',
//          ecomm_totalvalue: 'REPLACE_WITH_VALUE'
//    ];  
    }
    public function getGooglePageTypeJson(){
               
    }
    public function getConversionId(){
        return Mage::helper('rbmGoogleRemarketing')->getConversionId();
    }

}
