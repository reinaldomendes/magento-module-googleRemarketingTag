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
            $this->setTemplate('rbm/google-remarketing/script.phtml');
        }

    }


    public function getGoogleTagParams(){
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
        $ecommCategory =  $helper->getEcommCategory();
        if($ecommCategory){
           $result['ecomm_category'] = $ecommCategory;
        }
        
        

        return $result;

//    return [
//        ecomm_prodid: 'REPLACE_WITH_VALUE',
//          ecomm_pagetype: 'REPLACE_WITH_VALUE',
//          ecomm_totalvalue: 'REPLACE_WITH_VALUE'
//    ];
    }
    public function getGoogleTagParamsJson(){        
        return json_encode($this->getGoogleTagParams());
    }
    /**
     * 
     * @return string encoded url '&amp;ecomm_type=xyz&amp;....'
     */
    public function getGoogleTagParamsUri(){
        $array = $this->getGoogleTagParams();
        $str = '';
        foreach($array as $k => $v){
            $encodedValue = urlencode($v);
            $str="&amp;data.{$k}={$encodedValue}";
        }
        return $str;
    }
    
    /**
     * 
     * Google conversion Id
     * @return string numeric
     */
    public function getConversionId(){
        return Mage::helper('rbmGoogleRemarketing')->getConversionId();
    }
    
    /**
     * is debug enabled?
     * @return boolean
     */
    public function isDebugEnabled(){
        return Mage::helper('rbmGoogleRemarketing')->isDebugEnabled();
    }
    
    protected function _toHtml()
    {
        if(Mage::helper('rbmGoogleRemarketing')->isEnabled()){
            return parent::_toHtml();
        }
        return null;
    }

}
