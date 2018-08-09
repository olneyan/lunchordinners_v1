<?php 

/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsVendorReview
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

 
class Ced_CsVendorReview_Block_Rating_List extends Mage_Core_Block_Template
{
	
	/**
	 * Initialize Constructor
	 */
	public function __construct()
    {		
        parent::__construct();					
        $reviews = Mage::getModel('csvendorreview/review')->getCollection()
					->addFieldToFilter('vendor_id',$this->getRequest()->getParam('id'))
					->addFieldToFilter('status',1)
					->setOrder('created_at', 'desc');
		$this->setReviews($reviews);       
    }
	
	/**
	 * Prepare Layout
	 */
	protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($toolbar = $this->getLayout()->createBlock('page/html_pager')) {
			
            $toolbar->setCollection($this->getReviews());
            $this->setChild('toolbar', $toolbar);
			$this->getReviews()->load();
        }
        return $this;
    }

}
