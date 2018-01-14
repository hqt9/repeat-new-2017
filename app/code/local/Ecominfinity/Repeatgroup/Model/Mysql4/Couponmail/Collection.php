<?php

class Ecominfinity_Repeatgroup_Model_Mysql4_Couponmail_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('repeatgroup/couponmail');
    }
}