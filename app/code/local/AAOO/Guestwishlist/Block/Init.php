<?php

class AAOO_Guestwishlist_Block_Init extends Mage_Core_Block_Template {
  public function getGuestwishlistAbstract() {
    $_container = [];
    
    $_container['wishlist'] = $this->getLayout()
                                   ->createBlock('guestwishlist/customer_wishlist')
                                   ->setChild('item_options', $this->getLayout()->createBlock('guestwishlist/customer_wishlist_item_options'))
                                   ->setTemplate('guestwishlist/view-ajax.phtml')
                                   ->toHtml();
    $_container['sidebar'] = $this->getLayout()->createBlock('guestwishlist/customer_sidebar')->toHtml();
    $_container['itemscount'] = Mage::helper('guestwishlist')->getWishlist()->getItemsCount();
    
    return Mage::helper('core')->jsonEncode($_container);
  }
  
  protected function _toHtml() {
    $this->setTemplate('guestwishlist/init.phtml');
    return parent::_toHtml();
  }
}