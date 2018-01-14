<?php
class Ecominfinity_Repeatgroup_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage {

  public function _prepareLayout() {
    $_breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
    $_breadcrumbs->addCrumb('home', array('label'=>$this->__('Home'), 'title'=>$this->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
    $_breadcrumbs->addCrumb('shoppingbag', array('label'=>$this->__('Shopping Bag'), 'title'=>$this->__('Shipping Bag'), 'link'=>$this->getUrl('checkout/cart')));
    $_breadcrumbs->addCrumb('checkout', array('label'=>$this->__('Checkout')));
  }

  private function _getCheckout() {
    return Mage::getModel('checkout/session')->getQuote();
  }

  public function isLogin() {
    return Mage::getSingleton('customer/session')->isLoggedIn();
  }

  public function getCustomer() {
    return Mage::getSingleton('customer/session')->getCustomer();
  }

  public function getCustomerAddress($_type) {
    if ($this->isLogin() === false) {
      return '';
    }

    $_customer = $this->getCustomer();
    if ($_type === 'billing') {
      if ($this->hasBillingAddress() === true) {
        $_address = $_customer->getDefaultBilling();
      }
    } else if ($_type === 'shipping') {
      if ($this->hasShippingAddress() === true) {
        $_address = $_customer->getDefaultShipping();
      }
    } else {
      return '';
    }

    return json_encode($this->_prepareAddress($_address, $_customer));
  }

  public function getCountrySelect($_name, $_id) {
    $_country_options = $this->getCountryOptions();
    $_store_id = Mage::app()->getStore()->getId();
    switch ($_store_id) {
      case 1:
        $_country_options[0] = array(
          'value' => '',
          'label' => $this->__('Select Country ...')
        );
        return $this->getLayout()->createBlock('core/html_select')->setName($_name)->setId($_id)->setClass('required-entry')->setOptions($_country_options)->getHtml();  
        break;
      case 2:
        return $this->getLayout()->createBlock('core/html_select')->setName($_name)->setId($_id)->setClass('required-entry')->setValue('DE')->setOptions($_country_options)->getHtml();
        unset($_country_options[0]);
      case 3: // NL
        return $this->getLayout()->createBlock('core/html_select')->setName($_name)->setId($_id)->setClass('required-entry')->setValue('NL')->setOptions($_country_options)->getHtml();
        unset($_country_options[0]);
      case 4: // FR
        unset($_country_options[0]);
        return $this->getLayout()->createBlock('core/html_select')->setName($_name)->setId($_id)->setClass('required-entry')->setValue('FR')->setOptions($_country_options)->getHtml();
	  case 5: // UK
        unset($_country_options[0]);
        return $this->getLayout()->createBlock('core/html_select')->setName($_name)->setId($_id)->setClass('required-entry')->setValue('UK')->setOptions($_country_options)->getHtml();
	  case 6: // UK
        unset($_country_options[0]);
        return $this->getLayout()->createBlock('core/html_select')->setName($_name)->setId($_id)->setClass('required-entry')->setValue('US')->setOptions($_country_options)->getHtml();
    case 7:
    case 8:
      unset($_country_options[0]);
      return $this->getLayout()->createBlock('core/html_select')->setName($_name)->setId($_id)->setClass('required-entry')->setValue('CH')->setOptions($_country_options)->getHtml();
      default:
        return $this->getLayout()->createBlock('core/html_select')->setName($_name)->setId($_id)->setClass('required-entry')->setValue('FR')->setOptions($_country_options)->getHtml();
        break;
    }
  }

  private function _prepareAddress($_address, $_customer) {
    $_address_data = $_address->getData();
    $_email = $_address->getEmail();
    if (isset($_email) === false) $_email = $_customer->getEmail();

    $_country = '';
    if (isset($_address_data['country_id'])) {
      $_country = Mage::getModel('directory/country')->load($_address_data['country_id'])->getName();
    }

    $_street = '';
    if (isset($_address_data['street'])) {
      $_street = explode("\n", $_address_data['street']);
    }

	$_region_code = '';
	if (isset($_address_data['region_id'])) {
		$_region_code = Mage::getModel('directory/region')->load($_address_data['region_id'])->getCode();
	}

    return array(
      'firstname' => isset($_address_data['firstname']) ? $_address_data['firstname'] : '',
      'lastname' => isset($_address_data['lastname']) ? $_address_data['lastname'] : '',
      'city' => isset($_address_data['city']) ? $_address_data['city'] : '',
      'country_id' => isset($_address_data['country_id']) ? $_address_data['country_id'] : '',
      'region' => isset($_address_data['region']) ? $_address_data['region'] : '',
      'region_id' => isset($_address_data['region_id']) ? $_address_data['region_id'] : '',
      'region_code' => $_region_code,
      'country' => $_country,
      'postcode' => isset($_address_data['postcode']) ? $_address_data['postcode'] : '',
      'telephone' => isset($_address_data['telephone']) ? $_address_data['telephone'] : '',
      'email' => $_email,
      'street' => $_street,
    );
  }

  public function getCheckoutOptions() {
    $_result = array();
    $_customer = $this->getCustomer();

    if ($this->isLogin()) {
      $_result['type'] = 1;

      $_result['customer'] = array(
        'email' => $_customer->getEmail(),
      );

      if ($this->hasBillingAddress()) {
        $_result['customer']['defaultBillingAddress'] = array(
          'id' => $_customer->getDefaultBilling(),
          'data' => $this->_prepareAddress(Mage::getModel('customer/address')->load($_customer->getDefaultBilling()), $_customer),
        );
      }
      if ($this->hasShippingAddress()) {
        $_result['customer']['defaultShippingAddress'] = array(
          'id' => $_customer->getDefaultShipping(),
          'data' => $this->_prepareAddress(Mage::getModel('customer/address')->load($_customer->getDefaultShipping()), $_customer),
        );
      }
    } else {
      $_result['type'] = 0;
    }

    $_result['billingAddress'] = $this->_prepareAddress($this->_getCheckout()->getBillingAddress(), $_customer);
    $_result['shippingAddress'] = $this->_prepareAddress($this->_getCheckout()->getShippingAddress(), $_customer);
	
	$_quote = Mage::getSingleton('checkout/session')->getQuote();
	$_result['sac'] = $_quote->getData('sac');
    $_shipping_method = $_quote->getShippingAddress()->getShippingMethod();

    $_result['shippingMethod'] = $_shipping_method;

  //  $this->_getCheckout()->getShippingAddress()->setCollectShippingRates(true)->save();
    
    return json_encode($_result);
  }

  public function hasBillingAddress() {
    $_default_billing = $this->getCustomer()->getDefaultBilling();
    return isset($_default_billing);
  }

  public function hasShippingAddress() {
    $_default_shipping = $this->getCustomer()->getDefaultShipping();
    return isset($_default_shipping);
  }

  public function getAddressTemplate() {
	$_store_code = Mage::app()->getStore()->getCode();
	if ($_store_code == 'uk') {
		$_labels = array(
			$this->__('Name: '),
			$this->__('Address line 1: '),
			$this->__('Address line 2: '),
			$this->__('Locality: '),
			$this->__('City / Town: '),
			$this->__('Postcode: '),
			$this->__('Telephone: ')
		);
	} elseif ($_store_code == 'us') {
		$_labels = array(
			$this->__('Name: '),
			$this->__('Address line 1: '),
			$this->__('Address line 2: '),
			$this->__('State: '),
			$this->__('City: '),
			$this->__('Zipcode: '),
			$this->__('Telephone: '),
		);
	} else {
		$_labels = array(
		  $this->__('Name: '),
		  $this->__('Street / House Nr: '),
		  $this->__('Zipcode / City: '),
		  $this->__('Country: '),
		  $this->__('Telephone: '),
		);
	}

	$_html = '';
	foreach ($_labels as $_label) {
	  $_html .= '<div class="tr"><div class="th">'.$_label.'</div><div class="td">%s</div></div>';
	}
	$_html = '<div class="table table-address"><div class="tbody">' . $_html . '</div></div>';

    return $_html;
  }

  public function getReviewAddressTemplate() {
    $_store_id = Mage::app()->getStore()->getId();
    $_store_code = Mage::app()->getStore()->getCode();

    if ($_store_code == 'uk') {
      $_html = '%s %s <br>%s<br>%s<br>%s<br>%s<br>%s<br>%s';
    } elseif ($_store_code == 'us') {
      $_html = '%s %s <br>%s<br>%s %s %s<br>%s<br>%s';
    } elseif (strpos($_store_code, 'hk') !== false) {
      $_html = '%s %s <br> %s %s<br> %s%s, %s<br> %s';
    } else {
      $_html = '%s %s <br> %s %s<br> %s, %s<br>%s<br> %s';
    }

    return $_html;
  }
}
