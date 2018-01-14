<?php

class Ecominfinity_Repeatgroup_Block_Filter extends Mage_Core_Block_Template {

    protected $_layer = null;

    protected function _construct() {
        $this->addData(array(
            'cache_lifetime'=>false,
            'cache_tags'=>array(Mage_Core_Model_Store::CACHE_TAG, Mage_Cms_Model_Block::CACHE_TAG)
        ));
    }

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    protected function _getLayer() {
        if ($this->_layer === null) {
            $this->_layer = Mage::getSingleton('catalog/layer');
        } 
        return $this->_layer;
    }

    public function getAttributes() {
        $_layer = $this->_getLayer();
        $_attributes = $_layer->getFilterableAttributes();
        return $_attributes;
    }

    public function getAttributeItems($_attribute) {
        $_layer = $this->_getLayer();
        $_block = $this->getLayout()->createBlock('catalog/layer_filter_attribute')->setLayer($_layer)->setAttributeModel($_attribute)->init();
        return $_block->getItems();
    }

    public function getSelectedBrands() {
		$_brand = $this->getRequest()->getParam('brand');
		if (isset($_brand) === false) return NULL;
		foreach ($_brand as $_key => $_item) {
			if (is_numeric($_item) === false) {
				unset($_brand[$_key]);
			}
		}
        return $_brand;
    }

    public function getSelectedFabric() {
		$_fabric = $this->getRequest()->getParam('fabric');
		if (isset($_fabric) === false) return NULL;
		foreach ($_fabric as $_key => $_item) {
			if (is_numeric($_item) === false) {
				unset($_fabric[$_key]);
			}
		}
        return $_fabric;
    }
}
