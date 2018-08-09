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
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'banners_tabs' );
		$this->setDestElementId ( 'edit_form' );
		$this->setTitle ( Mage::helper ( 'mobiconnect' )->__ ( 'Banner Information' ) );
	}
	
	/**
	 * prepare before render block to html
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Edit_Tabs
	 */
	protected function _beforeToHtml() {
		$this->addTab ( 'Banner Information', array (
				'label' => Mage::helper ( 'mobiconnect' )->__ ( 'Banner Information' ),
				'title' => Mage::helper ( 'mobiconnect' )->__ ( 'Banner Information' ),
				'content' => $this->getLayout ()->createBlock ( 'mobiconnect/adminhtml_mobiconnect_edit_tab_bannerinfo' )->toHtml () 
		) );
		return parent::_beforeToHtml ();
	}
}
?>