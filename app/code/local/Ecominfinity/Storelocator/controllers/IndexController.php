<?php
class Ecominfinity_Storelocator_IndexController extends Mage_Core_Controller_Front_Action {
  public function formatAction() {
    $_collection = Mage::getModel('storelocator/storelocator')->getCollection();

    $_url_keys = [];

    foreach ($_collection->getItems() as $_store) {
      $_city = $_store->getData('city');
      $_name = $_store->getData('name');

      
      $_name = str_replace('&', '', $_name);
      $_name = str_replace('/', '', $_name);
      $_name = str_replace('+', '', $_name);
      $_name = str_replace(',', '', $_name);
      $_name = str_replace("'", '', $_name);
      $_name = str_replace('  ', ' ', $_name);
      $_name = str_replace('   ', ' ', $_name);

      $_city = str_replace(' ', '-', $_city);
      $_name = str_replace(' ', '-', $_name);
      $_city = str_replace('ü', 'u', $_city);
      $_name = str_replace('ü', 'u', $_name);
      $_city = str_replace('Ü', 'U', $_city);
      $_name = str_replace('Ü', 'U', $_name);
      $_city = str_replace('è', 'e', $_city);
      $_name = str_replace('è', 'e', $_name);
      $_city = str_replace('ä', 'e', $_city);
      $_name = str_replace('ä', 'e', $_name);
      $_city = str_replace('ö', 'o', $_city);
      $_name = str_replace('ö', 'o', $_name);
      
      

      $_url_key = strtolower($_city . '_' . $_name);

      $_tmp = $_url_key;
      $_idx = 1;
      while (isset($_url_keys[$_tmp]) === true) {
        $_tmp = $_url_key . '_' . $_idx;
        $_idx ++;
      }
      $_url_key = $_tmp;
      $_url_keys[$_url_key] = true;
      
      $_store->setData('url_key', $_url_key);
      $_store->save();
    }

    die();
  }

  public function indexAction() {
    $this->loadLayout();
    $this->renderLayout();
  }

  public function printAction() {
    $this->loadLayout();
    $this->renderLayout();
  }
}