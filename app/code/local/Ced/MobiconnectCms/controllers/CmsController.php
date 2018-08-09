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
class Ced_MobiconnectCms_CmsController extends Ced_MobiconnectCms_Controller_Action {

    public function getCmsPagesAction(){ 
        $id = $this->getRequest ()->getParam ( 'store_id' );
        $cmspages = Mage::getModel ( 'mobiconnectcms/cmspages' )->getCmsPages ($id);
        $this->printHtmlJsonData ( $cmspages );

    }
    public function cmsContentAction() {
        $id=$this->getRequest ()->getParam ( 'id' );
        Mage::register('page_id', $id);
        $this->loadLayout();
        $this->renderLayout();

       /* $id = $this->getRequest ()->getParam ( 'id' );
        $page = Mage::getModel ( 'mobiconnect/cmsblock' )->load($id);
        $helper = Mage::helper ( 'cms' );
        $processor = $helper->getPageTemplateProcessor ();
        $html = $processor->filter ( $page->getCmsContent () );
        echo $html;*/
    }
}


