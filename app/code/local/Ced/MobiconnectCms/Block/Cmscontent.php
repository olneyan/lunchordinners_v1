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
class Ced_MobiconnectCms_Block_Cmscontent extends Mage_Core_Block_Template {
	
	public function getCmsHtml() {
		$id=Mage::registry('page_id');
		$page = Mage::getModel ( 'mobiconnectcms/cmsblock' )->load($id);
        $helper = Mage::helper ( 'cms' );
        $processor = $helper->getPageTemplateProcessor ();
        $html = $processor->filter ( $page->getCmsContent () );
        return $html;
	}
}
?>