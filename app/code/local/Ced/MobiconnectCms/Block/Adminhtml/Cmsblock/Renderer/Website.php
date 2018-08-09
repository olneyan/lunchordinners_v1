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
  * @package   Ced_MobiconnectCms
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectCms_Block_Adminhtml_Cmsblock_Renderer_Website extends
Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * Render Website name
	 *
	 * @return $row
	 */
	public function render(Varien_Object $row)
	{
		return $this->_getValue($row);
	}
	public function _getValue(Varien_Object $row)
	{
		$val = $row->getData($this->getColumn()->getIndex());
		//$pathexplode=explode('/',$val);
		$collection = Mage::getModel('core/website')->getCollection()->addFieldToFilter('website_id',$val);
		
		foreach ($collection as $v)
		{
			echo $path= $v->getName();
		}
	}
}
?>
