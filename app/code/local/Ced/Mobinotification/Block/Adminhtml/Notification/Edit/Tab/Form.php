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
  * @package   Ced_Mobinotification
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobinotification_Block_Adminhtml_Notification_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset   = $form->addFieldset('notification_form', array('legend'    => Mage::helper('mobinotification')->__("Notification Information")));

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('mobinotification')->__('Title'),
            'title'     => Mage::helper('mobinotification')->__('Title'),
            'required'  => true,
        ));
        $fieldset->addField('message', 'textarea', array(
          'label'     => Mage::helper('mobinotification')->__('Message'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'message',
          'after_element_html' => '<small>Enter message for notification </small>',
        ));
        $fieldset->addField('type', 'select', array(
          'label'     => Mage::helper('mobinotification')->__('Deal Type'),
          'name'      => 'type',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobinotification')->__('Product'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobinotification')->__('Category'),
              ),
              array(
                  'value'     => 3,
                  'label'     => Mage::helper('mobinotification')->__('Static'),
              ),
          ),
        ));
         $fieldset->addField('linked_page', 'text', array(
          'label'     => Mage::helper('mobinotification')->__('Static Deal Link'),
          'class'     => 'required-entry',
          'name'      => 'linked_page',
      ));
      $productIds = implode(",", Mage::getResourceModel('catalog/product_collection')->getAllIds());
        $fieldset->addField('linked_product', 'text', array(
            'name' => 'linked_product',
            'label'     => Mage::helper('mobinotification')->__('Choose Product'),
            'title'     => Mage::helper('mobinotification')->__('Choose Product'),
            'note'  => Mage::helper('mobinotification')->__('Choose a product for deal'),
            'after_element_html' => '<a id="linked_product" href="javascript:void(0)" onclick="toggleMainProducts()"><img src="' . $this->getSkinUrl('images/rule_chooser_trigger.gif') . '" alt="" class="v-middle rule-chooser-trigger" title="Select Products"></a><input type="hidden" value="'.$productIds.'" id="product_all_ids"/><div id="main_products_select" style="display:none;width:640px"></div>
                <script type="text/javascript">
                    function toggleMainProducts(){
                        if($("main_products_select").style.display == "none"){
                            var url = "' . $this->getUrl('adminhtml/adminhtml_deals/chooseProducts') . '";
                            var params = $("linked_product").value.split(", ");
                            var parameters = {"form_key": FORM_KEY,"selected[]":params };
                            var request = new Ajax.Request(url,
                            {
                                evalScripts: true,
                                parameters: parameters,
                                onComplete:function(transport){
                                    $("main_products_select").update(transport.responseText);
                                    $("main_products_select").style.display = "block"; 
                                }
                            });
                        }else{
                            $("main_products_select").style.display = "none";
                        }
                    };
             var grid;
                   
                    function constructData(div){
                        grid = window[div.id+"JsObject"];
                        if(!grid.reloadParams){
                            grid.reloadParams = {};
                            grid.reloadParams["selected[]"] = $("linked_product").value.split(", ");
                        }
                    }
                    function toogleCheckAllProduct(el){
                        if(el.checked == true){
                            $$("#main_products_select input[type=checkbox][class=checkbox]").each(function(e){
                                if(e.name != "check_all"){
                                    if(!e.checked){
                                        if($("linked_product").value == "")
                                            $("linked_product").value = e.value;
                                        else
                                            $("linked_product").value = $("linked_product").value + ", "+e.value;
                                        e.checked = true;
                                        grid.reloadParams["selected[]"] = $("linked_product").value.split(", ");
                                    }
                                }
                            });
                        }else{
                            $$("#main_products_select input[type=checkbox][class=checkbox]").each(function(e){
                                if(e.name != "check_all"){
                                    if(e.checked){
                                        var vl = e.value;
                                        if($("linked_product").value.search(vl) == 0){
                                            if($("linked_product").value == vl) $("linked_product").value = "";
                                            $("linked_product").value = $("linked_product").value.replace(vl+", ","");
                                        }else{
                                            $("linked_product").value = $("linked_product").value.replace(", "+ vl,"");
                                        }
                                        e.checked = false;
                                        grid.reloadParams["selected[]"] = $("linked_product").value.split(", ");
                                    }
                                }
                            });
                            
                        }
                    }
                    function selectProduct(e) {
                        if(e.checked == true){
                            if(e.id == "main_on"){
                                $("linked_product").value = $("product_all_ids").value;
                            }else{
                                if($("linked_product").value == "")
                                    $("linked_product").value = e.value;
                                else
                                    $("linked_product").value = e.value;
                                grid.reloadParams["selected[]"] = $("linked_product").value;
                            }
                        }else{
                             if(e.id == "main_on"){
                                $("linked_product").value = "";
                            }else{
                                var vl = e.value;
                                if($("linked_product").value.search(vl) == 0){
                                    $("linked_product").value = $("linked_product").value.replace(vl+", ","");
                                }else{
                                    $("linked_product").value = $("linked_product").value.replace(", "+ vl,"");
                                }
                            }
                        }
                    }
                </script>'  
        ));
        
        $fieldset->addField('linked_category', 'text', array(
            'name' => 'linked_category',
            'label'     => Mage::helper('mobinotification')->__('Choose Category'),
            'title'     => Mage::helper('mobinotification')->__('Choose Category'),
            'note'  => Mage::helper('mobiconnect')->__('Choose a category having deal'),
            'after_element_html' => '<a id="category_id" href="javascript:void(0)" onclick="toggleMainCategories($(this).value)"><img src="' . $this->getSkinUrl('images/rule_chooser_trigger.gif') . '" alt="" class="v-middle rule-chooser-trigger" title="Select Category"></a>
                <div id="main_categories_select" style="display:none"></div>
                    <script type="text/javascript">
                    function toggleMainCategories(check){
                        var cate = $("main_categories_select");
                        if($("main_categories_select").style.display == "none" || (check ==1) || (check == 2)){
                            var url = "' . $this->getUrl('adminhtml/adminhtml_deals/chooseCategories') . '";
                            if(check == 1){
                                $("linked_category").value = $("category_all_ids").value;
                            }else if(check == 2){
                                $("linked_category").value = "";
                            }
                            var params = $("linked_category").value.split(", ");
                            var parameters = {"form_key": FORM_KEY,"selected[]":params };
                            var request = new Ajax.Request(url,
                                {
                                    evalScripts: true,
                                    parameters: parameters,
                                    onComplete:function(transport){
                                        $("main_categories_select").update(transport.responseText);
                                        $("main_categories_select").style.display = "block";
                                    }
                                });
                        if(cate.style.display == "none"){
                            cate.style.display = "";
                        }else{
                            cate.style.display = "none";
                        }
                    }else{
                        cate.style.display = "none";
                    }
                };
        </script>
            '
        ));
        $fieldset->addField('image', 'image', array(
          'label'     => Mage::helper('mobinotification')->__('Notification Image'),
          'required'  => false,
          'name'      => 'image',
        ));
        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('shedule', 'date', array(
        'name'   => 'shedule',
        'label'  => Mage::helper('mobinotification')->__('Schedule'),
        'title'  => Mage::helper('mobinotification')->__('Schedule'),
        'image'  => $this->getSkinUrl('images/grid-cal.gif'),
        'input_format' => $dateFormatIso,
        'format'       => $dateFormatIso,
        'tabindex' => 1,
        'time' => true
        ));
        $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('mobinotification')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobinotification')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobinotification')->__('Disabled'),
              ),
          ),
      ));
-       $this->setChild('form_after', $this->getLayout()
          ->createBlock('adminhtml/widget_form_element_dependence')
          ->addFieldMap('type', 'type')
          ->addFieldMap('linked_page', 'linked_page')
          ->addFieldDependence('linked_page', 'type', 3)
          ->addFieldMap('linked_product', 'linked_product')
          ->addFieldDependence('linked_product', 'type', 1)
          ->addFieldMap('linked_category', 'linked_category')
          ->addFieldDependence('linked_category', 'type', 2) 
          );
        if (Mage::getSingleton('adminhtml/session')->getFormData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
            Mage::getSingleton('adminhtml/session')->setGroupData(null);
        } elseif ( Mage::registry('mobinotification_data') ) {
              $form->setValues(Mage::registry('mobinotification_data')->getData());
        }
        return parent::_prepareForm();
    }


}
