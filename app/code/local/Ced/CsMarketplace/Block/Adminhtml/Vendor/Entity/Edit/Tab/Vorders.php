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
class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Edit_Tab_Vorders extends Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid
{
	public function __construct()
    {
	  parent::__construct();
	  $vendor_id = $this->getRequest()->getParam('vendor_id',0);
	  $this->setId('vordersGrids_'.$vendor_id);
	  $this->setDefaultSort('created_at');
	  $this->setDefaultDir('DESC');
	  $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
    }
	
	protected function _prepareColumns()
	{
		parent::_prepareColumns();
		$this->removeColumn('vendor_id');
		return $this;
	}
	
	public function getGridUrl() {
        return $this->getUrl('*/*/vordersgrid', array('_secure'=>true, '_current'=>true));
    }
	
	/**
	 * Remove existing column
	 *
	 * @param string $columnId
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	public function removeColumn($columnId)
	{
		if (isset($this->_columns[$columnId])) {
			unset($this->_columns[$columnId]);
			if ($this->_lastColumnId == $columnId) {
				$this->_lastColumnId = key($this->_columns);
			}
		}
		return $this;
	}
}