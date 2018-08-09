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
class Ced_CsMarketplace_Block_Widget_Grid_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('csmarketplace/widget/grid/massaction.phtml');
		$this->setErrorText(Mage::helper('catalog')->jsQuoteEscape(Mage::helper('catalog')->__('Please select items.')));
	}
	
	public function getJavaScript()
	{
		
		return "
			var {$this->getJsObjectName()} = new varienGridMassaction('{$this->getHtmlId()}', {$this->getGridJsObjectName()}, '{$this->getSelectedJson()}', '{$this->getFormFieldNameInternal()}', '{$this->getFormFieldName()}');
			{$this->getJsObjectName()}.setItems({$this->getItemsJson()});"
			. "{$this->getJsObjectName()}.setGridIds('{$this->getGridIdsJson()}');"
			. ($this->getUseAjax() ? "{$this->getJsObjectName()}.setUseAjax(true);" : '') . "
			". ($this->getUseSelectAll() ? "{$this->getJsObjectName()}.setUseSelectAll(true);" : '') .
			"{$this->getJsObjectName()}.errorText = '{$this->getErrorText()}';";
	}
	
	public function getGridIdsJson()
	{
		if (!$this->getUseSelectAll()) {
			return '';
		}
		$gridIds = $this->getParentBlock()->getCollection()->getAllIds();
		if(count($gridIds) == 0) {
			foreach($this->getParentBlock()->getCollection() as $item) {
				$gridIds[] = $item->getId();
			}
		}
	
		if(!empty($gridIds)) {
			return join(",", $gridIds);
		}
		return '';
	}
}
