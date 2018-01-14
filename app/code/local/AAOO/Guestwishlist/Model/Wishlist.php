<?php

class AAOO_Guestwishlist_Model_Wishlist extends Varien_Object {
  protected $_itemCollection = null;

  protected $_lastItemId;

  protected function _construct() {
    $this->_itemCollection = [];
    $this->_lastItemId = 0;
  }
  
  protected function _addCatalogProduct(Mage_Catalog_Model_Product $product, $qty = 1, $forciblySetQty = false) {
    $item = null;
    foreach ($this->_itemCollection as $_item) {
      if ($_item->representProduct($product)) {
        $item = $_item;
        break;
      }
    }

    if ($item === null) {
      // Item doesn't exist.
    
      $item = Mage::getModel('guestwishlist/item');
      $itemId = $this->getNextAvailableItemId();
    
      $item->setId($itemId)
           ->setProductId($product->getId())
           ->setAddedAt(now())
           ->setOptions($product->getCustomOptions())
           ->setQty($qty);
            
      $this->_itemCollection[$itemId] = $item;
    
    } else {
      // Item already exists
      $qty = $forciblySetQty ? $qty : $item->getQty() + $qty;
      $item->setQty($qty);
    }

    return $item;
  }

  public function getItemCollection() {
    return $this->_itemCollection;
  }

  public function getItem($itemId) {
    if (!$itemId) {
        return false;
    }
    return $this->_itemCollection[$itemId];
  }
  
  public function getItemByProduct($productId) {
    foreach ($this->_itemCollection as $item) {
      if ($item->getProductId() == $productId) {
        return $item;
      }
    }
    return $item;
  }

  public function removeItem($itemId) {
    if (!$itemId) {
      return false;
    }
    unset($this->_itemCollection[$itemId]);
    return true;
  }
  
  protected function getNextAvailableItemId() {
    $this->_lastItemId = $this->_lastItemId + 1;
    return $this->_lastItemId;
  }

  public function addNewItem($product, $buyRequest = null, $forciblySetQty = false) {
    if ($product instanceof Mage_Catalog_Model_Product) {
      $productId = $product->getId();
      $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $product->getStoreId();
    } else {
      $productId = (int) $product;
      if ($buyRequest->getStoreId()) {
        $storeId = $buyRequest->getStoreId();
      } else {
        $storeId = Mage::app()->getStore()->getId();
      }
    }

    /* @var $product Mage_Catalog_Model_Product */
    $product = Mage::getModel('catalog/product')
                   ->setStoreId($storeId)
                   ->load($productId);

    if ($buyRequest instanceof Varien_Object) {
      $_buyRequest = $buyRequest;
    } elseif (is_string($buyRequest)) {
      $_buyRequest = new Varien_Object(unserialize($buyRequest));
    } elseif (is_array($buyRequest)) {
      $_buyRequest = new Varien_Object($buyRequest);
    } else {
      $_buyRequest = new Varien_Object();
    }

    $cartCandidates = $product->getTypeInstance(true)
                              ->processConfiguration($_buyRequest, $product);

    if (is_string($cartCandidates)) {
      return $cartCandidates;
    }

    if (!is_array($cartCandidates)) {
      $cartCandidates = [$cartCandidates];
    }

    $errors = [];
    $items = [];

    foreach ($cartCandidates as $candidate) {
      if ($candidate->getParentProductId()) {
        continue;
      }
      $candidate->setWishlistStoreId($storeId);

      $qty = $candidate->getQty() ? $candidate->getQty() : 1; 
      $item = $this->_addCatalogProduct($candidate, $qty, $forciblySetQty);
      $items[] = $item;

      if ($item->getHasError()) {
          $errors[] = $item->getMessage();
      }
    }

    return $item;
  }

  public function getItemsCount() {
    return count($this->_itemCollection);
  }

  public function isSalable() {
    foreach ($this->_itemCollection as $item) {
      if ($item->getProduct()->getIsSalable()) {
        return true;
      }
    }
    return false;
  }

  public function updateItem($itemId, $buyRequest) {
    $item = $this->getItem((int)$itemId);
    if (!$item) {
      Mage::throwException(Mage::helper('wishlist')->__('Cannot specify wishlist item.'));
    }

    $product = $item->getProduct();
    $productId = $product->getId();
    if ($productId) {
      $product->setWishlistStoreId($item->getStoreId());
      $resultItem = $this->addNewItem($product, $buyRequest, true);
  
      if ($item->getId() != $resultItem->getId()) {
        $this->removeItem($item->getId());
      }
        
      if (is_string($resultItem)) {
        Mage::throwException(Mage::helper('checkout')->__($resultItem));
      }
    } else {
      Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
    }
    return $this;
  }
}
