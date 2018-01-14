<?php   
class Raccoon_Distributor_Block_Index extends Mage_Core_Block_Template{

    public function getProductList(){
        $_productCollection = Mage::getModel('catalog/product')->getCollection()
                            ->setStoreId(Mage::app()->getStore()->getId())
                            // ->addAttributeToFilter('price', array('gt' => '740'))
                            ->addAttributeToFilter('type_id', array('eq' => 'simple'))
                            ->addAttributeToFilter('status', array('eq' => '1'))
                            ->addAttributeToSelect('*')->load();
        return $_productCollection;
    }

    protected function getLoadedProductCollection()
    {
        // if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            // /* @var $layer Mage_Catalog_Model_Layer */
            // if ($this->getShowRootCategory()) {
            //     $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            // }

            // if (Mage::registry('product')) {
            //     /** @var Mage_Catalog_Model_Resource_Category_Collection $categories */
            //     $categories = Mage::registry('product')->getCategoryCollection()
            //         ->setPage(1, 1)
            //         ->load();
            //     if ($categories->count()) {
            //         $this->setCategoryId($categories->getFirstItem()->getId());
            //     }
            // }

            // $origCategory = null;
            // if ($this->getCategoryId()) {
            //     $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
            //     if ($category->getId()) {
            //         $origCategory = $layer->getCurrentCategory();
            //         $layer->setCurrentCategory($category);
            //         $this->addModelTags($category);
            //     }
            // }
            $this->_productCollection = $layer->getProductCollection();

        //     $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        //     if ($origCategory) {
        //         $layer->setCurrentCategory($origCategory);
        //     }
        // }

        return $this->_productCollection;
    }

    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }
}