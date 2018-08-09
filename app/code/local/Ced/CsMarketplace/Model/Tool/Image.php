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

/**
 * CsMarketplace Image Tool
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Tool_Image extends Mage_Catalog_Model_Product_Image 
{
	/**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setBaseFile($file)
    {
        $this->_isBaseFilePlaceholder = false;

        if (($file) && (0 !== strpos($file, '/', 0))) {
            $file = '/' . $file;
        }
        $baseDir = Mage::getBaseDir('media');
		
        if ('/no_selection' == $file) {
            $file = null;
        }
		//return $file;
        if ($file) {
            if ((!$this->_fileExists($baseDir . $file)) || !$this->_checkMemory($baseDir . $file)) {
                $file = null;
            }
        }
        if (!$file) {
        	$attr = $this->getDestinationSubdir();
        	if($attr=="logo" && Mage::getStoreConfig('ced_vshops/general/vshoppage_vendor_placeholder',Mage::app()->getStore()->getId())){
        		$imgpath = Mage::getStoreConfig('ced_vshops/general/vshoppage_vendor_placeholder',Mage::app()->getStore()->getId());
        		$baseDir = Mage::getBaseDir('media');
        		$skinPlaceholder = "/ced/csmarketplace/".$imgpath;
        		$file = $skinPlaceholder;
        		$this->_isBaseFilePlaceholder = true;
        	}
        	else if($attr=="banner" && Mage::getStoreConfig('ced_vshops/general/vshoppage_banner_placeholder',Mage::app()->getStore()->getId())){
        		$imgpath = Mage::getStoreConfig('ced_vshops/general/vshoppage_banner_placeholder',Mage::app()->getStore()->getId());
        		$baseDir = Mage::getBaseDir('media');
        		$skinPlaceholder = "/ced/csmarketplace/".$imgpath;
        		$file = $skinPlaceholder;
        		$this->_isBaseFilePlaceholder = true;
        	}
        	else{
	            // check if placeholder defined in config
	            $isConfigPlaceholder = Mage::getStoreConfig("catalog/placeholder/{$this->getDestinationSubdir()}_placeholder");
	            $configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;
	            if ( 0 && $isConfigPlaceholder && $this->_fileExists($baseDir . $configPlaceholder)) {
	                $file = $configPlaceholder;
	            }
	            else {
	                // replace file with skin or default skin placeholder
	                $skinBaseDir     = Mage::getDesign()->getSkinBaseDir();
	                $skinPlaceholder = "/images/ced/csmarketplace/vendor/placeholder/{$this->getDestinationSubdir()}.jpg";
	                $file = $skinPlaceholder;
	                if (file_exists($skinBaseDir . $file)) {
	                    $baseDir = $skinBaseDir;
	                }
	                else {
	                    $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
	                    if (!file_exists($baseDir . $file)) {
	                        $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default', '_package' => 'base'));
	                    }
	                }
	            }
	            $this->_isBaseFilePlaceholder = true;
        	}
        }

        $baseFile = $baseDir . $file;
		
        if ((!$file) || (!file_exists($baseFile))) {
            throw new Exception(Mage::helper('catalog')->__('Image file was not found.'));
        }

        $this->_baseFile = $baseFile;

        // build new filename (most important params)
        $path = array(
            Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath(),
            'cache',
            Mage::app()->getStore()->getId(),
            $path[] = $this->getDestinationSubdir()
        );
        if((!empty($this->_width)) || (!empty($this->_height)))
            $path[] = "{$this->_width}x{$this->_height}";

        // add misk params as a hash
        $miscParams = array(
                ($this->_keepAspectRatio  ? '' : 'non') . 'proportional',
                ($this->_keepFrame        ? '' : 'no')  . 'frame',
                ($this->_keepTransparency ? '' : 'no')  . 'transparency',
                ($this->_constrainOnly ? 'do' : 'not')  . 'constrainonly',
                $this->_rgbToString($this->_backgroundColor),
                'angle' . $this->_angle,
                'quality' . $this->_quality
        );

        // if has watermark add watermark params to hash
        if ($this->getWatermarkFile()) {
            $miscParams[] = $this->getWatermarkFile();
            $miscParams[] = $this->getWatermarkImageOpacity();
            $miscParams[] = $this->getWatermarkPosition();
            $miscParams[] = $this->getWatermarkWidth();
            $miscParams[] = $this->getWatermarkHeigth();
        }

        $path[] = md5(implode('_', $miscParams));

        // append prepared filename
        $this->_newFile = implode('/', $path) . $file; // the $file contains heading slash

        return $this;
    }
}
