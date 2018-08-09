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
 * CsMarketplace Products Report List block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vreports_Vproducts_List extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	protected $_filtercollection;
	/**
	 * Get set collection of Products report
	 *
	 */
	public function __construct(){
		parent::__construct();
		$productsCollection = array();
		$reportHelper = Mage::helper('csmarketplace/report');
		$params = Mage::getSingleton('core/session')->getData('vproducts_reports_filter');
		if(isset($params)&&$params!=null){
			$productsCollection=$reportHelper->getVproductsReportModel($this->getVendor()->getId(),$params['from'],$params['to']);
			if(count($productsCollection)>0){
				$this->_filtercollection=$productsCollection;
				$this->setVproductsReports($this->_filtercollection);
			}
		}
	}
	
	
}
