<?php

class AAOO_Qrcode_Model_Mysql4_Qrcode_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    public function _construct()
    {
        parent::_construct();
        $this->_init('qrcode/qrcode');
    }
}