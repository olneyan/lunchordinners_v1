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
class Ced_MobiconnectAdvCart_Block_Adminhtml_Category_Edit_Tab_Assign extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('category_banner');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    public function getCategoryBanner()
    {
        return Mage::getModel('mobiconnectadvcart/categorybanner')->load($this->getRequest()->getParam('id'));
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category_banner') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    public function getlayoutType(){
        $options=Mage::getModel('mobiconnect/banner')->getLayoutType();
        /*$options=array(
            'simple'=>'simple',
            'core'=>'core',
            );*/
        return $options;
    }
    

    protected function _prepareCollection()
    {   
        if ($this->getCategoryBanner()->getId()) {
            $this->setDefaultFilter(array('in_category_banner'=>1));
        }       
        $collection = Mage::getModel('mobiconnect/banner')
							->getCollection();
        $this->setCollection($collection);
        /*if ($this->getGroup()->getContent()!='') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
       // $this->getCollection()->addFieldToFilter('id', array('in'=>$productIds));
        }
        $this->setCollection($collection);*/
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        if (!$this->getCategoryBanner()->getProductLinks()) {
            $this->addColumn('in_category_banner', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_category_banner',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'id'
            ));
        }        
        $this->addColumn ( 'title', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Title' ),
                'align' => 'center',
                'index' => 'title',
                'type' => 'banner',
                'escape' => true,
                'sortable' => false,
                'width' => '150px' 
        ) );
        
        $this->addColumn ( 'image', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Banner Image' ),
                'width' => '150px',
                'index' => 'image',
                'renderer' => 'mobiconnect/adminhtml_mobiconnect_edit_tab_renderer_image' 
        ) );
        
        $this->addColumn ( 'description', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Description' ),
                'width' => '150px',
                'index' => 'description' 
        ) );
        $this->addColumn ( 'home_layout', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Layout Type' ),
                'align' => 'left',
                'width' => '80px',
                'index' => 'home_layout',
                'type' => 'options',
                'options' => $this->getlayoutType(),
        ) );
        $this->addColumn ( 'status', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Status' ),
                'align' => 'left',
                'width' => '80px',
                'index' => 'status',
                'type' => 'options',
                'options' => array (
                        1 => 'Enabled',
                        2 => 'Disabled' 
                ) 
        ) );
        $this->addColumn ( 'show_in', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Link to' ),
                'width' => '150px',
                'index' => 'show_in',
                'renderer' => 'mobiconnect/adminhtml_mobiconnect_edit_tab_renderer_website' 
        ) );
        return parent::_prepareColumns ();
    }

    public function getGridUrl()
    {
    return $this->getUrl('*/*/imageGrid', array('_current'=>true));
    }
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_deals');
        if (is_null($products)) {
            $products = $this->getCategoryBanner()->getBannerImage();
            $products=explode(',',$products);
            return $products;
        } 
        return $products;
    }    
    
}
