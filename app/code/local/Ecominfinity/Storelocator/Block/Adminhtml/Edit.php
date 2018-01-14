<?php

class EcomInfinity_Storelocator_Block_Adminhtml_Edit extends Mage_Core_Block_Template {
  public function _prepareLayout() {
    if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    }
    return parent::_prepareLayout();
  }
}
