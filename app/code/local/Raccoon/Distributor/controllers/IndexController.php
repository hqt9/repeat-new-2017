<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Raccoon_Distributor_IndexController extends Mage_Checkout_CartController{

    public function preDispatch() {
        parent::preDispatch();
        if (!Mage::getSingleton("customer/session")->isLoggedIn())
        {
            $session = Mage::getSingleton("customer/session");
            // Store The Current Page Url Where User will be redirected once loggedin
            $session->setBeforeAuthUrl(Mage::helper("core/url")->getCurrentUrl());
            $this->_redirect('distributor/customer/login/');
            return;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $configValue = Mage::getStoreConfig('distributor_customer_groups/distributor_group/customer_groups',Mage::app()->getStore()->getId());
        $_configValue = explode(",", $configValue);
        if(in_array($customer->getGroupId() , $_configValue) === false){
          $this->_redirect('/');
          return;
        }
    }

    public function IndexAction() {
      $this->loadLayout();
      $this->getLayout()->getBlock("head")->setTitle($this->__("Distributor"));
      $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
        "label" => $this->__("Home Page"),
        "title" => $this->__("Home Page"),
        "link"  => Mage::getBaseUrl()
      ));
// //获取购物车产品总数
// // $_counts = Mage::getModel('checkout/cart')->getQuote()->getItemsQty();

// // $_count = Mage::getModel('checkout/cart')->getQuote()->getItemsCount();

// // //获取所有商品总价格及格式
// // $_prices = Mage::helper('checkout')->formatPrice(Mage::getModel('checkout/cart')->getQuote()->getSubtotal());

// // //获取所有商品+税 总价格及格式
// // $grandTotal = Mage::helper('checkout')->formatPrice(Mage::getModel('checkout/cart')->getQuote()->getGrandTotal());

// // //获取购物车产品所有的信息集合
// // $items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
//     $customer = Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('email', 'jack@example.com')->getData();
//     //购物车中产品个数：  
//     $totalItemsInCart = Mage::helper('checkout/cart')->getItemsCount(); //total items in cart  
//     //  
//     $totals = Mage::getSingleton('checkout/session')->getQuote()->getTotals(); //Total object  
//     //产品总额小计  
//     $subtotal = $totals["subtotal"]->getValue(); //Subtotal value  
//     // echo "subtotal:".$subtotal."<br>";  
//     //订单总额（打折，优惠后的价格）  
//     $grandtotal = $totals["grand_total"]->getValue(); //Grandtotal value  
//     //折扣  
//     // $discount = $totals['discount']->getValue(); //Discount value if applied  
      
//     //tax  
//     // $tax = $totals['tax']->getValue(); //Tax value if present  
//     //shipping amount  
//     $shipping = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShipping_amount();
//     var_dump(Mage::helper('checkout')->formatPrice($shipping));
      $breadcrumbs->addCrumb("distributor", array(
        "label" => $this->__("Distributor"),
        "title" => $this->__("Distributor")
      ));

      $this->renderLayout();
    }


    public function addmultipleAction() {
      $params = $this->getRequest()->getParams();
      $cart = $this->_getCart();
      foreach ($params as $key => $value) {
        # code...
        if(is_numeric($key) ===false || is_numeric($value) === false){
          $this->_goBack();
          return;
        }
        $_params['product'] = $key;
        $_params['qty'] = $value;
        $_params['cpid'] = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_params['product']);

        try {

              $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($_params['product']);
            // $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $_params);
            // if (!empty($related)) {
            //     $cart->addProductsByIds(explode(',', $related));
            // }

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
      }
      $cart->save();
      echo "{'success':true, 'message':''}";
    }
}
