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
 
class Ced_CsMarketplace_Helper_Tool_Image extends Mage_Core_Helper_Abstract
{
    /**
     * Current model
     *
     * @var Ced_CsMarketplace_Model_Vendor_Image
     */
    protected $_model;

    /**
     * Scheduled for resize image
     *
     * @var bool
     */
    protected $_scheduleResize = false;

    /**
     * Scheduled for rotate image
     *
     * @var bool
     */
    protected $_scheduleRotate = false;

    /**
     * Angle
     *
     * @var int
     */
    protected $_angle;

    /**
     * Watermark file name
     *
     * @var string
     */
    protected $_watermark;

    /**
     * Watermark Position
     *
     * @var string
     */
    protected $_watermarkPosition;

    /**
     * Watermark Size
     *
     * @var string
     */
    protected $_watermarkSize;

    /**
     * Watermark Image opacity
     *
     * @var int
     */
    protected $_watermarkImageOpacity;

    /**
     * Current Product
     *
     * @var Ced_CsMarketplace_Model_Vendor
     */
    protected $_vendor;

    /**
     * Image File
     *
     * @var string
     */
    protected $_imageFile;

    /**
     * Image Placeholder
     *
     * @var string
     */
    protected $_placeholder;

    /**
     * Reset all previous data
     *
     * @return Mage_Catalog_Helper_Image
     */
    protected function _reset()
    {
        $this->_model = null;
        $this->_scheduleResize = false;
        $this->_scheduleRotate = false;
        $this->_angle = null;
        $this->_watermark = null;
        $this->_watermarkPosition = null;
        $this->_watermarkSize = null;
        $this->_watermarkImageOpacity = null;
        $this->_vendor = null;
        $this->_imageFile = null;
        return $this;
    }

    /**
     * Initialize Helper to work with Image
     *
     * @param Ced_CsMarketplace_Model_Vendor $vendor
     * @param string $attributeName
     * @param mixed $imageFile
     * @return Mage_Catalog_Helper_Image
     */
    public function init($imageFile = null, $type= 'logo')
    {
    	
        $this->_reset();
        $this->_setModel(Mage::getModel('csmarketplace/tool_image'));
        $this->_getModel()->setDestinationSubdir($type);
        $this->setWatermark(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image")
        );
        $this->setWatermarkImageOpacity(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity")
        );
        $this->setWatermarkPosition(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position")
        );
        $this->setWatermarkSize(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size")
        );
		
        if ($imageFile) {
			$imageFile = str_replace('/',DS,$imageFile);
            $this->setImageFile($imageFile);
        }
		
        return $this;
    }

    /**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be calculated.
     *
     * @see Ced_CsMarketplace_Model_Vendor_Image
     * @param int $width
     * @param int $height
     * @return Mage_Catalog_Helper_Image
     */
    public function resize($width, $height = null)
    {
        $this->_getModel()->setWidth($width)->setHeight($height);
        $this->_scheduleResize = true;
        return $this;
    }

    /**
     * Set image quality, values in percentage from 0 to 100
     *
     * @param int $quality
     * @return Mage_Catalog_Helper_Image
     */
    public function setQuality($quality)
    {
        $this->_getModel()->setQuality($quality);
        return $this;
    }

    /**
     * Guarantee, that image picture width/height will not be distorted.
     * Applicable before calling resize()
     * It is true by default.
     *
     * @see Ced_CsMarketplace_Model_Vendor_Image
     * @param bool $flag
     * @return Mage_Catalog_Helper_Image
     */
    public function keepAspectRatio($flag)
    {
        $this->_getModel()->setKeepAspectRatio($flag);
        return $this;
    }

    /**
     * Guarantee, that image will have dimensions, set in $width/$height
     * Applicable before calling resize()
     * Not applicable, if keepAspectRatio(false)
     *
     * $position - TODO, not used for now - picture position inside the frame.
     *
     * @see Ced_CsMarketplace_Model_Vendor_Image
     * @param bool $flag
     * @param array $position
     * @return Mage_Catalog_Helper_Image
     */
    public function keepFrame($flag, $position = array('center', 'middle'))
    {
        $this->_getModel()->setKeepFrame($flag);
        return $this;
    }

