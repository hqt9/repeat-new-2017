<?php

class Ecominfinity_Repeatgroup_AjaxController extends Mage_Core_Controller_Front_Action {
  private function _prepare_result($_is_success, $_message, $_data) {
    return json_encode(
      array(
        'success' => $_is_success,
        'message' => $_message,
        'data' => $_data
      )
    );
  }

  protected function _get_session() {
    return Mage::getSingleton('repeatgroup/session');
  }

  public function dismissAction() {
    $this->_get_session()->setData('is_dismissed', 'true');
    $this->getResponse()->setBody(
      json_encode(array('result'=>true))
    );
  }

  public function customerExistedAction() {
    $_email = $this->getRequest()->getParam('email');

    if (! Zend_Validate::is($_email, 'EmailAddress')) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $this->__('Not a valid email address.'), array())
      );
      return;
    }

    $_website_id = Mage::app()->getWebsite()->getId();
    $_customer = Mage::getModel('customer/customer')->setWebsiteId($_website_id)->loadByEmail($_email);
    if ($_customer->getId()) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $this->__('The email address has been already used.'), array())
      );
      return;
    }

    $this->getResponse()->setBody(
      $this->_prepare_result(true, '', array())
    );
    return;
  }

  private function _get_cart() {
    return Mage::getSingleton('checkout/cart');
  }

  private function _init_product() {
    $_product_id = (int) $this->getRequest()->getParam('product');
    if ($_product_id) {
      $_product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($_product_id);
      if ($_product->getId()) {
        return $_product;
      }
    }
    return false;
  }

  private function _get_cart_items($_qualified_giveaway = false) {
    $_result = array();

    $_count = Mage::helper('checkout/cart')->getSummaryCount();
    if ($_count <= 1) {
      $_result['summary'] = $this->__('%d Item', $_count);
    } else {
      $_result['summary'] = $this->__('%d Items', $_count);
    }
    $_result['count'] = $_count;

    $_quote = Mage::getModel('checkout/cart')->getQuote();
    $_result['title'] = $this->__('My shopping bag (%d)', $_count);

    $_result['items'] = array();

    foreach ($_quote->getAllVisibleItems() as $_item) {
      $_product = $_item->getProduct();
      $_sku = $_item->getSku();
      $_size = explode('-', $_sku)[2];

      $_result['items'][] = array(
        'sku' => $_sku,
        'image' => (string) Mage::helper('catalog/image')->init($_product, 'thumbnail')->resize(40 * 4, 65 * 4),
        'id' => $_item->getId(),
        'name' => $_item->getName(),
        'size' => $_size,
        'qty' => $_item->getData('qty'),
        'row' => Mage::helper('core')->currency($_item->getData('row_total_incl_tax'), true, false),
      );
    }

    $_result['total'] = Mage::helper('core')->currency($_quote->getData('grand_total'), true, false);
    $_result['giveaway'] = $_qualified_giveaway;

    return $_result;
  }

  public function deleteItemAction() {
    $_id = (int) $this->getRequest()->getParam('id');

    if ($_id) {
      try {
        $this->_get_cart()->removeItem($_id)->save();
      } catch (Exception $e) {
        $this->getResponse()->setBody(
          $this->_prepare_result(
            false, $e->getMessage(), null
          )
        );
        return;
      }

      $this->getResponse()->setBody(
        $this->_prepare_result(
          true, '', 
          $this->_get_cart_items()
        )
      );
    }
  }
  private function _get_cart_message() {
    $_product_id = Mage::getSingleton('checkout/session')->getLastAddedProductId(true);
    $_product = Mage::getModel('catalog/product')->load($_product_id);
    return $this->__('"%s" has been added to your shopping cart.', $_product->getData('name'));
  }

  public function addToCartAction() {
    $_cart = $this->_get_cart();
    $_params = $this->getRequest()->getParams();

    try {
      if (isset($_params['qty']) == true) {
        $filter = new Zend_Filter_LocalizedToNormalized(
          array('locale' => Mage::app()->getLocale()->getLocaleCode())
        );
        $_params['qty'] = $filter->filter($_params['qty']);
      }

      $_product = $this->_init_product();
      if ($_product == false) {
        $this->getResponse()->setBody(
          $this->_prepare_result(
            false, $this->__('Product not existed'), null
          )
        );
        return;
      }

      $_cart->addProduct($_product, $_params);
      $_cart->save();

      // $_spend_product_y_ids = explode(',', Mage::getStoreConfig('giveaway/general/spend_producty_product_id'));

      $_quote = Mage::getModel('checkout/cart')->getQuote();
      $_items = $_quote->getAllItems();

      $_sql = <<<EOD
        select count(*) from `catalog_category_product_index`
        where `category_id` = 51 and `product_id` in (%s);
EOD;

      $_store_id = Mage::app()->getStore()->getId();
      $_totals = $_quote->getTotals();
      $_subtotal = $_totals["subtotal"]->getValue();

      // men's book promotion
      $_subtotal_threshold = [0, 159, 159, 159, 159, 159, 159, 159, 159, 1590, 0, 1590, 0, 1590];
      $_product_y_id = 56477;
      $_giveaway_product_ids = [56477, 56479];

      // cashmere care set promotion
      // $_subtotal_threshold = [0, 179, 179, 179, 179, 179, 179, 179, 179, 1590, 0, 1590, 0, 1590];
      // $_product_y_id = 57481;
      // $_giveaway_product_ids = [57481, 57483];

      // skiny pants promotion
      // $_subtotal_threshold = [0, 299, 299, 299, 299, 299, 299, 299, 299, 2599, 0, 2599, 0, 2599];
      // $_product_y_id = 51353;
      // $_giveaway_product_ids = [56477, 56001, 56003, 56005, 56007, 56009, 56011];

      // scarves promotion
      // $_subtotal_threshold = [0, 359, 359, 359, 359, 359, 359, 359, 359, 2599, 0, 2599, 0, 2599];
      // $_product_y_id = 51353;
      // $_giveaway_product_ids = [
      //   57517, 57519, 57521, 57523, 57525, 57527, 57529, 57531, 57533, 57535, 57537, 57539, 57541, 57543, 57545,
      //   57591, 57593, 57595, 57597, 57599, 57601, 57603, 57605, 57607, 57609, 57611, 57613, 57615, 57617, 57619,
      // ];

      // printed scarves promotion
      // $_subtotal_threshold = [0, 159, 159, 159, 159, 159, 159, 159, 159, 1599, 0, 1599, 0, 1599];
      // $_product_y_id = 51353;
      // $_giveaway_product_ids = [57337, 57335, 56775, 56773];

      // mens t-shirt promotion
      // $_subtotal_threshold = [0, 199, 199, 199, 199, 199, 199, 199, 199, 1390, 0, 1390, 0, 1390];
      // $_product_y_id = 51353;
      // $_giveaway_product_ids = [56781, 56779, 56777, 57361, 57359, 57357, 57355, 57353, 57351, 57349, 57347, 57345, 57343, 57341, 57339];

      if ($_subtotal >= $_subtotal_threshold[$_store_id]) {

        $_product_ids = [];
        $_already_giveaway = false;
        foreach ($_items as $_item) {
          $_product_id = $_item->getProduct()->getId();
          $_product_ids[] = $_product_id;

          if (in_array($_product_id, $_giveaway_product_ids)) {
            $_already_giveaway = true;
          }
        }

        $_query = sprintf($_sql, implode(',', $_product_ids));
        $_resource = Mage::getSingleton('core/resource');
        $_read_connection = $_resource->getConnection('core_read');

        $_result = $_read_connection->fetchAll($_query);
        $_count = $_result[0]['count(*)'];

        $_qualified_giveaway = 
            (($_already_giveaway === false && $_count > 0) || 
            ($_already_giveaway === true && $_count > 1));

        if ($_qualified_giveaway) {
          $_product = Mage::getModel('catalog/product')->load($_product_y_id);
          $_params = ['qty' => 1, 'super_attribute' => [592 => 293]];
          $_cart->addProduct($_product, $_params);
          $_cart->save();
        }
      }

      Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
      
      $this->getResponse()->setBody(
        $this->_prepare_result(
          true, 
          $this->_get_cart_message(), 
          $this->_get_cart_items($_qualified_giveaway)
        )
      );
      return;
      
    } catch (Exception $e) {
       $this->getResponse()->setBody(
          $this->_prepare_result(
            false, $e->getMessage(), null
          )
        );

    }
  }

  protected function _getQuote() {
    return $this->_get_cart()->getQuote();
  }

  public function promotionAction() {
    $_code = (string) $this->getRequest()->getParam('q');

    // giftcard code
    $_is_giftcard_code = false;
    $_is_coupon_code = false;
    try {
      Mage::getModel('enterprise_giftcardaccount/giftcardaccount')->loadByCode($_code)->addToCart();
      $this->_getQuote()->collectTotals()->save();
      $_is_giftcard_code = true;
    } catch(Mage_Core_Exception $e) {
      Mage::dispatchEvent('enterprise_giftcardaccount_add', array('status' => 'fail', 'code' => $code));
    } catch(Exception $e) {
    }

    // coupon code
    if (! $_is_giftcard_code) {
      $_old_coupond_code = $this->_getQuote()->getCouponCode();
      $_code_length = strlen($_code);
      $_is_code_length_valid = $_code_length && $_code_length <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

      if ($_is_code_length_valid) {
        $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->_getQuote()->setCouponCode($_code)->collectTotals()->save();

        if ($_code == $this->_getQuote()->getCouponCode()) {
          $_is_coupon_code = true;
        }
      }
    }

    if ($_is_giftcard_code || $_is_coupon_code) {
      $total = $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();

      $layout = $this->getLayout();
      $update = $layout->getUpdate();
      $update->load('checkout_onepage_paymentmethod');
      $layout->generateXml();
      $layout->generateBlocks();
      $output = $layout->getOutput();

      $this->getResponse()->setBody(
        $this->_prepare_result(
          true, '', 
          [
            'payment' => $output,
            'total' => $total
          ])
      );
    } else {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $this->__('Cannot apply gift card / coupon code.'), [])
      );
    }

  }

  public function couponPostAction() {
    $couponCode = (string) $this->getRequest()->getParam('coupon');
    $oldCouponCode = $this->_getQuote()->getCouponCode();

    try {
      $codeLength = strlen($couponCode);
      $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

      $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
      $this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
                        ->collectTotals()
                        ->save();

      if ($codeLength) {
        if ($isCodeLengthValid && $couponCode == $this->_getQuote()->getCouponCode()) {
          $total = $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();
          $this->getResponse()->setBody(
            $this->_prepare_result(true, '', array('total' => $total))
          );
          return;
        } else {
          $this->getResponse()->setBody(
            $this->_prepare_result(false, $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode)), array())
          );
          return;
        }
      } 

    } catch (Mage_Core_Exception $e) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $e->getMessage(), Mage::helper('core')->escapeHtml($couponCode), array())
      );
      return;
    } catch (Exception $e) {
      $this->getResponse()->setBody(
        $this->_prepare_result(false, $this->__('Cannot apply the coupon code.'), array())
      );
      Mage::logException($e);
      return;
    }
  }

  public function giftcardAction() {
    $data = $this->getRequest()->getPost();
    if (isset($data['coupon'])) {
        $code = $data['coupon'];
        try {
          Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
              ->loadByCode($code)
              ->addToCart();

          $this->_getQuote()->collectTotals()->save();

          $total = $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();

          $layout = $this->getLayout();
          $update = $layout->getUpdate();
          $update->load('checkout_onepage_paymentmethod');
          $layout->generateXml();
          $layout->generateBlocks();
          $output = $layout->getOutput();

          $this->getResponse()->setBody(
            $this->_prepare_result(
              true, '', 
              [
                'payment' => $output,
                'total' => $total
              ])
          );
          return;
        } catch (Mage_Core_Exception $e) {
          Mage::dispatchEvent('enterprise_giftcardaccount_add', array('status' => 'fail', 'code' => $code));
          $this->getResponse()->setBody(
            $this->_prepare_result(false, $e->getMessage(), array())
          );
          return;
        } catch (Exception $e) {
          $this->getResponse()->setBody(
            $this->_prepare_result(false, $e->getMessage(), $this->__('Cannot apply gift card.'), array())
          );
          return;
        }
    }
  }
}
