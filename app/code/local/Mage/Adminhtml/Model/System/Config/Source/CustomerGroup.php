<?php
class Mage_Adminhtml_Model_System_Config_Source_CustomerGroup
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = Mage::getModel('customer/group')->getCollection()->addFieldToFilter('customer_group_id', array('neq' => 0));

        $options = array();

        // if ($addEmpty) {
        //     $options[] = array(
        //         'label' => Mage::helper('adminhtml')->__('-- Please Select a Category --'),
        //         'value' => ''
        //     );
        // }
        foreach ($collection as $groups) {
            $options[] = array(
               'label' => $groups->getCustomerGroupCode(),
               'value' => $groups->getCustomerGroupId()
            );
        }

        return $options;
    }

}

