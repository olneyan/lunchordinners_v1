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
 * @category    Ced;
 * @package     Ced_AdvFlatRate 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_AdvFlatRate_System_ConfigController  extends Mage_Adminhtml_Controller_Action
{
	

	/**
	 * Export Rates CSV Action
	 *
	 */
    public function exportRatesCsvAction()
    {
        $fileName   = 'advancerate.csv';

        $gridBlock  = $this->getLayout()->createBlock('advflatrate/adminhtml_advance_grid');
        $website    = Mage::app()->getWebsite($this->getRequest()->getParam('website'));

        $conditionName = $this->getVendorId();
 
        $gridBlock->setWebsiteId($website->getId())->setConditionName($conditionName);
		$content    = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
	
    /**
     * Get Vendor Id
     *
     */
	public function getVendorId()
    {
		return 'admin';
	}
}