    /**
     * Guarantee, that image will not lose transparency if any.
     * Applicable before calling resize()
     * It is true by default.
     *
     * $alphaOpacity - TODO, not used for now
     *
     * @see Ced_CsMarketplace_Model_Vendor_Image
     * @param bool $flag
     * @param int $alphaOpacity
     * @return Mage_Catalog_Helper_Image
     */
    public function keepTransparency($flag, $alphaOpacity = null)
    {
        $this->_getModel()->setKeepTransparency($flag);
        return $this;
    }

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default
     *
     * @param bool $flag
     * @return Mage_Catalog_Helper_Image
     */
    public function constrainOnly($flag)
    {
        $this->_getModel()->setConstrainOnly($flag);
        return $this;
    }

    /**
     * Set color to fill image frame with.
     * Applicable before calling resize()
     * The keepTransparency(true) overrides this (if image has transparent color)
     * It is white by default.
     *
     * @see Ced_CsMarketplace_Model_Vendor_Image
     * @param array $colorRGB
     * @return Mage_Catalog_Helper_Image
     */
    public function backgroundColor($colorRGB)
    {
        // assume that 3 params were given instead of array
        if (!is_array($colorRGB)) {
            $colorRGB = func_get_args();
        }
        $this->_getModel()->setBackgroundColor($colorRGB);
        return $this;
    }

    /**
     * Rotate image into specified angle
     *
     * @param int $angle
     * @return Mage_Catalog_Helper_Image
     */
    public function rotate($angle)
    {
        $this->setAngle($angle);
        $this->_getModel()->setAngle($angle);
        $this->_scheduleRotate = true;
        return $this;
    }

