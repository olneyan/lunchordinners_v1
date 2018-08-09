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
class Ced_Mobiconnectdeals_Block_Adminhtml_Group_Edit_Tab_Assign extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('group_deals');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    public function getGroup()
    {
        return Mage::getModel('mobiconnectdeals/group')->load($this->getRequest()->getParam('group_id'));
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_group_deals') {
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


    

    protected function _prepareCollection()
    {   
        if ($this->getGroup()->getId()) {
            $this->setDefaultFilter(array('in_group_deals'=>1));
        }       
        $collection = Mage::getModel('mobiconnectdeals/deals')
							->getCollection();
        $this->setCollection($collection);
        if ($this->getGroup()->getContent()!='') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
       // $this->getCollection()->addFieldToFilter('id', array('in'=>$productIds));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        if (!$this->getGroup()->getProductLinks()) {
            $this->addColumn('in_group_deals', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_group_deals',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'id'
            ));
        }
	    $this->addColumn('deal_title', array(
            'header'    =>Mage::helper('mobiconnectdeals')->__('Title'),
            'index'     =>'deal_title',
            'align'     => 'left',
        ));
        $this->addColumn('offer_text', array(
            'header'    =>Mage::helper('mobiconnectdeals')->__('Offer Text'),
            'index'     =>'offer_text',
            'align'     => 'left',
        ));
      /*  $this->addColumn('start_date', array(
            'header'    =>Mage::helper('mobiconnectdeals')->__('Start Date'),
            'index'     =>'start_date',
            'align'     => 'left',
        ));
        $this->addColumn('end_date', array(
            'header'    =>Mage::helper('mobiconnectdeals')->__('End Date'),
            'index'     =>'end_date',
            'align'     => 'left',
        ));*/
         $this->addColumn('status', array(
          'header'    => Mage::helper('mobiconnectdeals')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
       

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
    return $this->getUrl('*/*/dealsgrid', array('_current'=>true));
    }
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_deals');
        if (is_null($products)) {
            $products = $this->getGroup()->getContent();
            $products=explode(',',$products);
            return $products;
        }
        return $products;
    }    
    
}
