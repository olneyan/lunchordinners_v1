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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Vendor form model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Form extends Ced_CsMarketplace_Model_Abstract {

	public static $VENDOR_FORM_READONLY_ATTRIBUTES = array(
											'shop_url',
											'created_at',
											'status',
											'group',
											
										);
										
	public static $VENDOR_FORM_EDITABLE_ATTRIBUTES = array(
											'shop_url',
											'name',
											'gender',
											'profile_picture',
											'contact_number',
											'company_name',
											'about',
											'company_logo',
											'company_banner',
											'company_address',
											'support_number',
											'support_email',
											'email'
							
										);
	public static $VENDOR_FORM_NONEDITABLE_ATTRIBUTES = array(
											'shop_url',
											'status',
											'group',
											'created_at',
											'shop_disable',
										);
										
	public static $VENDOR_PROFILE_RESTRICTED_ATTRIBUTES = array(
											'customer_id',
										);	

	public static $VENDOR_REGISTRATION_RESTRICTED_ATTRIBUTES = array(
											'customer_id',
											'name',
											'gender',
											'created_at',
											'website_id',
											'email'
										);
}
