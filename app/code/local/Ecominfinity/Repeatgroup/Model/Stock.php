<?php

class Ecominfinity_Repeatgroup_Model_Stock extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('repeatgroup/stock');
    }
}