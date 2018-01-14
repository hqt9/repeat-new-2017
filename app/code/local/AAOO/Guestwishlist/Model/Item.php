<?php

class AAOO_Guestwishlist_Model_Item extends Varien_Object implements Mage_Catalog_Model_Product_Configuration_Item_Interface {

  protected $_customOptionDownloadUrl = 'guestwishlist/index/downloadCustomOption';
  protected $_options = [];
  protected $_optionsByCode = [];
  protected $_notRepresentOptions = ['info_buyRequest'];

  protected function _compareOptions($options1, $options2) {
    $skipOptions = ['id', 'qty', 'return_url'];
    foreach ($options1 as $code => $value) {
      if (in_array($code, $skipOptions)) {
        continue;
      }
      if (!isset($options2[$code]) || $options2[$code] != $value) {
        return false;
      }
    }
    return true;
  }

  protected function _addOptionCode($option) {
    if (!isset($this->_optionsByCode[$option->getCode()])) {
      $this->_optionsByCode[$option->getCode()] = $option;
    } else {
      Mage::throwException(Mage::helper('sales')->__('An item option with code %s already exists.', $option->getCode()));
    }
    return $this;
  }

  public function getDataForSave() {
    $data = [];
    $data['product_id']  = $this->getProductId();
    $data['wishlist_id'] = $this->getWishlistId();
    $data['added_at']    = $this->getAddedAt() ? $this->getAddedAt() : Mage::getSingleton('core/date')->gmtDate();
    $data['description'] = $this->getDescription();
    $data['store_id']    = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();

    return $data;
  }


  public function getProduct() {
    if (!$this->getProductId()) {
      Mage::throwException(Mage::helper('wishlist')->__('Cannot specify product.'));
    }

    $product = Mage::getModel('catalog/product')
                   ->setStoreId($this->getStoreId())
                   ->load($this->getProductId());

    $product->setCustomOptions($this->_optionsByCode);
    
    return $product;
  }

  public function addToCart(Mage_Checkout_Model_Cart $cart) {
    $product = $this->getProduct();
    $storeId = $this->getStoreId();

    if ($product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
      return false;
    }

    if (!$product->isVisibleInSiteVisibility()) {
      if ($product->getStoreId() == $storeId) {
        return false;
      }
      $urlData = Mage::getResourceSingleton('catalog/url')
                     ->getRewriteByProductStore(array($product->getId() => $storeId));
      if (!isset($urlData[$product->getId()])) {
        return false;
      }
      $product->setUrlDataObject(new Varien_Object($urlData));
      $visibility = $product->getUrlDataObject()->getVisibility();
      if (!in_array($visibility, $product->getVisibleInSiteVisibilities())) {
        return false;
      }
    }

    if (!$product->isSalable()) {
      throw new Mage_Core_Exception(null, self::EXCEPTION_CODE_NOT_SALABLE);
    }

    $buyRequest = $this->getBuyRequest();

    $cart->addProduct($product, $buyRequest);
    if (!$product->isVisibleInSiteVisibility()) {
      $cart->getQuote()->getItemByProduct($product)->setStoreId($storeId);
    }
    return true;
  }

  public function getProductUrl() {
    $product = $this->getProduct();
    $query = [];

    if ($product->getTypeInstance(true)->hasRequiredOptions($product)) {
      $query['options'] = 'cart';
    }

    return $product->getUrlModel()->getUrl($product, array('_query' => $query));
  }

  public function getBuyRequest() {
    $option = $this->getOptionByCode('info_buyRequest');
    $initialData = $option ? unserialize($option->getValue()) : null;

    if ($initialData instanceof Varien_Object) {
      $initialData = $initialData->getData();
    }

    $buyRequest = new Varien_Object($initialData);
    $buyRequest->setOriginalQty($buyRequest->getQty())
               ->setQty($this->getQty() * 1);
    return $buyRequest;
  }

