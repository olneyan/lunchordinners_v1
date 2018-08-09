<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectAdvCart_Block_Adminhtml_Mobiconnect_Edit_Tab_Bannerinfo extends Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Edit_Tab_Bannerinfo
{

	/**
	 * prepare tab form's information
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Edit_Tab_Bannerinfo
	 */
    
	
    protected function _prepareForm()
    {
       
        parent::_prepareForm();
        $form = $this->getForm();
        $fieldset = $form->getElement('banners_form');
        $fieldset->addField('newattr', 'text',
            array(
                'label' => Mage::helper('customer')->__('Select type'),
                'class' => 'input-text',
                'name'  => 'newattr',
                'required' => false
            )
        );
        $this->setForm($form);
        return $this;

    }
}
?>