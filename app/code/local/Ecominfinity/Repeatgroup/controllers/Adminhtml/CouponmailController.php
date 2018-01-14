<?php

class Ecominfinity_Repeatgroup_Adminhtml_CouponmailController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__('Ecom Infinity'))
             ->_title($this->__('Coupon Mail Management'));
        $this->loadLayout();     
        $this->renderLayout();
    }

}
