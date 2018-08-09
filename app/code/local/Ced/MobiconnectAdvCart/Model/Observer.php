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
    class Ced_MobiconnectAdvCart_Model_Observer{
        
        public function addField(Varien_Event_Observer $observer)
        {
            $block = $observer->getEvent()->getBlock();
            if (!isset($block)) {
                return $this;
            }
            if ($block->getType() == 'mobiconnect/adminhtml_mobiconnect_edit_tab_bannerinfo') {
                $form = $block->getForm();
                //create new custom fieldset 'website'
                 $fieldset = $form->getElement('banners_form');
                 $fieldset->addField('note', 'label', array(
                    'label'     => Mage::helper('mobiconnectadvcart')->__('Note'),
                    'name'      => 'note',
                    'value'    => 'Image Size should be'
                ));
            }
        }
        public function bannerSaveBefore(Varien_Event_Observer $observer){
         $eventData =$observer->getEvent ();
         $data=$eventData->getData();
         $uploader=$eventData->getUploader();
         $uploader->addValidateCallback('size', $this, 'validateMaxSize');
        }
        public function validateMaxSize($filePath){
            
            $image_info = getimagesize($filePath);
            $image_width = $image_info[0];
            $image_height = $image_info[1];
            if ($image_width > 1500 || $image_height>1600) {
                Mage::throwException('Image Dimension Does not meet the specified criteria');
            }
        }
    }
?>