<?php

class AAOO_Qrcode_Model_Mysql4_Qrcode extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        $this->_init('qrcode/qrcode', 'entity_id');
    }
}