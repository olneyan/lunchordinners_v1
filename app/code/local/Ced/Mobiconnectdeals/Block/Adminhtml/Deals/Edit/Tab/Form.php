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
class Ced_Mobiconnectdeals_Block_Adminhtml_Deals_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('deal_form', array('legend'=>Mage::helper('mobiconnectdeals')->__('Deal information')));
     
      $fieldset->addField('deal_title', 'text', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'deal_title',
      ));
      $fieldset->addField('offer_text', 'text', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Offer Text'),
          'class'     => 'required-entry',
          'name'      => 'offer_text',
      ));
      $fieldset->addField('deal_image_name', 'image', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Image'),
          'required'  => false,
          'name'      => 'deal_image_name',
	    ));
      $show_in_slider=0;
      if(Mage::registry('deal_data'))
      $show_in_slider=(Mage::registry('deal_data')->getShowInSlider())?1 :0; 
      $fieldset->addField('show_in_slider', 'checkbox', array(
          'label' => Mage::helper('mobiconnectdeals')->__('Show In Main Deal Slider'),
          'name' => 'show_in_slider',
          'value' => 1,
          'checked' => ($show_in_slider == 1) ? 'true' : '',
          'onclick' => 'this.value = this.checked ? 1 : 0;',
           'after_element_html' => '<small>Check if you want this to show in home page\'s top slider </small>',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Disabled'),
              ),
          ),
      ));
       $fieldset->addField('deal_type', 'select', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Deal Type'),
          'name'      => 'deal_type',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Product'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Category'),
              ),
              array(
                  'value'     => 3,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Static'),
              ),
          ),
      ));
      $fieldset->addField('static_link', 'text', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Static Deal Link'),
          'class'     => 'required-entry',
          'name'      => 'static_link',
      ));
      $productIds = implode(",", Mage::getResourceModel('catalog/product_collection')->getAllIds());
        $fieldset->addField('product_link', 'text', array(
            'name' => 'product_link',
            'label'     => Mage::helper('mobiconnectdeals')->__('Choose Product'),
            'title'     => Mage::helper('mobiconnectdeals')->__('Choose Product'),
            'note'  => Mage::helper('mobiconnectdeals')->__('Choose a product for deal'),
            'after_element_html' => '<a id="product_link" href="javascript:void(0)" onclick="toggleMainProducts()"><img src="' . $this->getSkinUrl('images/rule_chooser_trigger.gif') . '" alt="" class="v-middle rule-chooser-trigger" title="Select Products"></a><input type="hidden" value="'.$productIds.'" id="product_all_ids"/><div id="main_products_select" style="display:none;width:640px"></div>
                <script type="text/javascript">
                    function toggleMainProducts(){
                        if($("main_products_select").style.display == "none"){
                            var url = "' . $this->getUrl('adminhtml/adminhtml_deals/chooseProducts') . '";
                            var params = $("product_link").value.split(", ");
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
                            grid.reloadParams["selected[]"] = $("product_link").value.split(", ");
                        }
                    }
                    function toogleCheckAllProduct(el){
                        if(el.checked == true){
                            $$("#main_products_select input[type=checkbox][class=checkbox]").each(function(e){
                                if(e.name != "check_all"){
                                    if(!e.checked){
                                        if($("product_link").value == "")
                                            $("product_link").value = e.value;
                                        else
                                            $("product_link").value = $("product_link").value + ", "+e.value;
                                        e.checked = true;
                                        grid.reloadParams["selected[]"] = $("product_link").value.split(", ");
                                    }
                                }
                            });
                        }else{
                            $$("#main_products_select input[type=checkbox][class=checkbox]").each(function(e){
                                if(e.name != "check_all"){
                                    if(e.checked){
                                        var vl = e.value;
                                        if($("product_link").value.search(vl) == 0){
                                            if($("product_link").value == vl) $("product_link").value = "";
                                            $("product_link").value = $("product_link").value.replace(vl+", ","");
                                        }else{
                                            $("product_link").value = $("product_link").value.replace(", "+ vl,"");
                                        }
                                        e.checked = false;
                                        grid.reloadParams["selected[]"] = $("product_link").value.split(", ");
                                    }
                                }
                            });
                            
                        }
                    }
                    function selectProduct(e) {
                        if(e.checked == true){
                            if(e.id == "main_on"){
                                $("product_link").value = $("product_all_ids").value;
                            }else{
                                if($("product_link").value == "")
                                    $("product_link").value = e.value;
                                else
                                    $("product_link").value = e.value;
                                grid.reloadParams["selected[]"] = $("product_link").value;
                            }
                        }else{
                             if(e.id == "main_on"){
                                $("product_link").value = "";
                            }else{
                                var vl = e.value;
                                if($("product_link").value.search(vl) == 0){
                                    $("product_link").value = $("product_link").value.replace(vl+", ","");
                                }else{
                                    $("product_link").value = $("product_link").value.replace(", "+ vl,"");
                                }
                            }
                        }
                    }
                </script>'  
        ));
        $fieldset->addField('category_link', 'text', array(
            'name' => 'category_link',
            'label'     => Mage::helper('mobiconnectdeals')->__('Choose Category'),
            'title'     => Mage::helper('mobiconnectdeals')->__('Choose Category'),
            'note'  => Mage::helper('mobiconnect')->__('Choose a category having deal'),
            'after_element_html' => '<a id="category_id" href="javascript:void(0)" onclick="toggleMainCategories($(this).value)"><img src="' . $this->getSkinUrl('images/rule_chooser_trigger.gif') . '" alt="" class="v-middle rule-chooser-trigger" title="Select Category"></a>
                <div id="main_categories_select" style="display:none"></div>
                    <script type="text/javascript">
                    function toggleMainCategories(check){
                        var cate = $("main_categories_select");
                        if($("main_categories_select").style.display == "none" || (check ==1) || (check == 2)){
                            var url = "' . $this->getUrl('adminhtml/adminhtml_deals/chooseCategories') . '";
                            if(check == 1){
                                $("category_link").value = $("category_all_ids").value;
                            }else if(check == 2){
                                $("category_link").value = "";
                            }
                            var params = $("category_link").value.split(", ");
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
      $this->setChild('form_after', $this->getLayout()
          ->createBlock('adminhtml/widget_form_element_dependence')
          ->addFieldMap('deal_type', 'deal_type')
          ->addFieldMap('static_link', 'static_link')
          ->addFieldDependence('static_link', 'deal_type', 3)
          ->addFieldMap('product_link', 'product_link')
          ->addFieldDependence('product_link', 'deal_type', 1)
          ->addFieldMap('category_id', 'category_id')
          ->addFieldDependence('category_id', 'deal_type', 2) 
          );
      if (Mage::getSingleton('adminhtml/session')->getFormData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
          Mage::getSingleton('adminhtml/session')->setGroupData(null);
      } elseif ( Mage::registry('deal_data') ) {
          $form->setValues(Mage::registry('deal_data')->getData());
      }
      return parent::_prepareForm();
  }
}
