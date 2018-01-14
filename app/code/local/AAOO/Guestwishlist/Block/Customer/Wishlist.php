<?php

class AAOO_Guestwishlist_Block_Customer_Wishlist extends AAOO_Guestwishlist_Block_Abstract {
    protected $_optionsCfg = [];

    public function _construct() {
        parent::_construct();
        $this->addOptionsRenderCfg('default', 'catalog/product_configuration', 'guestwishlist/options_list.phtml');
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('wishlist')->__('My Wishlist'));
        }
    }

    public function getBackUrl() {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    public function setOptionsRenderCfgs($optionCfg) {
        $this->_optionsCfg = $optionCfg;
        return $this;
    }

    public function getOptionsRenderCfgs() {
        return $this->_optionsCfg;
    }

    public function addOptionsRenderCfg($productType, $helperName, $template = null) {
        $this->_optionsCfg[$productType] = array('helper' => $helperName, 'template' => $template);
        return $this;
    }

    public function getOptionsRenderCfg($productType) {
        if (isset($this->_optionsCfg[$productType])) {
            return $this->_optionsCfg[$productType];
        } elseif (isset($this->_optionsCfg['default'])) {
            return $this->_optionsCfg['default'];
        } else {
            return null;
        }
    }

    public function getDetailsHtml($item) {
        $cfg = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
        if (!$cfg) {
            return '';
        }

        $helper = Mage::helper($cfg['helper']);
        if (!($helper instanceof Mage_Catalog_Helper_Product_Configuration_Interface)) {
            Mage::throwException($this->__("Helper for wishlist options rendering doesn't implement required interface."));
        }

        $block = $this->getChild('item_options');
        if (!$block) {
            return '';
        }

        if ($cfg['template']) {
            $template = $cfg['template'];
        } else {
            $cfgDefault = $this->getOptionsRenderCfg('default');
            if (!$cfgDefault) {
                return '';
            }
            $template = $cfgDefault['template'];
        }

        return $block->setTemplate($template)
                     ->setOptionList($helper->getOptions($item))
                     ->toHtml();
    }

    public function getCommentValue($item) {
        return $this->hasDescription($item) ? $this->getEscapedDescription($item) : Mage::helper('guestwishlist')->defaultCommentString();
    }

    public function getAddToCartQty($item) {
        $qty = $this->getQty($item);
        return $qty ? $qty : 1;
    }
}
