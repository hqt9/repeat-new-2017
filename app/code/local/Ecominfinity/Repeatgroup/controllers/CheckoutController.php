<?php

require_once Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'OnepageController.php';

class Ecominfinity_Repeatgroup_CheckoutController extends Mage_Checkout_OnepageController {

  private function _prepare_result($_is_success, $_message, $_data) {
    return json_encode(
      array(
        'success' => $_is_success,
        'message' => $_message,
        'data' => $_data
      )
    );
  }

  public function loginAction() {
    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
      $this->getResponse()->setBody(
        $this->_prepare_result(true, '', array())
      );
      return;
    }

    $session = Mage::getSingleton('customer/session');

    if ($this->getRequest()->isPost()) {
      $login = $this->getRequest()->getPost('login');
      if (!empty($login['username']) && !empty($login['password'])) {
        try {
          $session->login($login['username'], $login['password']);
          $this->getResponse()->setBody(
            $this->_prepare_result(true, '', array())
          );

          // [TODO] check whether need to re-create address for customer
          return;
        } catch (Mage_Core_Exception $e) {
          switch ($e->getCode()) {
            case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
              $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
              $this->getResponse()->setBody(
                $this->_prepare_result(false, $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value), array())
              );
              return;
            case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
              $this->getResponse()->setBody(
                $this->_prepare_result(false, $e->getMessage(), array())
              );
              return;
            default:
              $this->getResponse()->setBody(
                $this->_prepare_result(false, $e->getMessage(), array())
              );
              return;
            }
            $session->setUsername($login['username']);
        } catch (Exception $e) {
          // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
        }
      } else {
        $this->getResponse()->setBody(
          $this->_prepare_result(false, $this->__('Login and password are required.'), array())
        );
      }
    }
  }

  public function debugAction() {
    $_quote = Mage::getSingleton('checkout/session')->getQuote();
    $_shipping_address = $_quote->getShippingAddress();
    var_dump($_shipping_address->getData());
  }

  public function registerAction() {
    $_params = $this->getRequest()->getParams();

    $_email = $_params['email'];
    if (! Zend_Validate::is($_email, 'EmailAddress')) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $this->__('Not a valid email address.'), array())
      );
      return;
    }

    // create customer
    $_customer = Mage::getModel('customer/customer')
                  ->setWebsiteId(Mage::app()->getWebsite()->getId())
                  ->setStore(Mage::app()->getStore())
                  ->setFirstname($_params['firstname'])
                  ->setLastname($_params['lastname'])
                  ->setEmail($_params['email'])
                  ->setPassword($_params['password']);
    try {
      $_customer->save();
    } catch (Exception $e) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $e->getMessage(), array())
      );
      return;
    }

    // save billing address
    $_address = Mage::getModel('customer/address');
    $_address -> setCustomerId($_customer->getId())
              -> setFirstname($_customer->getFirstname())
              -> setLastname($_customer->getLastname())
              -> setCountryId($_params['country_id'])
              -> setPostcode($_params['postcode'])
              -> setCity($_params['city'])
              -> setTelephone($_params['telephone'])
              -> setStreet($_params['street'])
              -> setIsDefaultBilling('1')
              -> setSaveInAddressBook('1');
	if (isset($_params['region'])) {
		$_address->setRegion($_params['region']);
	}

    try {
      $_address->save();
    } catch (Exception $e) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $e->getMessage(), array())
      );
      return;
    }

    // save shipping address
    $_address = Mage::getModel('customer/address');
    $_address -> setCustomerId($_customer->getId())
              -> setFirstname($_customer->getFirstname())
              -> setLastname($_customer->getLastname())
              -> setCountryId($_params['country_id'])
              -> setPostcode($_params['postcode'])
              -> setCity($_params['city'])
              -> setTelephone($_params['telephone'])
              -> setStreet($_params['street'])
              -> setIsDefaultShipping('1')
              -> setSaveInAddressBook('1');
	if (isset($_params['region'])) {
		$_address->setRegion($_params['region']);
	}
        if (isset($_params['region_id'])) {
		$_address->setRegion($_params['region_id']);
	}
    try {
      $_address->save();
    } catch (Exception $e) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $e->getMessage(), array())
      );
      return;
    }

    // log in customer
    try {
      $_session = Mage::getSingleton('customer/session');
      $_session->login($_params['email'], $_params['password']);
    } catch (Exception $e) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $e->getMessage(), array())
      );
      return;
    }

    $this->getResponse()->setBody(
      $this->_prepare_result(true, '', array())
    );
  }

  public function saveBillingAction() {
    if ($this->getRequest()->isPost()) {
      $data = $this->getRequest()->getPost('billing', array());

      $_subscribe = $this->getRequest()->getPost('subscribed');
      if ($_subscribe == 'on') {
        Mage::getModel('newsletter/subscriber')->subscribe($data['email']);
      }

      if (isset($data['region']) == false && isset($data['region_id']) == false) {
        $data['region'] = '-';
        $data['region_id'] = '-';
      }

      // turn checkbox "on" to 1
      $_use_for_shipping = $this->getRequest()->getPost('use_for_shipping');
      $data['use_for_shipping'] = (isset($_use_for_shipping)) ? 1 : 0;
      
      $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

      if (isset($data['email'])) {
        $data['email'] = trim($data['email']);
      }

      // security access code
      if (isset($data['sac'])) {
        $_quote = Mage::getSingleton('checkout/session')->getQuote();
        $_quote->setData('sac', $data['sac']);
        $_quote->save();
      }

      $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

      if (!isset($result['error'])) {
        $_checkout = Mage::getSingleton('checkout/session')->getQuote();
        $_customer = $this->getCustomer();

        $this->getResponse()->setBody(
          $this->_prepare_result(true, '', [
            'billing' => $this->_prepare_address($_checkout->getBillingAddress(), $_customer),
            'shipping' => $this->_prepare_address($_checkout->getShippingAddress(), $_customer),
          ])
        );
      } else {
        $this->getResponse()->setBody(
          $this->_prepare_result(false, $result['message'], $result)
        );
      }
    }
  }

  public function saveShippingAction() {
    if ($this->getRequest()->isPost()) {
      $data = $this->getRequest()->getPost('shipping', array());
      if (isset($data['region']) == false && isset($data['region_id']) == false) {
        $data['region'] = '-';
      }
      $data['region_id'] = '-';
      $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
      $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

      if (!isset($result['error'])) {
        $_checkout = Mage::getSingleton('checkout/session')->getQuote();
        $_customer = $this->getCustomer();

        $this->getResponse()->setBody(
          $this->_prepare_result(true, '', [
            'billing' => $this->_prepare_address($_checkout->getBillingAddress(), $_customer),
            'shipping' => $this->_prepare_address($_checkout->getShippingAddress(), $_customer),
          ])
        );
      } else {
        $this->getResponse()->setBody(
          $this->_prepare_result(false, '', $result)
        );
      }
    }
  }

  public function getPaymentAction() {
    $layout = $this->getLayout();
    $update = $layout->getUpdate();
    $update->load('checkout_onepage_paymentmethod');
    $layout->generateXml();
    $layout->generateBlocks();
    $output = $layout->getOutput();

    $total = $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();

    $this->getResponse()->setBody(
      $this->_prepare_result(true, '', array('payment' => $output, 'total' => $total))
    );
  }

  public function savePaymentAction() {
    try {
      $data = $this->getRequest()->getPost('payment', array());
      $result = $this->getOnepage()->savePayment($data);

      // get section and redirect data
      $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
      $this->getResponse()->setBody(
        $this->_prepare_result(
          true, '', [
            'redirect' => $redirectUrl, 
            'result' => $result,
            'payment' => $this->_getPayment(),
            'totals' => $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml(),
        ])
      );
      return;
    } catch (Mage_Payment_Exception $e) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $e->getMessage(), null)
      );
      return;
    } catch (Mage_Core_Exception $e) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $e->getMessage(), null)
      );
      return;
    } catch (Exception $e) {
      Mage::logException($e);
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $this->__('Unable to set Payment Method.'), null)
      );
      return;
    }
  }

  public function getShippingMethodAction() {
    $layout = $this->getLayout();
    $update = $layout->getUpdate();
    $update->load('checkout_onepage_shippingmethod');
    $layout->generateXml();
    $layout->generateBlocks();
    $output = $layout->getOutput();
    $this->getResponse()->setBody($output);
  }

  public function saveShippingMethodAction() {
    if ($this->getRequest()->isPost()) {

      // giftwrapping
      $_gift_wrapping = $this->getRequest()->getPost('wrapping-style', '1');
      $_gift_wrapping_info = array(
        'gw_id' => $_gift_wrapping,
        'gw_allow_gift_receipt' => false,
        'gw_add_card' => false,
      );

      $_quote = $this->getOnepage()->getQuote();
      $_quote->getShippingAddress()->addData($_gift_wrapping_info);
      $_quote->addData($_gift_wrapping_info)->save();

      $data = $this->getRequest()->getPost('shipping_method', '');
      $result = $this->getOnepage()->saveShippingMethod($data);

      $_gift_wrapping_output = Mage::getModel('enterprise_giftwrapping/wrapping')->load($_gift_wrapping);

      // $result will contain error data if shipping method is empty
      if (!$result) {
        Mage::dispatchEvent(
          'checkout_controller_onepage_save_shipping_method',
           array(
            'request' => $this->getRequest(),
            'quote'   => $this->getOnepage()->getQuote()));
        $this->getOnepage()->getQuote()->collectTotals()->save();
        $this->getResponse()->setBody(
          $this->_prepare_result(
            true, '', [
              'shipping_method' => $this->_getShippingMethod(),
              'packaging' => $_gift_wrapping_output->getData('design'),
              'totals' => $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml(),
            ]
          )
        );
        return;
      }
      $this->getResponse()->setBody(
          $this->_prepare_result(false, '', array())
        );
    }
  }

  private function _getPayment() {
    $_payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance();
    $_title = $_payment->getTitle();

    if (get_class($_payment) == 'Phoenix_CashOnDelivery_Model_CashOnDelivery') {
      $_shipping_address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
      $_extra_fee = $_payment->getAddressCosts($_shipping_address);

      $_extra_fee_excl = $this->convertPrice(
        Mage::helper('phoenix_cashondelivery')->getCodPrice(
          $_extra_fee,
          Mage::helper('phoenix_cashondelivery')->displayCodFeeIncludingTax(),
          $_shipping_address,
          true
        )
      );

      $_extra_fee_incl = $this->convertPrice(
        Mage::helper('phoenix_cashondelivery')->getCodPrice(
          $_extra_fee,
          true,
          $_shipping_address,
          true
        )
      );

      $_codFeeStr = $_extra_fee_excl;
      if (Mage::helper('phoenix_cashondelivery')->displayCodBothPrices() && ($_extra_fee_incl != $_extra_fee_excl)) {
        $_codFeeStr .= ' ('.$this->__('Incl. Tax').' '.$_extra_fee_incl.')';
      }

      if ($_extra_fee > 0.0001) {
        $_description = $this->__('You will be charged an extra fee of %s.', $_codFeeStr);
      }
    }

    if (get_class($_payment) == 'Adyen_Payment_Model_Adyen_Hpp') {
      // $_title = '';
    }

    if (isset($_description) === true) {
      $_title = sprintf('<p>%s</p><p>%s</p>', $_title, $_description);
    }

    return $_title;
  }

  private function _getShippingMethod() {
	$_store_code = Mage::app()->getStore()->getCode();
    $_quote = Mage::getSingleton('checkout/session')->getQuote();
    $_shipping_address = $_quote->getShippingAddress();
    $_title = $_shipping_address->getShippingDescription();
    if (strpos($_title, ' - ') != false) {
      $_title = substr($_title, strpos($_title, ' - ') + 3);
    }
    $_amount = $_shipping_address->getData('base_shipping_incl_tax');
    if ($_amount < 0.0001) {
	  if ($_store_code == 'uk') {
		$_title .= ' <strong>£ 0,00</strong> (' . $this->__('Free shipping') . ')';
	  } elseif ($_store_code == 'us') {
        $_title .= ' <strong>$ 0.00</strong> (' . $this->__('Free shipping') . ')';
	  } elseif ($_store_code == 'ch_de' || $_store_code == 'ch_fr') {
		$_title .= ' <strong>CHF 0,00</strong> (' . $this->__('Free shipping') . ')';
	  } else {
		$_title .= ' <strong>0,00 €</strong> (' . $this->__('Free shipping') . ')';
	  }
    } else {
	  if ($_store_code == 'uk') {
		 $_title .= ' <strong>£ ' . number_format($_amount, 2, ',', '.') . '</strong>';
	  } elseif ($_store_code == 'us') {
		 $_title .= ' <strong>$ ' . number_format($_amount, 2, '.', ',') . '</strong>';
	  } elseif ($_store_code == 'ch_de' || $_store_code == 'ch_fr') {
		 $_title .= ' <strong>CHF ' . number_format($_amount, 2, ',', '.') . '</strong>';
	  } else {
		 $_title .= ' <strong>' . number_format($_amount, 2, ',', '.') . ' €</strong>';
	  }
    }
    
    return $_title;
  }

  public function getReviewSectionAction() {

    // order review html
    $this->loadLayout('checkout_onepage_review');
    $_order_review_html = $this->getLayout()->getBlock('checkout.onepage.review')->toHtml();

    $_checkout = Mage::getSingleton('checkout/session')->getQuote();
    $_customer = $this->getCustomer();

    $this->getResponse()->setBody(
      $this->_prepare_result(
        true,
        '',
        array(
          'billing' => $this->_prepare_address($_checkout->getBillingAddress(), $_customer),
          'shipping' => $this->_prepare_address($_checkout->getShippingAddress(), $_customer),
          'shipping_method' => $this->_getShippingMethod(),
          'payment' => $this->_getPayment(), 
          'review' => $_order_review_html
        )
      )
    );
  }

  public function getReviewAction() {
    $this->loadLayout('checkout_onepage_review');
    $_html = '';
    try {
      $_html = $this->getLayout()->getBlock('checkout.onepage.review')->toHtml();
      $this->getResponse()->setBody(
        $this->_prepare_result(true, '', array('html' => $_html))
      );
    } catch (Exception $e) {
    	$this->getResponse()->setBody(
    		$this->_prepare_result(false, $e->getMessage(), array())
    	);
    }
  }

  private function _format_address($_address) {
    return implode('<br>', array(
      $_address->getFirstname() . ' ' . $_address->getLastname(),
      implode('<br>', $_address->getStreet()),
      Mage::getModel('directory/country')->load($_address->getCountryId())->getName()
    ));
  }

  private function _prepare_address($_address, $_customer) {
    $_address_data = $_address->getData();
    $_email = $_address->getEmail();
    if (isset($_email) === false) $_email = $_customer->getEmail();
    return array(
      'firstname' => $_address_data['firstname'],
      'lastname' => $_address_data['lastname'],
      'city' => $_address_data['city'],
      'country_id' => $_address_data['country_id'],
      'region' => $_address_data['region'],
      'region_id' => $_address_data['region_id'],
      'country' => Mage::getModel('directory/country')->load($_address_data['country_id'])->getName(),
      'postcode' => $_address_data['postcode'],
      'telephone' => $_address_data['telephone'],
      'email' => $_email,
      'street' => explode("\n", $_address_data['street']),
    );
  }

  public function getCustomer() {
    return Mage::getSingleton('customer/session')->getCustomer();
  }

  public function getAddressAction() {
    $_checkout = Mage::getSingleton('checkout/session')->getQuote();
    $_shipping_address = $_checkout->getShippingAddress();
    $_customer = $this->getCustomer();
    $this->getResponse()->setBody(
      $this->_prepare_result(
        true,
        '',
        array(
          'billing' => $this->_prepare_address($_checkout->getBillingAddress(), $_customer),
          'shipping' => $this->_prepare_address($_checkout->getShippingAddress(), $_customer)
        )
      )
    );
  }

  public function getReviewPaymentAction() {
    $_payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance();
    $_title = $_payment->getTitle();

    if (get_class($_payment) == 'Phoenix_CashOnDelivery_Model_CashOnDelivery') {
      $_shipping_address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
      $_extra_fee = $_payment->getAddressCosts($_shipping_address);

      $_extra_fee_excl = $this->convertPrice(
        Mage::helper('phoenix_cashondelivery')->getCodPrice(
          $_extra_fee,
          Mage::helper('phoenix_cashondelivery')->displayCodFeeIncludingTax(),
          $_shipping_address,
          true
        )
      );

      $_extra_fee_incl = $this->convertPrice(
        Mage::helper('phoenix_cashondelivery')->getCodPrice(
          $_extra_fee,
          true,
          $_shipping_address,
          true
        )
      );

      $_codFeeStr = $_extra_fee_excl;
      if (Mage::helper('phoenix_cashondelivery')->displayCodBothPrices() && ($_extra_fee_incl != $_extra_fee_excl)) {
        $_codFeeStr .= ' ('.$this->__('Incl. Tax').' '.$_extra_fee_incl.')';
      }

      if ($_extra_fee > 0.0001) {
        $_description = $this->__('You will be charged an extra fee of %s.', $_codFeeStr);
      }
    }

    if (isset($_description) === true) {
      $_title = sprintf('<p>%s</p><p>%s</p>', $_title, $_description);
    }

    $this->getResponse()->setBody($_title);
  }

  public function convertPrice($price, $format=true, $includeContainer = true) {
    return Mage::getSingleton('checkout/session')->getQuote()->getStore()->convertPrice($price, $format, $includeContainer);
  }

  public function getReviewShipmentAction() {
    $_quote = Mage::getSingleton('checkout/session')->getQuote();
    $_shipping_address = $_quote->getShippingAddress();
    $_title = $_shipping_address->getShippingDescription();
    if (strpos($_title, ' - ') != false) {
      $_title = substr($_title, strpos($_title, ' - ') + 3);
    }
    $_amount = $_shipping_address->getData('base_shipping_incl_tax');
    if ($_amount < 0.0001) {
      $_title .= ' <strong>0,00 €</strong> (' . $this->__('Free shipping') . ')';
    } else {
      $_title .= ' <strong>' . number_format($_amount, 2, ',', '.') . ' €</strong>';
    }
    $this->getResponse()->setBody($_title);
  }
}
