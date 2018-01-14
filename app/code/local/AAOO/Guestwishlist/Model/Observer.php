<?php

class AAOO_Guestwishlist_Model_Observer extends Mage_Core_Model_Abstract {

  /* Varien_Event_Observer $_observer */
  public function customerLogin($_observer) {
    $_wishlist = Mage::helper('guestwishlist')->getWishlist(true);
    $_embeded_wishlist = $this->getEmbededWishlist();

    try {
      if ($_wishlist->getItemsCount() > 0) {
        foreach($_wishlist->getItemCollection() as $_item) {
          $_new_item = $_embeded_wishlist->addNewItem($_item->getProduct(), $_item->getBuyRequest());
          if ($_item->getDescription()) {
            $_new_item->setDescription($_item->getDescription())->save();
          }
        }
      }

      Mage::helper('guestwishlist')->clearWishlist();
    } catch (Exception $e) {
      Mage::getSingleton('customer/session')->addError($e->getMessage());
    }
    return $this;
  }

  protected function getEmbededWishlist() {
    $_wishlist = Mage::registry('wishlist');
    if ($_wishlist) {
      return $_wishlist;
    }

    try {
      $_wishlist = Mage::getModel('wishlist/wishlist')
                       ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
      Mage::register('wishlist', $_wishlist);
    } catch (Exception $e) {
      return false;
    }

    return $_wishlist;
  }
}