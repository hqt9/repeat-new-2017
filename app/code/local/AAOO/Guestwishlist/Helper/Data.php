<?php
class AAOO_Guestwishlist_Helper_Data extends Mage_Core_Helper_Abstract {

  protected $_wishlist = NULL;

  public function getWishlist($force=false) {
    if (Mage::getSingleton('customer/session')->isLoggedIn() && $force === false) {
      $this->_wishlist = Mage::helper('wishlist')->getWishlist();
    } else {
      if (Mage::getSingleton('guestwishlist/session')->getWishlist()) {
        $this->_wishlist = unserialize(Mage::getSingleton('guestwishlist/session')->getWishlist());
      } else {
        $this->_wishlist = Mage::getModel('guestwishlist/wishlist');
      }
    }

    return $this->_wishlist;
  }

  public function setWishlist($_wishlist) {
    if (! $_wishlist instanceof AAOO_Guestwishlist_Model_Wishlist) {
      throw new Exception("AAOO_Guestwishlist_Model_Wishlist expected", 1);
    }

    try {
      Mage::getSingleton('guestwishlist/session')->setWishlist(serialize($_wishlist));
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public function saveWishlist($wishlist) {
    try {
      Mage::getSingleton('guestwishlist/session')->setWishlist(serialize($wishlist));
      return true;
    } catch (Exception $e){
      return $e->getMessage();
    }
  }

  public function clearWishlist() {
    Mage::getSingleton('guestwishlist/session')->unsWishlist();
    $this->_wishlist = NULL;
  }

  protected function _getUrlStore($item) {
    $storeId = NULL;
    $product = NULL;
    if ($item instanceof AAOO_Guestwishlist_Model_Item) {
      $product = $item->getProduct();
    } elseif ($item instanceof Mage_Catalog_Model_Product) {
      $product = $item;
    }

    if ($product) {
      if ($product->isVisibleInSiteVisibility()) {
        $storeId = $product->getStoreId();
      } else if ($product->hasUrlDataObject()) {
        $storeId = $product->getUrlDataObject()->getStoreId();
      }
    }
    return Mage::app()->getStore($storeId);
  }

  public function getRemoveUrl($item) {
    return $this->_getUrl('guestwishlist/index/remove', ['item' => $item->getId()]);
  }

  public function getConfigureUrl($item) {
    return $this->_getUrl('guestwishlist/index/configure', ['item' => $item->getId()]);
  }

  public function getAddUrl($item) {
    return $this->getAddUrlWithParams($item);
  }

  public function getAddUrlWithParams($item, array $params=[]) {
    $productId = NULL;
    if ($item instanceof Mage_Catalog_Model_Product) {
      $productId = $item->getEntityId();
    }

    if ($item instanceof AAOO_Guestwishlist_Model_Item) {
      $productId = $item->getProductId();
    }

    if ($productId) {
      $params['product'] = $productId;
      return $this->_getUrlStore($item)->getUrl('guestwishlist/index/add', $params);
    }

    return false;
  }

  public function getAddToCartUrl($item) {
    $urlParamName = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
    $continueUrl  = Mage::helper('core')->urlEncode(
                      Mage::getUrl('*/*/*', [
                          '_current'      => true,
                          '_use_rewrite'  => true,
                          '_store_to_url' => true,
                      ])
                    );

    $urlParamName = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
    $params = [
      'item' => is_string($item) ? $item : $item->getId(),
      $urlParamName => $continueUrl
    ];
  
    return $this->_getUrlStore($item)->getUrl('mgwishlist/index/cart', $params);
  }

  public function defaultCommentString() {
    return Mage::helper('guestwishlist')->__('Please enter your comments ...');
  }
}