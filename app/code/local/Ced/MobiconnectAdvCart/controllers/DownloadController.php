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
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
require_once Mage::getModuleDir('controllers', 'Mage_Downloadable').DS.'DownloadController.php';
class  Ced_MobiconnectAdvCart_DownloadController extends Mage_Downloadable_DownloadController
{
    /**
     * Download sample action
     *
     */
    public function sampleAction()
    {
        $sampleId = $this->getRequest()->getParam('sample_id', 0);
        $sample = Mage::getModel('downloadable/sample')->load($sampleId);
        if ($sample->getId()) {
            $resource = '';
            $resourceType = '';
            if ($sample->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $sample->getSampleUrl();
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($sample->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('downloadable/file')->getFilePath(
                    Mage_Downloadable_Model_Sample::getBasePath(), $sample->getSampleFile()
                );
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                exit(0);
            } catch (Mage_Core_Exception $e) {
                 $this->getResponse()->setBody(json_encode(array('success'=>'false','message'=>Mage::helper('downloadable')->__('Sorry, there was an error getting requested content. Please contact the store owner.'))));
            }
        }
    }

    /**
     * Download link's sample action
     *
     */
    public function linkSampleAction()
    {
        $linkId = $this->getRequest()->getParam('link_id', 0);
        $link = Mage::getModel('downloadable/link')->load($linkId);
        if ($link->getId()) {
            $resource = '';
            $resourceType = '';
            if ($link->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $link->getSampleUrl();
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($link->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('downloadable/file')->getFilePath(
                    Mage_Downloadable_Model_Link::getBaseSamplePath(), $link->getSampleFile()
                );
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                exit(0);
            } catch (Mage_Core_Exception $e) {
                $this->getResponse()->setBody(json_encode(array('success'=>'false','message'=>Mage::helper('downloadable')->__('Sorry, there was an error getting requested content. Please contact the store owner.'))));
            }
        }

    }
    protected function _processDownload($resource, $resourceType)
    {
        $helper = Mage::helper('downloadable/download');
        /* @var $helper Mage_Downloadable_Helper_Download */

        $helper->setResource($resource, $resourceType);

        $fileName       = $helper->getFilename();
        $contentType    = $helper->getContentType();

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($fileSize = $helper->getFilesize()) {
            $this->getResponse()
                ->setHeader('Content-Length', $fileSize);
        }

        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
                $this->getResponse()
                ->setHeader('Set-Success', json_encode(array('success'=>'true','message'=>Mage::helper('downloadable')->__("Succes Download"))));
                
        }

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

        session_write_close();
        $helper->output();
    }
    /**
     * Download link action
     */
    public function linkAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
    
        $linkPurchasedItem = Mage::getModel('downloadable/link_purchased_item')->load($id, 'link_hash');
        if (! $linkPurchasedItem->getId() ) {
            $message='Requested link does not exist.';

        }
        if (!Mage::helper('downloadable')->getIsShareable($linkPurchasedItem)) {
          
             $customerId = $this->getRequest()->getParam('customer_id', 0);
            if (!$customerId) {
                
                $product = Mage::getModel('catalog/product')->load($linkPurchasedItem->getProductId());
                if ($product->getId()) {
                    
                    $notice = Mage::helper('downloadable')->__('Please log in to download your product or purchase <a href="%s">%s</a>.', $product->getProductUrl(), $product->getName());
                  
                } else {
                
                    $notice = Mage::helper('downloadable')->__('Please log in to download your product.');
                }
               // $this->_getCustomerSession()->addNotice($notice);
               // $this->_getCustomerSession()->authenticate($this);
            
                $message=$notice;            }
            $linkPurchased = Mage::getModel('downloadable/link_purchased')->load($linkPurchasedItem->getPurchasedId());
            if ($linkPurchased->getCustomerId() != $customerId) {
                $message='Requested link does not exist.';
            }
        }
        $downloadsLeft = $linkPurchasedItem->getNumberOfDownloadsBought()
            - $linkPurchasedItem->getNumberOfDownloadsUsed();

        $status = $linkPurchasedItem->getStatus();
       
         
        if ($status == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE
            && ($downloadsLeft || $linkPurchasedItem->getNumberOfDownloadsBought() == 0)
        ) {
            
            $resource = '';
            $resourceType = '';
            if ($linkPurchasedItem->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $linkPurchasedItem->getLinkUrl();
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($linkPurchasedItem->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('downloadable/file')->getFilePath(
                    Mage_Downloadable_Model_Link::getBasePath(), $linkPurchasedItem->getLinkFile()
                );
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                
                $this->_processDownload($resource, $resourceType);

                $linkPurchasedItem->setNumberOfDownloadsUsed($linkPurchasedItem->getNumberOfDownloadsUsed() + 1);

                if ($linkPurchasedItem->getNumberOfDownloadsBought() != 0 && !($downloadsLeft - 1)) {
                    $linkPurchasedItem->setStatus(Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED);
                }
                $linkPurchasedItem->save();
                
                exit(0);
            }
            catch (Exception $e) {
                $this->getResponse()
                ->setHeader('Set-Success', json_encode(array('success'=>'false','message'=>Mage::helper('downloadable')->__('Sorry, there was an error getting requested content. Please contact the store owner.'))));
                
            }
        } elseif ($status == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
           $message='The link has expired.';
           $this->getResponse()
                ->setHeader('Set-Success', json_encode(array('success'=>'false','message'=>$message)));
           
        } elseif ($status == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING
            || $status == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
        ) {
             $message=Mage::helper('downloadable')->__('The link is not available.');
             $this->getResponse()
                ->setHeader('Set-Success', json_encode(array('success'=>'false','message'=>$message)));

            
        } else {
             $message=Mage::helper('downloadable')->__('Sorry, there was an error getting requested content. Please contact the store owner.');
             $this->getResponse()
                ->setHeader('Set-Success', json_encode(array('success'=>'false','message'=>$message)));
            
        }

        
    }

}
