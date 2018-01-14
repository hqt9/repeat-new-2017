<?php

class AAOO_Guestwishlist_Block_Item_Configure extends Mage_Core_Block_Template {
    
    protected function getProduct() {
        return Mage::registry('product');
    }

    protected function getWishlistItem() {
        return Mage::registry('guestwishlist_item');
    }

    protected function _prepareLayout() {
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $url = Mage::helper('guestwishlist')->getAddToCartUrl($this->getWishlistItem());
            $block->setCustomAddToCartUrl($url);
        }
        return parent::_prepareLayout();
    }
}
