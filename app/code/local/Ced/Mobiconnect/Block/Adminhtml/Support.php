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
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnect_Block_Adminhtml_Support extends Mage_Adminhtml_Block_Template {
	public function __construct() {
		
	}
  protected function getHeader()
    {
        return Mage::helper('mobiconnect')->__('Mobiconnect Support');
    }
     public function getSaveFormAction()
    {
        return $this->getUrl('*/*/submit');
    }
   protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('mobiconnect')->__('Submit Request'),
                    'onclick'   => 'requestForm.submit();',
                    'class'     => 'save'
        )));
        return parent::_prepareLayout();
    }
  protected function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }
}
?>