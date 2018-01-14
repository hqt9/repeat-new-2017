<?php
class Ecominfinity_Storelocator_Block_Storelocator extends Mage_Core_Block_Template {
    public function _prepareLayout() {
        $this->getLayout()->getBlock('head')->setTitle($this->__('Store Locator'));

        $_breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $_breadcrumbs->addCrumb('home', array('label'=>$this->__('Home'), 'title'=>$this->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
        $_breadcrumbs->addCrumb('storelocator', array('label'=>$this->__('Store Locator')));

        return parent::_prepareLayout();
    }
    
     public function getStorelocator() {
        if (!$this->hasData('storelocator')) {
            $this->setData('storelocator', Mage::registry('storelocator'));
        }
        return $this->getData('storelocator');
    }
}