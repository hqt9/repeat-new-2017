<?php

class Ecominfinity_Repeatgroup_Adminhtml_McacheController extends Mage_Adminhtml_Controller_Action {

  public function indexAction() {
    $_is_running = Mage::helper('repeatgroup')->getMcacheStatus();

    if ($_is_running != '0') {
      Mage::getSingleton('adminhtml/session')->addWarning('Recache is running. Please wait a moment. ');
    } else {
      Mage::helper('repeatgroup')->setMcacheStatus('1');
    }
    $this->_redirectReferer();
  }

}
