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
  * @package   Ced_Mobiconnectdeals
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnectdeals_Block_Adminhtml_Deals_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct($arguments = array()) {
        parent::__construct ( $arguments );
        if ($this->getRequest ()->getParam ( 'current_grid_id' )) {
            $this->setId ( $this->getRequest ()->getParam ( 'current_grid_id' ) );
        } else {
            $this->setId ( 'skuChooserGrid_' . $this->getId () );
        }
        $form = $this->getJsFormObject ();
        $gridId = $this->getId ();
        $this->setCheckboxCheckCallback ( "constructData($gridId)" );
        $this->setDefaultSort ( 'sku' );
        $this->setUseAjax ( true );
        if ($this->getRequest ()->getParam ( 'collapse' )) {
            
            $this->setIsCollapsed ( true );
        }
        $this->setTemplate ( 'mobiconnectdeals/grid.phtml' );
    }
    /**
     * Retrieve quote store object
     * 
     * @return Mage_Core_Model_Store
     */
    public function getStore() {
        return Mage::app ()->getStore ();
    }
    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in product flag
        if ($column->getId () == 'in_products') {
            $selected = $this->_getSelectedProducts ();
            if (empty ( $selected )) {
                $selected = '';
            }
            if ($column->getFilter ()->getValue ()) {
                $this->getCollection ()->addFieldToFilter ( 'sku', array (
                        'in' => $selected 
                ) );
            } else {
                $this->getCollection ()->addFieldToFilter ( 'sku', array (
                        'nin' => $selected 
                ) );
            }
        } else {
            parent::_addColumnFilterToCollection ( $column );
        }
        return $this;
    }
    
    /**
     * Prepare Catalog Product Collection for attribute SKU in Promo Conditions SKU chooser
     *
     * @return Mage_Adminhtml_Block_Promo_Widget_Chooser_Sku
     */
    protected function _prepareCollection() {
        $collection = Mage::getResourceModel ( 'catalog/product_collection' )->setStoreId ( 0 )->addAttributeToSelect ( 'name', 'type_id', 'attribute_set_id' )->addFieldToFilter ( 'visibility', array (
                'neq' => '1' 
        ) )->addFieldToFilter ( 'status', '1' );
        // Zend_debug::dump($collection->getData());die();
        $this->setCollection ( $collection );
        
        return parent::_prepareCollection ();
    }
    
    /**
     * Define Cooser Grid Columns and filters
     *
     * @return Adminhtml_Block_Promo_Widget_Chooser_Sku
     */
    protected function _prepareColumns() {
        $this->addColumn ( 'in_products', array (
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts (),
                'align' => 'center',
                'index' => 'sku',
                'use_index' => true,
                'width' => '50px',
                'renderer' => 'mobiconnect/adminhtml_mobiconnect_edit_tab_renderer_sku' 
        ) );
        
        $this->addColumn ( 'id', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'ID' ),
                'sortable' => true,
                'width' => '60px',
                'index' => 'id' 
        ) );
        
        $this->addColumn ( 'chooser_name', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Product Name' ),
                'name' => 'chooser_name',
                'index' => 'name',
                'width' => '400px' 
        ) );
        
        $this->addColumn ( 'type', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Type' ),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => Mage::getSingleton ( 'catalog/product_type' )->getOptionArray () 
        ) );
        
        $this->addColumn ( 'chooser_sku', array (
                'header' => Mage::helper ( 'mobiconnect' )->__ ( 'SKU' ),
                'name' => 'chooser_sku',
                'width' => '80px',
                'index' => 'sku' 
        ) );
        
        return parent::_prepareColumns ();
    }
    public function getGridUrl() {
        return $this->getUrl ( '*/*/chooseProducts', array (
                '_current' => true,
                'current_grid_id' => $this->getId (),
                'collapse' => null 
        ) );
    }
    protected function _getSelectedProducts() {
        $products = $this->getRequest ()->getPost ( 'selected', array () );
        return $products;
    }
}

