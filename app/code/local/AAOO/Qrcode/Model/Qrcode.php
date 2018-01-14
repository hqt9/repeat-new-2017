<?php
class AAOO_Qrcode_Model_Qrcode extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('qrcode/qrcode');
    }

    public function loadByCode($_code) {
      return Mage::getModel('qrcode/qrcode')
              ->getCollection()
              ->addFieldToFilter('code', $_code)
              ->getFirstItem();
    }
}