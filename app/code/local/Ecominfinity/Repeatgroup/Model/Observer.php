<?php

class Ecominfinity_Repeatgroup_Model_Observer extends Mage_Core_Model_Abstract {
    public function logCompiledLayout($o) {
        $req  = Mage::app()->getRequest();
        $info = sprintf(
            "\nRequest: %s\nFull Action Name: %s_%s_%s\nHandles:\n\t%s\nUpdate XML:\n%s",
            $req->getRouteName(),
            $req->getRequestedRouteName(),      //full action name 1/3
            $req->getRequestedControllerName(), //full action name 2/3
            $req->getRequestedActionName(),     //full action name 3/3
            implode("\n\t",$o->getLayout()->getUpdate()->getHandles()),
            $o->getLayout()->getUpdate()->asString()
        );

        // Force logging to var/log/layout.log
        Mage::log($info, Zend_Log::INFO, 'layout.log', true);
    }

    public function chfFormat(Varien_Event_Observer $observer) {
        if (Mage::app()->getStore()->getCurrentCurrency()->getCode() == 'CHF') {
            $options = $observer->getEvent()->getCurrencyOptions();
            $options['position'] = Zend_Currency::LEFT;
        }

        return $this;
    }

    public function hreflang($o) {
        $_handles = $o->getLayout()->getUpdate()->getHandles();
        $_head_block = Mage::app()->getLayout()->getBlock('head');
        
        $_hreflang = [];
        if (in_array('catalog_category_view', $_handles) || in_array('cms_page_view', $_handles)) {
            $_hreflang = Mage::helper('repeatgroup')->redirectHreflang();
        } elseif (in_array('catalog_product_view', $_handles)) {
            $_hreflang = Mage::helper('repeatgroup')->productHreflang(Mage::registry('current_product')->getId());
        } elseif (in_array('cms_index_index', $_handles)) {
            $_hreflang = Mage::helper('repeatgroup')->indexHreflang();
        }

        if (count($_hreflang) > 0) {
            foreach ($_hreflang as $_link) {
                $_head_block->addLinkRel('alternate', $_link['url'] . '" hreflang="' . $_link['locale']);
            }
        }

    }
}
