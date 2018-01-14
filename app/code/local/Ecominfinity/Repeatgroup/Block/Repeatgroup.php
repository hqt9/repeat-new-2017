<?php
class Ecominfinity_Repeatgroup_Block_Repeatgroup extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getRepeatgroup()     
     { 
        if (!$this->hasData('repeatgroup')) {
            $this->setData('repeatgroup', Mage::registry('repeatgroup'));
        }
        return $this->getData('repeatgroup');
        
    }
}