<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsVAttribute
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

$registrationFormAttributes = array('public_name'=>10,'shop_url'=>20);
$profileDisplayAttributes = array(
								'public_name'=>array('sort_order'=>10,'fontawesome'=>'','store_label'=>'Public Name'),
								'support_number'=>array('sort_order'=>20,'fontawesome'=>'fa fa-mobile','store_label'=>'Tel'),
								'support_email'=>array('sort_order'=>30,'fontawesome'=>'fa fa-envelope-o','store_label'=>'Support Email'),
								'email'=>array('sort_order'=>35,'fontawesome'=>'fa fa-envelope-o','store_label'=>'Email'),
								'company_name'=>array('sort_order'=>40,'fontawesome'=>'fa fa-building','store_label'=>'Company'),
								'name'=>array('sort_order'=>50,'fontawesome'=>'fa fa-user','store_label'=>'Representative'),
								'company_address'=>array('sort_order'=>60,'fontawesome'=>'fa fa-location-arrow','store_label'=>'Location'),
								'created_at'=>array('sort_order'=>70,'fontawesome'=>'fa fa-calendar','store_label'=>'Vendor Since'),
								'facebook_id'=>array('sort_order'=>80,'fontawesome'=>'fa fa-facebook-square','store_label'=>'Find us on Facebook'),
								'twitter_id'=>array('sort_order'=>90,'fontawesome'=>'fa fa-twitter','store_label'=>'Follow us on Twitter')
							);
$vendorAttributes = Mage::getModel('csmarketplace/vendor_form')->getCollection();

if(count($vendorAttributes) > 0) {
	$storesViews = Mage::getModel('core/store')->getCollection();
	foreach($vendorAttributes as $vendorAttribute) {
		$vendorMainAttribute = Mage::getModel('csmarketplace/vendor_attribute')->load($vendorAttribute->getAttributeId());
		$isSaveNeeded = false;
		if(isset($registrationFormAttributes[$vendorAttribute->getAttributeCode()]))
		{
			$vendorAttribute->setData('use_in_registration',1);
			$vendorAttribute->setData('position_in_registration',$registrationFormAttributes[$vendorAttribute->getAttributeCode()]);
			$isSaveNeeded = true;
		}
		if(isset($profileDisplayAttributes[$vendorAttribute->getAttributeCode()]))
		{
			$frontend_label[0] = $vendorMainAttribute->getFrontendLabel();
			foreach($storesViews as $storesView){
				$frontend_label[$storesView->getId()] = $profileDisplayAttributes[$vendorAttribute->getAttributeCode()]['store_label'];
			}
			$vendorAttribute->setData('use_in_left_profile',1);
			$vendorAttribute->setData('position_in_left_profile',$profileDisplayAttributes[$vendorAttribute->getAttributeCode()]['sort_order']);
			$vendorAttribute->setData('fontawesome_class_for_left_profile',$profileDisplayAttributes[$vendorAttribute->getAttributeCode()]['fontawesome']);
			$vendorMainAttribute->setData('frontend_label',$frontend_label);
			$isSaveNeeded = true;
		}
		if($isSaveNeeded){	
			$vendorAttribute->save();
			$vendorMainAttribute->save();
			$isSaveNeeded = false;
		}
	}
}

$installer->endSetup();