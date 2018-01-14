<?php

abstract class AAOO_Guestwishlist_Block_Abstract extends Mage_Catalog_Block_Product_Abstract {
    protected $_collection;
    protected $_wishlist;
    protected $_itemPriceBlockTypes = [];
    protected $_cachedItemPriceBlocks = [];

    protected function _construct() {
        parent::_construct();
        $this->addItemPriceBlockType('default', 'guestwishlist/render_item_price', 'guestwishlist/render/item/price.phtml');
    }

    protected function _getHelper() {
        return Mage::helper('guestwishlist');
    }

    protected function _getCustomerSession() {
        return Mage::getSingleton('customer/session');
    }

    protected function _getWishlist() {
        return $this->_getHelper()->getWishlist();
    }

    protected function _prepareCollection($collection) {
        return $this;
    }

    public function getWishlistItems() {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_getWishlist()
                ->getItemCollection();
            $this->_prepareCollection($this->_collection);
        }
        return $this->_collection;
    }

    public function getWishlist() {
        return $this->getWishlistItems();
    }

    public function getItemRemoveUrl($item) {
        return $this->_getHelper()->getRemoveUrl($item);
    }
  
    public function getItemRemoveUrlAjax($item) {
        return $this->_getHelper()->getRemoveUrlAjax($item);
    }

    public function getItemAddToCartUrl($item) {
        return $this->_getHelper()->getAddToCartUrl($item);
    }

    public function getSharedItemAddToCartUrl($item) {
        return $this->_getHelper()->getSharedAddToCartUrl($item);
    }

    public function getAddToWishlistUrl($product) {
        return $this->_getHelper()->getAddUrl($product);
    }

    public function getItemConfigureUrl($product) {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $id = $product->getWishlistItemId();
        } else {
            $id = $product->getId();
        }
        $params = array('id' => $id);
        return $this->getUrl('guestwishlist/index/configure/', $params);
    }

    public function getEscapedDescription($item) {
        if ($item->getDescription()) {
            return $this->escapeHtml($item->getDescription());
        }
        return '&nbsp;';
    }

    public function hasDescription($item) {
        return trim($item->getDescription()) != '';
    }

    public function getFormatedDate($date) {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    public function isSaleable() {
        foreach ($this->getWishlistItems() as $item) {
            if ($item->getProduct()->isSaleable()) {
                return true;
            }
        }
        return false;
    }

    public function getWishlistItemsCount() {
        return $this->_getWishlist()->getItemsCount();
    }

    public function getQty($item) {
        $qty = $item->getQty() * 1;
        if (!$qty) {
            $qty = 1;
        }
        return $qty;
    }

    public function hasWishlistItems() {
        return $this->getWishlistItemsCount() > 0;
    }

    /**
     * Adds special block to render price for item with specific product type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     */
    public function addItemPriceBlockType($type, $block = '', $template = '') {
        if ($type) {
            $this->_itemPriceBlockTypes[$type] = array(
                'block' => $block,
                'template' => $template
            );
        }
    }

    protected function _getItemPriceBlock($productType) {
        if (!isset($this->_itemPriceBlockTypes[$productType])) {
            $productType = 'default';
        }

        if (!isset($this->_cachedItemPriceBlocks[$productType])) {
            $blockType = $this->_itemPriceBlockTypes[$productType]['block'];
            $template = $this->_itemPriceBlockTypes[$productType]['template'];
            $block = $this->getLayout()->createBlock($blockType)
                ->setTemplate($template);
            $this->_cachedItemPriceBlocks[$productType] = $block;
        }
        return $this->_cachedItemPriceBlocks[$productType];
    }

    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '') {
        $type_id = $product->getTypeId();
        if (Mage::helper('catalog')->canApplyMsrp($product)) {
            $realPriceHtml = $this->_preparePriceRenderer($type_id)
                ->setProduct($product)
                ->setDisplayMinimalPrice($displayMinimalPrice)
                ->setIdSuffix($idSuffix)
                ->setIsEmulateMode(true)
                ->toHtml();
            $product->setAddToCartUrl($this->getAddToCartUrl($product));
            $product->setRealPriceHtml($realPriceHtml);
            $type_id = $this->_mapRenderer;
        }

        return $this->_preparePriceRenderer($type_id)
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }

    public function getProductUrl($item, $additional = array())
    {
        if ($item instanceof Mage_Catalog_Model_Product) {
            $product = $item;
        } else {
            $product = $item->getProduct();
        }
        $buyRequest = $item->getBuyRequest();
        if (is_object($buyRequest)) {
            $config = $buyRequest->getSuperProductConfig();
            if ($config && !empty($config['product_id'])) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getStoreId())
                    ->load($config['product_id']);
            }
        }
        return parent::getProductUrl($product, $additional);
    }
}
