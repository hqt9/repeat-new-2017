<?php

class Ecominfinity_Repeatgroup_Helper_Stock extends Mage_Core_Helper_Abstract {
	public function get_delivery_estimation($_item_ids) {
		$_products = Mage::getModel('catalog/product')->getCollection()
							->addAttributeToSelect(array('entity_id', 'name'))
							->addAttributeToFilter('entity_id', array('in' => $_item_ids));

		$_status = array();
		foreach ($_products as $_product) {
			$_status[] = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getData('limetec_status');
		}

		$_status = max($_status);

		$_ret = 'Your order will be delivered in %s days';

		switch ($_status) {
		case 1: 
			return Mage::getStoreConfig('edelivery/general/web_stock');
		case 2:
			return Mage::getStoreConfig('edelivery/general/store_stock');
		default: 
			return Mage::getStoreConfig('edelivery/general/default_stock');
		}

		return Mage::helper('core')->__($_ret);
	}
}