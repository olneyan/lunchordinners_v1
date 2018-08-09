<?php
class Ced_CsMarketplace_Model_Vendor_Address_Source_Region extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    public function getAllOptions()
    {
        return array(array('label'=>'Please select region, state or province','value'=>''));

    }
}

?>
