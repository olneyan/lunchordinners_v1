<?php

/**
 * Delivery Boy edit block
 *
 * @category   Delivery
 * @package    Delivery_Boy
 * @author     <rajeeb.saraswati@gmail.com>
 */
class Delivery_Boy_Block_Adminhtml_Boy_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'delivery_boy';
        $this->_controller = 'adminhtml_boy';
    }


    public function getDeliveryBoy()
    {
        return Mage::registry('current_delivery_boy');
    }
    
    
    protected function _prepareLayout()
    {
        if ($this->getDeliveryBoy()) {
            $this->_addButton(
                'save_and_continue', 
                array(
                    'label'     => Mage::helper('delivery_boy')->__('Save and Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                    'class'     => 'save'
                ), 
                100
            );
        } else {
            $this->removeButton('save');
        }

        return parent::_prepareLayout();
    }


    public function getHeaderText()
    {
        if ($this->getDeliveryBoy()->getId()) {
            return $this->escapeHtml($this->getDeliveryBoy()->getFirstname() . " " . $this->getDeliveryBoy()->getLastname());
        } else {
            return Mage::helper('delivery_boy')->__('New Delivery Boy');
        }
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
    
    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'  => true,
            'back'      => 'edit',
            'tab'       => '{{tab_id}}'
        ));
    }
}
