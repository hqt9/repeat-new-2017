<?php
class Ecominfinity_Repeatgroup_Block_Cataloglabel extends Mage_Core_Block_Template
{
    public function _prepareLayout() {
	return parent::_prepareLayout();
    }

    private $_product;

    public function setProduct($_product) {
        $this->_product = $_product;
	return $this;
    }

    public function getProduct() {
    	return $this->_product;
    }
    
}
