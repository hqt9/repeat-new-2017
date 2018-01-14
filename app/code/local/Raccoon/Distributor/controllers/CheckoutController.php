<?php
class Raccoon_Distributor_CheckoutController extends Mage_Core_Controller_Front_Action{

    public function isRegisterAction() {
        $_params = $this->getRequest()->getParams();
        $_email = $_params['email'];
        $customer = Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('email', $_email)->getData();
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        if($customer[0] && $customer[0]['entity_id'] > 0){
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ]));
        }
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                'success' => false,
                'message' => '',
                'data' => array(),
            ]));
    }

    public function ajaxLoginAction() {
        if (!$this->_validateFormKey()) {
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'message' => '',
                    'data' => array(),
                ]));
        }
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ]));
        }
        if ($this->getRequest()->isPost()) {
            // $login = $this->getRequest()->getParams();
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    $customer = $session->getCustomer();
                    $session->setCustomerAsLoggedIn($customer);
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                            'success' => true,
                            'message' => '',
                            'data' => array(),
                        ]));
                } catch(Exception $ex) {
                }
            }
        }
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                'success' => false,
                'message' => '',
                'data' => array(),
            ]));
    }

//     public function IndexAction() {
//       $this->loadLayout();
//       $this->getLayout()->getBlock("head")->setTitle($this->__("Distributor"));
//       $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
//       $breadcrumbs->addCrumb("home", array(
//         "label" => $this->__("Home Page"),
//         "title" => $this->__("Home Page"),
//         "link"  => Mage::getBaseUrl()
//       ));
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
//       $breadcrumbs->addCrumb("distributor", array(
//         "label" => $this->__("Distributor"),
//         "title" => $this->__("Distributor")
//       ));

//       $this->renderLayout();
//     }

}
