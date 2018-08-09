<?php

/**
 * Delivery boy bank account information form block
 *
 * @category   Delivery
 * @package    Delivery_Boy
 * @author     <rajeeb.saraswati@gmail.com>
 */
class Delivery_Boy_Block_Adminhtml_Boy_Edit_Tab_BankAccount
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    
    protected function _construct() {
        parent::_construct();
        $this->setActive(FALSE);
    }
    
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('delivery_boy')->__('Bank Account');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('delivery_boy')->__('Bank Account');
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
     * @return Delivery_Boy_Block_Adminhtml_Boy_Edit_Tab_BankAccount
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
            array('legend' => Mage::helper('delivery_boy')->__('Bank Account'))
        );

        if ($boy->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
            ));
        }

        $this->_addElementTypes($fieldset);
        
        $fieldset->addField('bank_name', 'text', array(
            'name'  => 'bank_name',
            'label' => Mage::helper('delivery_boy')->__('Bank Name'),
            'title' => Mage::helper('delivery_boy')->__('Bank Name'),
            'class' => '',
            'required' => true,
        ));
        
        $fieldset->addField('bank_account_number', 'text', array(
            'name'  => 'bank_account_number',
            'label' => Mage::helper('delivery_boy')->__('Account Number'),
            'title' => Mage::helper('delivery_boy')->__('Account Number'),
            'class' => '',
            'required' => true,
        ));
        
        $fieldset->addField('ifsc_code', 'text', array(
            'name'  => 'ifsc_code',
            'label' => Mage::helper('delivery_boy')->__('IFSC Code'),
            'title' => Mage::helper('delivery_boy')->__('IFSC Code'),
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
