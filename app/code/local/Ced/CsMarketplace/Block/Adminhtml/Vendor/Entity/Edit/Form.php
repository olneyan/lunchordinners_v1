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
class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
  	 $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('vendor_id' => $this->getRequest()->getParam('vendor_id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }


   /**
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        $vendor =Mage::getModel('csmarketplace/vendor')->load($this->getRequest()->getParam('vendor_id'));
        
        if (is_object($this->getForm())) {
          $html="<script>new RegionUpdater('country_id', 'region', 'region_id',".Mage::helper('directory')->getRegionJson().", undefined, 'zip_code');
      	 Event.observe(window, 'load', function() {
				  	 document.getElementById('region_id').value='".$vendor ->getRegionId()."';
            var country_id='".$vendor ->getCountryId()."';
             var rurl ='".$this->getUrl('adminhtml/adminhtml_vendor/country')."';
              
          new Ajax.Request(rurl, {
                 parameters: {cid:country_id},
            method: 'get',
                  onComplete: function(stateform) {
                    if(stateform.responseText=='true'){
                document.getElementById('region').parentNode.parentNode.style.display='none';
                document.getElementById('region_id').parentNode.parentNode.style.display='inline-block';
              }else{
                document.getElementById('region_id').parentNode.parentNode.style.display='none'; 
                document.getElementById('region').parentNode.parentNode.style.display='inline-block'; 
              }
                  }
              });         
          
      
				}); 


        country_id.onchange = function()
        {
              var country_id= document.getElementById('country_id').value;
          var rurl ='".$this->getUrl('adminhtml/adminhtml_vendor/country')."';
              
          new Ajax.Request(rurl, {
                 parameters: {cid:country_id},
            method: 'get',
                  onComplete: function(stateform) {
                    if(stateform.responseText=='true'){
                document.getElementById('region').parentNode.parentNode.style.display='none';
                document.getElementById('region_id').parentNode.parentNode.style.display='inline-block';
              }else{
                document.getElementById('region_id').parentNode.parentNode.style.display='none'; 
                document.getElementById('region').parentNode.parentNode.style.display='inline-block'; 
              }
                  }
              });         
          
        };
      </script><style>
      .form-list tr{
      display:block;
    }
    </style><script>document.getElementById('member_id').readOnly = true;</script>"; 
            return $this->getForm()->getHtml().$html;
        }
        return '';
    }

}