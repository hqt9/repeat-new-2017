<?php

class Ecominfinity_Repeatgroup_Model_Couponmail extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('repeatgroup/couponmail');
    }

    public function getByCoupon($_code) {
        $_collection = $this->getCollection()->addFieldToFilter('coupon', $_code);
        if ($_collection->getSize() > 0) {
            $_items = $_collection->getItems();
            foreach($_items as $_item) {
                return $_item;
            }
        }

        return NULL;
    }
}