<?php

class AAOO_Guestwishlist_IndexController extends Mage_Wishlist_Controller_Abstract {
  public function preDispatch() {
    parent::preDispatch();

    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
      $this->getRequest()->setRouteName('wishlist')
           ->setDispatched(false);
    }
  }

  protected function _getWishlist() {
    return Mage::helper('guestwishlist')->getWishlist();
  }

  public function indexAction() {
    $this->loadLayout();

    $session = Mage::getSingleton('customer/session');
    $block   = $this->getLayout()->getBlock('customer.wishlist');
    $referer = $session->getAddActionReferer(true);
    if ($block) {
      $block->setRefererUrl($this->_getRefererUrl());
      if ($referer) {
        $block->setRefererUrl($referer);
      }
    }

    $this->_initLayoutMessages('customer/session');
    $this->_initLayoutMessages('checkout/session');
    $this->_initLayoutMessages('catalog/session');
    $this->_initLayoutMessages('guestwishlist/session');

    $this->renderLayout();
  }

  public function addAction() {
    $session = Mage::getSingleton('customer/session');
    $wishlist = $this->_getWishlist();
        
    $productId = (int) $this->getRequest()->getParam('product');
    if (!$productId) {
      $this->_redirect('*/');
      return;
    }

    $product = Mage::getModel('catalog/product')->load($productId);
    if (!$product->getId() || !$product->isVisibleInCatalog()) {
      $session->addError(Mage::helper('wishlist')->__('Cannot specify product.'));
      $this->_redirect('*/');
      return;
    }

    try {
      $requestParams = $this->getRequest()->getParams();
      if ($session->getBeforeWishlistRequest()) {
        $requestParams = $session->getBeforeWishlistRequest();
        $session->unsBeforeWishlistRequest();
      }
      $buyRequest = new Varien_Object($requestParams);
      
      $result = $wishlist->addNewItem($product, $buyRequest);
      if (is_string($result)) {
        Mage::throwException($result);
      }            

      $referer = $session->getBeforeWishlistUrl();
      if ($referer) {
        $session->setBeforeWishlistUrl(null);
      } else {
        $referer = $this->_getRefererUrl();
      }

      $session->setAddActionReferer($referer);

      $message = Mage::helper('wishlist')->__(
                    '%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping',
                    $product->getName(), 
                    Mage::helper('core')->escapeUrl($referer)
                );
      $session->addSuccess($message);
    } catch (Mage_Core_Exception $e) {
      $session->addError(Mage::helper('wishlist')->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
    } catch (Exception $e) {
      $session->addError(Mage::helper('wishlist')->__('An error occurred while adding item to wishlist.'));
    }
    
    $res = Mage::helper('guestwishlist')->saveWishlist($wishlist);
    if (true === $res){     
      $this->_redirect('*');
    } else {
      $session->addError($res);
    }
  }
}