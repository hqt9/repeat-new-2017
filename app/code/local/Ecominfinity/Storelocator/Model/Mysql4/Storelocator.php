<?php

class Ecominfinity_Storelocator_Model_Mysql4_Storelocator extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the storelocator_id refers to the key field in your database table.
        $this->_init('storelocator/storelocator', 'entity_id');
    }
}