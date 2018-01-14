<?php

class AAOO_Guestwishlist_Block_Render_Item_Price extends Mage_Core_Block_Template {
    public function getCleanProductPriceHtml() {
        $renderer = $this->getCleanRenderer();
        if (!$renderer) {
            return '';
        }

        $product = $this->getProduct();
        if ($product->canConfigure()) {
            $product = clone $product;
            $product->setCustomOptions([]);
        }

        return $renderer->setProduct($product)
                        ->setDisplayMinimalPrice($this->getDisplayMinimalPrice())
                        ->setIdSuffix($this->getIdSuffix())
                        ->toHtml();
    }
}
