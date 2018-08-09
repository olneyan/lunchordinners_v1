<?php

/**
 * Delivery boy general information form block
 *
 * @category   Delivery
 * @package    Delivery_Boy
 * @author     <rajeeb.saraswati@gmail.com>
 */
class Delivery_Boy_Block_Adminhtml_Boy_Edit_Tab_General 
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    
    protected function _construct() {
        parent::_construct();
        $this->setActive(true);
    }

    
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('delivery_boy')->__('General Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('delivery_boy')->__('General Properties');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
//        return $this->getWidgetInstance()->isCompleteToCreate();
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Getter
     *
     * @return Delivery_Boy_Model_Boy
     */
    public function getDeliveryBoy()
    {
        return Mage::registry('current_delivery_boy');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Delivery_Boy_Block_Adminhtml_Boy_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $boy = $this->getDeliveryBoy();
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('delivery_boy')->__('General Properties'))
        );

        if ($boy->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
            ));
        }

        $this->_addElementTypes($fieldset);
        
        $fieldset->addField('firstname', 'text', array(
            'name'  => 'firstname',
            'label' => Mage::helper('delivery_boy')->__('Firstname'),
            'title' => Mage::helper('delivery_boy')->__('Firstname'),
            'class' => '',
            'required' => true,
        ));
        
        $fieldset->addField('middlename', 'text', array(
            'name'  => 'middlename',
            'label' => Mage::helper('delivery_boy')->__('Middlename'),
            'title' => Mage::helper('delivery_boy')->__('Middlename'),
            'class' => '',
            'required' => true,
        ));
        
        $fieldset->addField('lastname', 'text', array(
            'name'  => 'lastname',
            'label' => Mage::helper('delivery_boy')->__('Lastname'),
            'title' => Mage::helper('delivery_boy')->__('Lastname'),
            'class' => '',
            'required' => true,
        ));
        
        $fieldset->addField('email', 'text', array(
            'name'  => 'email',
            'label' => Mage::helper('delivery_boy')->__('Email'),
            'title' => Mage::helper('delivery_boy')->__('Email'),
            'class' => '',
            'required' => true,
        ));
        
        $fieldset->addField('mobile', 'text', array(
            'name'  => 'mobile',
            'label' => Mage::helper('delivery_boy')->__('Mobile'),
            'title' => Mage::helper('delivery_boy')->__('Mobile'),
            'class' => '',
            'required' => true,
        ));
        

        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Initialize form fileds values
     *
     * @return Delivery_Boy_Block_Adminhtml_Boy_Edit_Tab_General
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getDeliveryBoy()->getData());
        return parent::_initFormValues();
    }

}
