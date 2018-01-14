<?php

class EcomInfinity_Storelocator_Adminhtml_StorelocatorController extends Mage_Adminhtml_Controller_Action {
  public function indexAction() {
    $this->_title($this->__('Ecom Infinity'))
         ->_title($this->__('Store Locator'));
    $this->loadLayout();     
    $this->renderLayout();
  }

  public function createAction() {
    $_params = $this->getRequest()->getParams();
    unset($_params['form_key']);
    unset($_params['key']);
    
    $_store = Mage::getModel('storelocator/storelocator');
    foreach ($_params as $_key => $_value) {
      $_store->setData($_key, $_value);
    }

    $_store->setData('main_image', 'wysiwyg/storelocator/whole-sell/main.jpg');

    $_store->save();
    
    $this->getResponse()->setBody(
      $this->_prepareResponse(true, array('id' => $_store->getId()), '')
    );
  }

  public function updateAction() {
    $_params = $this->getRequest()->getParams();
    unset($_params['key']);
    unset($_params['form_key']);
    unset($_params['main_image']);

    $_store = Mage::getModel('storelocator/storelocator')->load($_params['entity_id']);
    if (isset($_params['country'])) {
      $_params['continent'] = Mage::helper('storelocator')->getContinentByCountryCode($_params['country']);
    }

    foreach ($_params as $_key => $_value) {
      if ($_key == 'opening_hours') {
        $_value = json_encode($_value);
      }
      $_store->setData($_key, $_value);
    }

    $_store->save();

    $this->getResponse()->setBody(
      $this->_prepareResponse(true, array(), '')
    );
  }

  public function deleteAction() {

  }

  private function _prepareResponse($_success, $_data, $_message = "") {
    return json_encode(array(
      'success' => $_success,
      'data' => $_data,
      'message' => $_message,
    ));
  }
}
