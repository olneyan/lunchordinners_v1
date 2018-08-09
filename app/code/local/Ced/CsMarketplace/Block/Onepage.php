<?php
class Ced_Csmarketplace_Block_Onepage extends Mage_Checkout_Block_Onepage 
{
    protected function _getStepCodes()
    {
    	
        return array_diff(parent::_getStepCodes(), array('shipping_method'));
    }
}