    /**
     * Add watermark to image
     * size param in format 100x200
     *
     * @param string $fileName
     * @param string $position
     * @param string $size
     * @param int $imageOpacity
     * @return Mage_Catalog_Helper_Image
     */
    public function watermark($fileName, $position, $size=null, $imageOpacity=null)
    {
        $this->setWatermark($fileName)
            ->setWatermarkPosition($position)
            ->setWatermarkSize($size)
            ->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Set placeholder
     *
     * @param string $fileName
     * @return void
     */
    public function placeholder($fileName)
    {
        $this->_placeholder = $fileName;
    }

    /**
     * Get Placeholder
     *
     * @return string
     */
    public function getPlaceholder()
    {
        if (!$this->_placeholder) {
            $attr = $this->_getModel()->getDestinationSubdir();        
            if($attr=="logo" && Mage::getStoreConfig('ced_vshops/general/vshoppage_vendor_placeholder',Mage::app()->getStore()->getId()))
            	$this->_placeholder = 'ced/csmarketplace/'.Mage::getStoreConfig('ced_vshops/general/vshoppage_vendor_placeholder',Mage::app()->getStore()->getId());
            else if($attr=="banner" && Mage::getStoreConfig('ced_vshops/general/vshoppage_banner_placeholder',Mage::app()->getStore()->getId()))
            	$this->_placeholder = 'ced/csmarketplace/'.Mage::getStoreConfig('ced_vshops/general/vshoppage_banner_placeholder',Mage::app()->getStore()->getId());
            else 
            	$this->_placeholder = 'images/ced/csmarketplace/vendor/placeholder/'.$attr.'.jpg';
        }
        return $this->_placeholder;
    }

    /**
     * Return Image URL
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $model = $this->_getModel();
			//return $this->getImageFile();
            if ($this->getImageFile()) {
                $model->setBaseFile($this->getImageFile());
            } else {
                $model->setBaseFile('no_selection');
            }
			//$model->clearCache();
            if ($model->isCached()) {
                return $model->getUrl();
            } else {
                if ($this->_scheduleRotate) {
                    $model->rotate($this->getAngle());
                }

                if ($this->_scheduleResize) {
                    $model->resize();
                }

                if ($this->getWatermark()) {
                    $model->setWatermark($this->getWatermark());
                }

                $url = $model->saveFile()->getUrl();
            }
        } catch (Exception $e) {
			if(Mage::getStoreConfig('ced_vshops/general/vshoppage_vendor_placeholder',Mage::app()->getStore()->getId())
			||Mage::getStoreConfig('ced_vshops/general/vshoppage_banner_placeholder',Mage::app()->getStore()->getId())		
			){
				$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."/".($this->getPlaceholder());
			}else{
				$url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
			}
			//$url = $e->getMessage();
        }
        return $url;
    }

    /**
     * Set current Image model
     *
     * @param Ced_CsMarketplace_Model_Vendor_Image $model
     * @return Mage_Catalog_Helper_Image
     */
    protected function _setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * Get current Image model
     *
     * @return Ced_CsMarketplace_Model_Vendor_Image
     */
    protected function _getModel()
    {
        return $this->_model;
    }

    /**
     * Set Rotation Angle
     *
     * @param int $angle
     * @return Mage_Catalog_Helper_Image
     */
    protected function setAngle($angle)
    {
        $this->_angle = $angle;
        return $this;
    }

    /**
     * Get Rotation Angle
     *
     * @return int
     */
    protected function getAngle()
    {
        return $this->_angle;
    }

    /**
     * Set watermark file name
     *
     * @param string $watermark
     * @return Mage_Catalog_Helper_Image
     */
    protected function setWatermark($watermark)
    {
        $this->_watermark = $watermark;
        $this->_getModel()->setWatermarkFile($watermark);
        return $this;
    }

    /**
     * Get watermark file name
     *
     * @return string
     */
    protected function getWatermark()
    {
        return $this->_watermark;
    }

    /**
     * Set watermark position
     *
     * @param string $position
     * @return Mage_Catalog_Helper_Image
     */
    protected function setWatermarkPosition($position)
    {
        $this->_watermarkPosition = $position;
        $this->_getModel()->setWatermarkPosition($position);
        return $this;
    }

    /**
     * Get watermark position
     *
     * @return string
     */
    protected function getWatermarkPosition()
    {
        return $this->_watermarkPosition;
    }

    /**
     * Set watermark size
     * param size in format 100x200
     *
     * @param string $size
     * @return Mage_Catalog_Helper_Image
     */
    public function setWatermarkSize($size)
    {
        $this->_watermarkSize = $size;
        $this->_getModel()->setWatermarkSize($this->parseSize($size));
        return $this;
    }

    /**
     * Get watermark size
     *
     * @return string
     */
    protected function getWatermarkSize()
    {
        return $this->_watermarkSize;
    }

    /**
     * Set watermark image opacity
     *
     * @param int $imageOpacity
     * @return Mage_Catalog_Helper_Image
     */
    public function setWatermarkImageOpacity($imageOpacity)
    {
        $this->_watermarkImageOpacity = $imageOpacity;
        $this->_getModel()->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Get watermark image opacity
     *
     * @return int
     */
    protected function getWatermarkImageOpacity()
    {
        if ($this->_watermarkImageOpacity) {
            return $this->_watermarkImageOpacity;
        }

        return $this->_getModel()->getWatermarkImageOpacity();
    }

    /**
     * Set current Product
     *
     * @param Ced_CsMarketplace_Model_Vendor $vendor
     * @return Mage_Catalog_Helper_Image
     */
    protected function setVendor($vendor)
    {
        $this->_vendor = $vendor;
        return $this;
    }

    /**
     * Get current Product
     *
     * @return Ced_CsMarketplace_Model_Vendor
     */
    protected function getVendor()
    {
        return $this->_vendor;
    }

    /**
     * Set Image file
     *
     * @param string $file
     * @return Mage_Catalog_Helper_Image
     */
    protected function setImageFile($file)
    {
        $this->_imageFile = $file;
        return $this;
    }

    /**
     * Get Image file
     *
     * @return string
     */
    protected function getImageFile()
    {
        return $this->_imageFile;
    }

    /**
     * Retrieve size from string
     *
     * @param string $string
     * @return array|bool
     */
    protected function parseSize($string)
    {
        $size = explode('x', strtolower($string));
        if (sizeof($size) == 2) {
            return array(
                'width' => ($size[0] > 0) ? $size[0] : null,
                'heigth' => ($size[1] > 0) ? $size[1] : null,
            );
        }
        return false;
    }

    /**
     * Retrieve original image width
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        return $this->_getModel()->getImageProcessor()->getOriginalWidth();
    }

    /**
     * Retrieve original image height
     *
     * @deprecated
     * @return int|null
     */
    public function getOriginalHeigh()
    {
        return $this->getOriginalHeight();
    }

    /**
     * Retrieve original image height
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        return $this->_getModel()->getImageProcessor()->getOriginalHeight();
    }

    /**
     * Retrieve Original image size as array
     * 0 - width, 1 - height
     *
     * @return array
     */
    public function getOriginalSizeArray()
    {
        return array(
            $this->getOriginalWidth(),
            $this->getOriginalHeight()
        );
    }

    /**
     * Check - is this file an image
     *
     * @param string $filePath
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function validateUploadFile($filePath) {
        if (!getimagesize($filePath)) {
            Mage::throwException($this->__('Disallowed file type.'));
        }

        $_processor = new Varien_Image($filePath);
        return $_processor->getMimeType() !== null;
    }

}
