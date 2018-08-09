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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMarketplace_Helper_Vproducts_Link extends Mage_Core_Helper_Abstract {
	
	/**
	 * Upload Downloadable product data
	 * 
	 * @params array $data,String $type
	 * @return array $uploaded_files_array
	 */
	public function uploadDownloadableFiles($type, $data) {
		$mediDir = Mage::getBaseDir ( 'media' );
		$uploadDir = $mediDir . DS . 'downloadable' . DS . 'files' . DS . $type . DS;
		if ($type == "samples") {
			$formats = Mage::getStoreConfig ( 'ced_vproducts/downloadable_config/sample_formats' );
		} else if ($type == "link_samples") {
			$formats = Mage::getStoreConfig ( 'ced_vproducts/downloadable_config/sample_formats' );
		} else if ($type == "links") {
			$formats = Mage::getStoreConfig ( 'ced_vproducts/downloadable_config/link_formats' );
		}
		
		$tempArr = explode ( ',', $formats );
		
		$formats_array = array ();
		foreach ( $tempArr as $value ) {
			if (strlen ( $value )) {
				$formats_array [] = trim ( $value );
			}
		}
		$uploaded_files_array = array ();
		if (isset ( $_FILES [$type] ) && count ( $_FILES [$type] ) > 0) {
			foreach ( $_FILES [$type] ["tmp_name"] as $key => $value ) {
				if($type=="link_samples"){
					if(isset($data [$key] ['sample']['type']) && $data [$key] ['sample']['type'] == "url")
						continue;
				}					
				else{ 
					if (isset($data [$key] ['type']) && $data [$key] ['type'] == "url")
						continue;
				}
				if (isset ( $_FILES [$type] ['name'] [$key] ) && file_exists ( $_FILES [$type] ['tmp_name'] [$key] )) {
					$uploader = new Varien_File_Uploader ( "{$type}[{$key}]" );
					$uploader->setAllowRenameFiles ( false );
					$uploader->setFilesDispersion ( false );
					$uploader->setAllowedExtensions ( $formats_array );
					$file = md5 ( $_FILES [$type] ['tmp_name'] [$key] ) . $_FILES [$type] ['name'] [$key];
					try {
						if ($result = $uploader->save ( $uploadDir, $file )) {
							$uploaded_files_array [$key] = $result ['file'];
						}
					} catch ( Exception $e ) {
						echo $e->getMessage ();
					}
				}
			}
		}
		return $uploaded_files_array;
	}
	
	/**
	 * Helper for saving downlodable product samples data
	 * 
	 * @params array $data,array $samples
	 */
	public function processSamplesData($samplesdata, $samples,$productid) {
		
		if(is_array($samplesdata) && count($samplesdata)>0){
			// setting sample data
			foreach ( $samplesdata as $key => $val ) {
				$linkModel = null;
				if ($samplesdata [$key] ['sample_id'] != '') {
					$linkModel = Mage::getModel ( 'downloadable/sample' )->load ( $samplesdata [$key] ['sample_id'] );
				} else if ($samplesdata [$key] ['sample_id'] == '') {
					$linkModel = Mage::getModel ( 'downloadable/sample' );
					$linkModel->setProductId ( $productid );
					$linkModel->setStoreId ( 0 );
					$linkModel->setWebsiteId (0);
				}
				$linkModel->setSortOrder (  isset($samplesdata [$key]['sort_order'])?$samplesdata [$key]['sort_order']:0 );
				$linkModel->setTitle(isset($samplesdata[$key]['title'])?$samplesdata[$key] ['title']:'');
						
				// /setting sample file
				if (isset ( $samplesdata [$key] ['type'] ) && ($samplesdata [$key] ['type'] == 'file')) {
					if (isset($_FILES ['samples'] ['name'] [$key]) && $_FILES ['samples'] ['name'] [$key] != '') {
						if(isset($samples[$key]))
							$linkModel->setSampleFile ( "/" . $samples [$key] );
						$linkModel->setSampleType ( "file" );
					}
				} else if (isset ( $samplesdata [$key] ['type'] ) && ($samplesdata [$key] ['type'] == 'url')) {
					$linkModel->setSampleType ( "url" );
					if(isset($samplesdata [$key] ['sample_url']))
						$linkModel->setSampleUrl ($samplesdata [$key] ['sample_url']);
				}
				$linkModel->save ();
			}
		}
	}
	
	/**
	 * Helper for saving downlodable product Links data
	 * 
	 * @param
	 *        	s array $data,array $samples
	 */
	public function processLinksData($linksdata, $links, $link_samples,$productid) {
		// //setting link data
		$helper=Mage::helper('csmarketplace');
		if(Mage::registry('ced_csmarketplace_current_store')) {
			$currentStoreId = Mage::registry('ced_csmarketplace_current_store');
			Mage::app()->setCurrentStore($currentStoreId);
		}
			
		if(is_array($linksdata) && count($linksdata)>0){
			foreach ( $linksdata as $key => $val ) {
				$linkModel = null;
				if (isset($linksdata [$key] ['link_id']) && $linksdata [$key] ['link_id'] != '') {
					$linkModel = Mage::getModel ( 'downloadable/link' )->load ( $linksdata [$key] ['link_id'] );
				} else if (isset($linksdata [$key] ['link_id']) && $linksdata [$key] ['link_id'] == '') {
					$linkModel = Mage::getModel ( 'downloadable/link' );
					$linkModel->setProductId ( $productid );
					$linkModel->setStoreId ( 0 );
					$linkModel->setWebsiteId (0);
					$linkModel->setProductWebsiteIds(Mage::getModel('catalog/product')->load($productid)->getWebsiteIds());
				}
				$linkModel->setPrice ( isset($linksdata[$key]['price'])?$linksdata[$key]['price']:0);
				$linkModel->setSortOrder ( isset($linksdata[$key]['sort_order'])?$linksdata[$key]['sort_order']:0 );
				$linkModel->setTitle ( isset($linksdata[$key]['title'])?$linksdata[$key] ['title']:'');
				if (isset ( $linksdata [$key] ['is_unlimited'] )) {
					if ($linksdata [$key] ['is_unlimited'] == 1)
						$linkModel->setNumberOfDownloads ( 0 );
				} else
					$linkModel->setNumberOfDownloads ( isset($linksdata [$key] ['number_of_downloads'])?$linksdata [$key] ['number_of_downloads']:0 );
					
				// setting link file
				if (isset ( $linksdata [$key] ['type'] ) && $linksdata [$key] ['type'] == 'file' && isset($links [$key])) {
					if (isset($_FILES ['links'] ['name'] [$key]) && $_FILES ['links'] ['name'] [$key] != '') {
						$linkModel->setLinkFile ( "/" . $links [$key] );
						$linkModel->setLinkType ( "file" );
					}
				} else if (isset ( $linksdata [$key] ['type'] ) && ($linksdata [$key] ['type'] == 'url') && isset($linksdata [$key] ['link_url'])) {
					$linkModel->setLinkType ( "url" );
					$linkModel->setLinkUrl ($linksdata [$key] ['link_url']);
				}
				
				// setting link sample file
				if (isset ( $linksdata [$key]['sample'] ['type'] ) && ($linksdata [$key]['sample']['type'] == 'file') && isset($link_samples [$key])) {
					if ($_FILES ['link_samples'] ['name'] [$key] != '') {
						$linkModel->setSampleFile ( "/" . $link_samples [$key] );
						$linkModel->setSampleType ( "file" );
					}
				} else if (isset ( $linksdata [$key]['sample'] ['type'] ) && ($linksdata [$key] ['sample'] ['type'] == 'url') && isset($linksdata [$key] ['sample'] ['sample_url'])) {
					$linkModel->setSampleType ( "url" );
					$linkModel->setSampleUrl ($linksdata [$key] ['sample'] ['sample_url']);
				}
				$linkModel->save ();
			}
		}
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	}
}