  public function mergeBuyRequest($buyRequest) {
    if ($buyRequest instanceof Varien_Object) {
      $buyRequest = $buyRequest->getData();
    }

    if (empty($buyRequest) || ! is_array($buyRequest)) {
      return $this;
    }

    $oldBuyRequest = $this->getBuyRequest()->getData();
    $sBuyRequest = serialize($buyRequest + $oldBuyRequest);

    $option = $this->getOptionByCode('info_buyRequest');
    if ($option) {
        $option->setValue($sBuyRequest);
    } else {
        $this->addOption([
            'code'  => 'info_buyRequest',
            'value' => $sBuyRequest
        ]);
    }

    return $this;
  }

  public function setBuyRequest($buyRequest) {
    $buyRequest->setId($this->getId());

    $_buyRequest = serialize($buyRequest->getData());
    
    $this->setData('buy_request', $_buyRequest);
    return $this;
  }

  public function isRepresent($product, $buyRequest) {
    if ($this->getProductId() != $product->getId()) {
      return false;
    }

    $selfOptions = $this->getBuyRequest()->getData();

    if (empty($buyRequest) && !empty($selfOptions)) {
        return false;
    }

    if (empty($selfOptions) && !empty($buyRequest)) {
      if (!$product->isComposite()){
          return true;
      } else {
          return false;
      }
    }

    $requestArray = $buyRequest->getData();

    if(!$this->_compareOptions($requestArray, $selfOptions)){
        return false;
    }
    if(!$this->_compareOptions($selfOptions, $requestArray)){
        return false;
    }
    return true;
  }

  public function representProduct($product) {
    $itemProduct = $this->getProduct();
    if ($itemProduct->getId() != $product->getId()) {
      return false;
    }

    $itemOptions = $this->getOptionsByCode();
    $productOptions = $product->getCustomOptions();

    if(!$this->compareOptions($itemOptions, $productOptions)){
      return false;
    }

    if(!$this->compareOptions($productOptions, $itemOptions)){
      return false;
    }

    return true;
  }

  public function compareOptions($options1, $options2) {
    foreach ($options1 as $option) {
      $code = $option->getCode();
      if (in_array($code, $this->_notRepresentOptions )) {
          continue;
      }
      if ( !isset($options2[$code])
          || ($options2[$code]->getValue() === null)
          || $options2[$code]->getValue() != $option->getValue()) {
          return false;
      }
    }
    return true;
  }

  public function setOptions($options) {
    foreach ($options as $option) {
      $this->addOption($option);
    }
    return $this;
  }

  public function getOptions() {
    return $this->_options;
  }

  public function getOptionsByCode() {
    return $this->_optionsByCode;
  }

  public function addOption($option) {
    if (is_array($option)) {
      $option = Mage::getModel('wishlist/item_option')->setData($option)
                    ->setItem($this);
    } else if ($option instanceof AAOO_Guestwishlist_Model_Item_Option) {
      $option->setItem($this);
    } else if ($option instanceof Varien_Object) {
      $option = Mage::getModel('wishlist/item_option')->setData($option->getData())
                    ->setProduct($option->getProduct())
                    ->setItem($this);
    } else {
        Mage::throwException(Mage::helper('sales')->__('Invalid item option format.'));
    }

    $exOption = $this->getOptionByCode($option->getCode());
    if ($exOption) {
        $exOption->addData($option->getData());
    } else {
        $this->_addOptionCode($option);
        $this->_options[] = $option;
    }
    return $this;
  }

  public function removeOption($code) {
    $option = $this->getOptionByCode($code);
    if ($option) {
      $option->isDeleted(true);
    }
    return $this;
  }

  public function getOptionByCode($code) {
    if (isset($this->_optionsByCode[$code]) && !$this->_optionsByCode[$code]->isDeleted()) {
      return $this->_optionsByCode[$code];
    }
    return null;
  }

  public function canHaveQty() {
    $product = $this->getProduct();
    return $product->getTypeId() != Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE;
  }

  public function getCustomDownloadUrl() {
    return $this->_customOptionDownloadUrl;
  }

  public function setCustomDownloadUrl($url) {
    $this->_customOptionDownloadUrl = $url;
  }

  public function getFileDownloadParams() {
    $params = new Varien_Object();
    $params->setUrl($this->_customOptionDownloadUrl);
    return $params;
  }
}