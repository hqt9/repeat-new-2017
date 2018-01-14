<?php

require_once Mage::getModuleDir('controllers', 'Mage_Customer').DS.'AddressController.php';

class Ecominfinity_Repeatgroup_AddressController extends Mage_Customer_AddressController {
  public function formPostAction() {
    if ($this->getRequest()->isPost()) {
      $customer = $this->_getSession()->getCustomer();
      $_type = $this->getRequest()->getParam('type');

      /* @var $address Mage_Customer_Model_Address */
      $address  = Mage::getModel('customer/address');
      $addressId = $this->getRequest()->getParam('id');
      if ($addressId) {
        $existsAddress = $customer->getAddressById($addressId);
        if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
          $address->setId($existsAddress->getId());
        }
      }

      $errors = array();

      /* @var $addressForm Mage_Customer_Model_Form */
      $addressForm = Mage::getModel('customer/form');
      $addressForm->setFormCode('customer_address_edit')
          ->setEntity($address);
      $addressData    = $addressForm->extractData($this->getRequest());
      $addressData['region'] = '-';
      $addressData['region_id'] = '-';
      $addressErrors  = $addressForm->validateData($addressData);
      if ($addressErrors !== true) {
        $errors = $addressErrors;
      }

      try {
        $addressForm->compactData($addressData);
        $address->setCustomerId($customer->getId());

        if ($_type === 'billing') {
          $address->setIsDefaultBilling(1, false);
        } else {
          $address->setIsDefaultShipping(1, false);
        }

        $addressErrors = $address->validate();
        if ($addressErrors !== true) {
            $errors = array_merge($errors, $addressErrors);
        }
        if (count($errors) === 0) {
            $address->save();
            $this->_getSession()->addSuccess($this->__('The address has been saved.'));
            $this->_redirectSuccess(Mage::getUrl('customer/account/index', array('_secure'=>true)));
            return;
        } else {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
            foreach ($errors as $errorMessage) {
                $this->_getSession()->addError($errorMessage);
            }
        }
      } catch (Mage_Core_Exception $e) {
        var_dump($e->getMessage());
          $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
              ->addException($e, $e->getMessage());
      } catch (Exception $e) {
        var_dump($e->getMessage());
          $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
              ->addException($e, $this->__('Cannot save address.'));
      }
    }
  }
}
