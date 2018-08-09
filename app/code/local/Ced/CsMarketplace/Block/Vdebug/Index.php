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
 * CsMarketplace Orders List block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vdebug_Index extends Mage_Core_Block_Template
{
  	/**
	 * getdownload link processed log
	 *
	 */
   function getProcessDataLink(){
		return Mage::getUrl('csmarketplace/vdebug/downloadProceesedLog',array('_secure'=>true,'_nosid'=>true));
   }
  	/**
	 * getdownload link exception log
	 *
	 */   
   function getExceptionDataLink(){
		return Mage::getUrl('csmarketplace/vdebug/downloadExceptionLog',array('_secure'=>true,'_nosid'=>true));
   }
  	/**
	 * get admin create link processed log
	 *
	 */   
   function getcreateAdminLink(){
		return Mage::getUrl('csmarketplace/vdebug/createAdmin',array('_secure'=>true,'_nosid'=>true));
   }
}
