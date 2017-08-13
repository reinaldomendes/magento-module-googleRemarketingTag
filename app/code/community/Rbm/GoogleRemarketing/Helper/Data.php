<?php

/**
 * @category   Rbm
 * @package    Rbm_GoogleRemarketing
 * @author     reinaldorock@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Rbm_GoogleRemarketing_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @const string 'is_enabled'  config path
     */
    const XML_CONFIG_IS_ENABLED = 'rbmGoogleRemarketing/config/enabled';

    /**
     * @const string 'conversion_id' config path
     */
    const XML_CONFIG_CONVERSION_ID = 'rbmGoogleRemarketing/config/conversion_id';

    /**
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_IS_ENABLED);
    }

    /**
     * Numeric value of conversion id
     * @return string
     */
    public function getConversionId()
    {
        return Mage::getStoreConfig(self::XML_CONFIG_CONVERSION_ID);
    }

    /**
     * @ecomm_pagetype
     * Indicates the type of page that the tag is on. Valid values:
     * <strong>home</strong>
     *   Used on the home page or landing page of your site.
     * <strong>searchresults</strong>
     *   Used on pages where the results of a user's search are displayed.
     * <strong>category</strong>
     *  Used on pages that list multiple items within a category, for example a page showing all shoes in a given style.
     * <strong>product</strong>
     *   Used on individual product pages.
     * <strong>cart</strong>
     *  Used on the cart/basket/checkout page.
     * <strong>purchase</strong>
     *   Used on the page shown once a user has purchased (and so converted), for example a "Thank You" or confirmation page.
     * <strong>other</strong>
     *   Used where the page does not fit into the other types of page, for example a "Contact Us" or "About Us" page.
     * Example usage:
     *
     * <code>
     *    var google_tag_params = {
     *      ecomm_pagetype: 'searchresults'
     *  };
     * </code>
     *
     *
     * @see https://developers.google.com/adwords-remarketing-tag/parameters
     */
    public function getEcommPageType()
    {
        
        if($this->isCategoryViewPage()){
            return 'category';
        }
        if($this->isProductViewPage()){
            return 'product';
        }        
        
        if($this->isHomePage()){
            return 'homepage';
        }
        
        if($this->isSearchResultsPage()){
            return 'searchresults';
        }        
        if($this->isCartPage()){
            return 'cart';
        }        
        if($this->isPurchasePage()){
            return 'purchase';
        }        
        return 'other'; //default return type
    }

    /**
     * is home?
     * @return bool
     */
    public function isHomePage()
    {
        $flagUseCustom = 'useHomeCustom';
        $configCustom = 'homeCustomPath';
        $defaultComparePath = '/';

        return $this->_isPageByPath($flagUseCustom, $configCustom, $defaultComparePath);
    }
    /**
     * is searchResults page?
     * @return bool
     */
    public function isSearchResultsPage()
    {
        $flagUseCustom = 'useSearchResultsCustom';
        $configCustom = 'searchResultsCustomPath';
        $defaultComparePath = '/catalogsearch/result*';

        return $this->_isPageByPath($flagUseCustom, $configCustom, $defaultComparePath);
    }
    
     /**
     * is cart page?
     * @return bool
     */
    public function isCartPage()
    {
        $flagUseCustom = 'useCartCustom';
        $configCustom = 'cartCustomPath';
        $defaultComparePath = '/checkout/cart*';

        return $this->_isPageByPath($flagUseCustom, $configCustom, $defaultComparePath);
    }
    
      /**
     * is cart page?
     * @return bool
     */
    public function isPurchasePage()
    {
        $flagUseCustom = 'usePurchaseCustom';
        $configCustom = 'purchaseCustomPath';
        $defaultComparePath = '/checkout/onepage/success';

        return $this->_isPageByPath($flagUseCustom, $configCustom, $defaultComparePath);
    }
    
    
     /**
     * is catalogCategory page?
     * @return bool
     */
    public function isCategoryViewPage(){
        $request = Mage::app()->getRequest();
        return $request->getModuleName() == 'catalog' 
                && $request->getControllerName() == 'category'
                && $request->getActionName() == 'view';
                
    }
    
      /**
     * is product view page?
     * @return bool
     */
    public function isProductViewPage(){
        $request = Mage::app()->getRequest();
        return $request->getModuleName() == 'catalog' 
                && $request->getControllerName() == 'product'
                && $request->getActionName() == 'view';
                
    }
    
    

    protected function _isPageByPath($flagCustom, $config, $pathCompareString)
    {
        $path = $this->_getRequestPath();
        if ($this->_getEcommTypeConfigFlag($flagCustom)) {
            $path = $this->_getEcommTypeConfig($config);
        }
        $regex = $this->_makeRegex($pathCompareString);
        return preg_match($regex,$path);
    }
    
    /**
     * convert string/* => '@^string/.*$@'
     * @param string $pathCompareString
     */
    protected function _makeRegex($pathCompareString){
        return '@^'. strtr($pathCompareString,['*' => '.*']) . '$@';
    }

    /**
     * return current request path
     * @return string
     */
    protected function _getRequestPath()
    {
        $request = Mage::app()->getRequest();
        $requestUri = $request->getRequestUri();
        $withoutQueryArr = explode('?', $requestUri);
        $requestPath = current($withoutQueryArr);
        $scriptName = $request->getServer('SCRIPT_NAME');
        $result =  strtr($requestPath, [$scriptName => '/']);
        return  '/' . ltrim($result,'/');
    }

    protected function _getEcommTypeConfig($path)
    {
        return Mage::getStoreConfig('rbmGoogleRemarketing/ecomm_page_type/' . ltrim($path,
                                '/'));
    }

    protected function _getEcommTypeConfigFlag($path)
    {
        return Mage::getStoreConfigFlag('rbmGoogleRemarketing/ecomm_page_type/' . ltrim($path,
                                '/'));
    }

}
