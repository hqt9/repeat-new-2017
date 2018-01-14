<?php

class AAOO_Qrcode_IndexController extends Mage_Core_Controller_Front_Action {
  public function indexAction() {
    $_params = $this->getRequest()->getParams();
    $_sku = $_params['sku'];

    $_qrcode = Mage::getModel('qrcode/qrcode')->loadByCode($_sku);
    $_qrcode_id = $_qrcode->getId();

    if ($_qrcode_id == NULL) {
      $_qrcode = Mage::getModel('qrcode/qrcode');
      $_qrcode->setData('code', $_sku);
      $_qrcode->setData('counter', 1);
      $_qrcode->setData('url', '');
      $_qrcode->setData('enabled', 1);
      $_qrcode->setData('created_time', date('Y-m-d H:i:s',time()));
      $_qrcode->setData('update_time', date('Y-m-d H:i:s',time()));
      $_qrcode->save();
    } else {
      // [TODO] this should be self increase
      $_counter = $_qrcode->getData('counter');
      $_counter ++;
      $_qrcode->setData('counter', $_counter);
      $_qrcode->setData('update_time', date('Y-m-d H:i:s',time()));
      $_qrcode->save();
    }

    $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $_sku);
    if ($_product == false) {
      return;
    }
    $_product = Mage::getModel('catalog/product')->load($_product->getId());

    $_url_key = $_product->getData('url_key');
    $_url = Mage::getBaseUrl() . $_url_key . '.html?utm_source=qr-code';

    Mage::app()->getResponse()->setRedirect($_url, 301)->sendResponse();
    return;
  }
}