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
class Ced_Mobiconnectdeals_Block_Adminhtml_Deals_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('assignGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection =  Mage::getModel("mobiconnectdeals/deals")->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>Mage::helper('mobiconnectdeals')->__('Deal ID#'),
            'index'     =>'id',
            'align'     => 'right',
            'width'    => '50px'
        ));
		
	    $this->addColumn('deal_type', array(
            'header'    =>Mage::helper('mobiconnectdeals')->__('Deal Type'),
            'index'     =>'deal_type',
            'align'     => 'left',
            'type'      => 'options',
            'options'   => array(
                  1 => 'Product',
                  2 => 'Category',
                  3 => 'Static',
            ),
        ));
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
    return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('deal_id' => $row->getId()));
    }

}
