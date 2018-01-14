<?php

class Ecominfinity_Repeatgroup_Model_Mysql4_Repeatgroup extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the repeatgroup_id refers to the key field in your database table.
        $this->_init('repeatgroup/repeatgroup', 'repeatgroup_id');
    }
}