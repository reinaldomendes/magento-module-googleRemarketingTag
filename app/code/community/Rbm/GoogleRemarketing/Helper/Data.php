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
     * @const string 'debug' config path
     */
    const XML_CONFIG_DEBUG_IS_ENABLED = 'rbmGoogleRemarketing/config/debug';
    
    /**
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_currentOrder = null;

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
     * 
     */
    public function isDebugEnabled(){
         return Mage::getStoreConfigFlag(self::XML_CONFIG_DEBUG_IS_ENABLED);
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

        if ($this->isCategoryViewPage()) {
            return 'category';
        }
        if ($this->isProductViewPage()) {
            return 'product';
        }

       

        if ($this->isSearchResultsPage()) {
            return 'searchresults';
        }
        if ($this->isCartPage()) {
            return 'cart';
        }
        
        
        if ($this->isPurchasePage()) {
            return 'purchase';
        }
        
         if ($this->isHomePage()) {
            return 'home';
        }
        return 'other'; //default return type
    }

    /**
     * Required. This is the product ID of the product or products displayed on the current page - the IDs used here should match the IDs in your GMC feed.
     * 
     * This parameter should be passed when the ecomm_pagetype is product or cart. On product pages you will generally have a single product and so a simple single literal value can be passed; on cart pages if there is more than one product shown (i.e. if the user has more than one product in their cart) then an array of values can be passed. 
     * 
     * Both numeric and alphanumeric values are supported, for example 34592212, '23423-131-12', or 'gp232123-19a', please note that if your product ID is anything other than a number, then you will need to treat it as a string and surround it in quotes.
     *      
     * Example usage on a single product page:
     * <code>
     * var google_tag_params = { 
     *     ecomm_prodid: 34592212
     * };
     * </code>
     * 
     * Example usage on a cart page with more than on product
     * <code> 
     * var google_tag_params = { 
     *     ecomm_prodid: [34592212, '23423-131-12', 'gp232123-19a']
     * };
     * </code>
     * 
     * @return array|string|null
     */
    public function getEcommProdId()
    {
        if ($this->isCartPage()) {
            $quote = $this->_getQuote();
            /* @var $quote Mage_Sales_Model_Quote */
            $result = [];
            foreach ($quote->getAllVisibleItems() as $item) {
                $result[] = $this->_getItemIdentifier($item);
            }
            return $result;
        }        
        if ($this->isProductViewPage()) {
            $currentProduct = Mage::registry('current_product');
            return $this->_getProductIdentifier($currentProduct);
        }     
    }
    
    /**
     * 
     * @return float|null
     */
    public function getEcommTotalvalue(){
        if ($this->isCartPage()) {
            $quote = $this->_getQuote();
            /* @var $quote Mage_Sales_Model_Quote */
            $result = [];
            foreach ($quote->getAllVisibleItems() as $item) {
                /*@var $item Mage_Sales_Model_Quote_Item*/
                $result[] = $item->getPriceInclTax();
            }
            return array_sum($result);
        }
        if($this->isPurchasePage()){
            $result = [];
            $order = $this->getCurrentOrder();
            foreach ($order->getAllVisibleItems() as $item) {
                 /*@var $item Mage_Sales_Model_Order_Item*/
                $result[] = $item->getPriceInclTax();
            }
            return array_sum($result);
        }        
        if ($this->isProductViewPage()) {
            $currentProduct = Mage::registry('current_product');
            return $this->_getProductPrice($currentProduct);
        }        
    }
    
    /**
     * This parameter contains a string specifying the category of the currently viewed product or category pages. The string can be any value and does not need to conform to any specific naming convention.
     *
     *   Example usage for a product on in the "Home & Garden" category
     * <code>
     * var google_tag_params = { 
     *      ecomm_category: 'Home & Garden'
     *  };
     * </code>
     * 
     * @return string|null
     */
    public function getEcommCategory(){
        
        if($this->isCategoryViewPage()){
            return Mage::registry('current_category')->getName();
        }
        
        if($this->isProductViewPage()){        
            try{
                return Mage::registry('current_category')->getName();
            }catch(Exception $e){
                return;
            }
        }
    }
    
    
    /**
     * @see Rbm_GoogleRemarketing_Model_Observer::quoteSubmitSuccess
     * @param Mage_Sales_Model_Order $order
     * @return \Rbm_GoogleRemarketing_Helper_Data
     */
    public function setCurrentOrder(Mage_Sales_Model_Order $order){
        $this->_currentOrder = $order;
        return $this;
    }
    
     
    /**
     * @see Rbm_GoogleRemarketing_Model_Observer::quoteSubmitSuccess
     * @return Mage_Sales_Model_Order
     */
    public function getCurrentOrder(){
        return $this->_currentOrder;
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

        return $this->_isPageByPath($flagUseCustom, $configCustom,
                        $defaultComparePath);
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

        return $this->_isPageByPath($flagUseCustom, $configCustom,
                        $defaultComparePath);
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

        return $this->_isPageByPath($flagUseCustom, $configCustom,
                        $defaultComparePath);
    }

    /**
     * is cart page?
     * @return bool
     */
    public function isPurchasePage()
    {
        $flagUseCustom = 'usePurchaseCustom';
        $configCustom = 'purchaseCustomPath';
        $defaultComparePath = '/checkout/onepage/success/';

        return $this->_isPageByPath($flagUseCustom, $configCustom,
                        $defaultComparePath);
    }

    /**
     * is catalogCategory page?
     * @return bool
     */
    public function isCategoryViewPage()
    {
        $request = Mage::app()->getRequest();
        return $request->getModuleName() == 'catalog'
                && $request->getControllerName() == 'category'
                && $request->getActionName() == 'view';
    }

    /**
     * is product view page?
     * @return bool
     */
    public function isProductViewPage()
    {
        $request = Mage::app()->getRequest();
        return $request->getModuleName() == 'catalog'
                && $request->getControllerName() == 'product'
                && $request->getActionName() == 'view';
    }

    /**
     * 
     * Check if page is the type compared with $pathCompareString or a configured _getEcommTypeConfig($config)?
     * @param string $flagCustom
     * @param string $config
     * @param string $pathCompareString
     * @return boolean
     */
    protected function _isPageByPath($flagCustom, $config, $pathCompareString)
    {
        $path = $this->_getRequestPath();
        if ($this->_getEcommTypeConfigFlag($flagCustom)) {
            $pathCompareString = $this->_getEcommTypeConfig($config);
        }
        $regex = $this->_makeRegex($pathCompareString);

        return preg_match($regex, $path);
    }

    /**
     * convert string/* => '@^string/.*$@'
     * @param string $pathCompareString
     */
    protected function _makeRegex($pathCompareString)
    {
        return '@^' . strtr($pathCompareString, ['*' => '.*']) . '$@';
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
        $result = strtr($requestPath, [$scriptName => '/']);
        return '/' . ltrim($result, '/');
    }

    /**
     * 
     * @param string $path - suffix of rbmGoogleRemarketing/ecomm_page_type/ config
     * @return string
     */
    protected function _getEcommTypeConfig($path)
    {
        $_path = ltrim($path, '/');
        return Mage::getStoreConfig('rbmGoogleRemarketing/ecomm_page_type/' . $_path);
    }

    /**
     * 
     * @param string $path - suffix of rbmGoogleRemarketing/ecomm_page_type/ config
     * @return boolean
     */
    protected function _getEcommTypeConfigFlag($path)
    {
        $_path = ltrim($path, '/');
        return Mage::getStoreConfigFlag('rbmGoogleRemarketing/ecomm_page_type/' . $_path);
    }
    
    

    protected function _getProductIdentifierName()
    {        
        return Mage::getStoreConfig('rbmGoogleRemarketing/config/product_identifier' );
    }

    protected function _getProductIdentifier(Mage_Catalog_Model_Product $product)
    {
        $identifier = $this->_getProductIdentifierName();        
        $result = null;
        switch ($identifier) {
            case Rbm_GoogleRemarketing_Model_System_Config_Source_ProductIdentifier::PRODUCT_SKU:
                $result = $product->getSku();
                break;
            default:
                $result = $product->getId();
                break;
        }
        return $result;
    }

    /**
     * 
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item $item
     * @return null|string|int
     */
    protected function _getItemIdentifier( $item)
    {
        $identifier = $this->_getProductIdentifierName();
        $result = null;
        switch ($identifier) {
            case Rbm_GoogleRemarketing_Model_System_Config_Source_ProductIdentifier::PRODUCT_SKU:
                $id = $this->_getSuperProductIdByItem($item);
                $result = Mage::getModel('catalog/product')->load($id)->getSku();                
                break;
            default:
                $result = $this->_getSuperProductIdByItem($item);
                break;
        }
        return $result;
    }
    
    /**
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote(){
        return Mage::getSingleton('checkout/session')->getQuote();
    }
    
    protected function _getProductPrice(Mage_Catalog_Model_Product $product){     
        
        $price = $product->getFinalPrice();            
        if($product->getTypeId() == 'grouped'){                        
            $_associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);                
            $price = null;
            foreach($_associatedProducts as $_associatedProduct) {
                if(null  == $price) {
                    $price = $_associatedProduct->getPrice();
                }
                $price = min($price,$_associatedProduct->getPrice());
            }
        }
        return round($price,2);
    }
    
    protected function _getSuperProductIdByItem($item){
            /*@var $item Mage_Sales_Model_Order_Item|Mage_Sales_Model_Quote_Item*/            
            $productId = $item->getProductId();
            $buyRequest = $item->getBuyRequest()->getData();
            if(isset($buyRequest['super_product_config'])){
                $productConfig = $buyRequest['super_product_config'];
                if($productConfig['product_id']){
                    $productId = $productConfig['product_id'];
                }
            } 
            return $productId;
    }
    
   

}
