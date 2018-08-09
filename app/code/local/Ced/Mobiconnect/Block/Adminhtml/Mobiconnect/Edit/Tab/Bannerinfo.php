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
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Edit_Tab_Bannerinfo extends Mage_Adminhtml_Block_Widget_Form
{

	/**
	 * prepare tab form's information
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Edit_Tab_Bannerinfo
	 */
    public function getlayoutType(){
        $options=Mage::getModel('mobiconnect/banner')->getLayoutType();
        /*$options=array(
            'simple'=>'simple',
            'core'=>'core',
            );*/
        return $options;
    }
    
     protected function _prepareForm()
    { 
        $categorymodel=Mage::getModel('catalog/category');
        $id=$this->getRequest()->getParam('id');
        $bannerdata=Mage::registry('banners_data')->getData();

        $productid = (isset($bannerdata['product_id']) && $bannerdata['product_id'] ) ? $bannerdata['product_id']: '';
        $categoryid = (isset($bannerdata['category_id']) && $bannerdata['category_id'] ) ?$bannerdata['category_id']:'';

        $model=Mage::getModel('catalog/product');
        $form = new Varien_Data_Form();
        $this->setForm($form);
        if (Mage::getSingleton('adminhtml/session')->getConnectorData()) {
            $data = Mage::getSingleton('adminhtml/session')->getConnectorData();
            Mage::getSingleton('adminhtml/session')->setConnectorData(null);
        } 
        elseif (Mage::registry('banners_data'))
        $data = Mage::registry('banners_data')->getData();
        
        $fieldset = $form->addFieldset('banners_form', array('legend'=>Mage::helper('mobiconnect')->__('Banners information')));

        $object = Mage::getModel('mobiconnect/banner')->load( $this->getRequest()->getParam('id') );

        /**get image uploaded path */
        $imgPath = Mage::getBaseUrl('media')."Banners/images/".$object['image'];

        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('mobiconnect')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));
      
        $fieldset->addField('home_layout', 'select', array(
            'label'     => Mage::helper('mobiconnect')->__('Home Layout'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'home_layout',
            'values'    => $this->getLayoutType(),
        ));
        if($object['image']){
            $fieldset->addField('image', 'image', array(
                    'label'     => Mage::helper('mobiconnect')->__('Change Banner'),
                    'required'  => false,
                    'name'      => 'image',
                  // 'after_element_html' => 'comment:Image Size should be 1799*100 for simple and //2000*669 for lite'
            ));
        }else {
            $fieldset->addField('image', 'image', array(
                    'label'     => Mage::helper('mobiconnect')->__('Choose Banner'),
                    'required'  => false,
                    'name'      => 'image',
                  //  'after_element_html' => 'comment:Image Size should be 1799*100 for simple and 2000*669 for lite'
            ));
        }
        
          $fieldset->addField('show_in', 'select', array(
            'label' => Mage::helper('mobiconnect')->__('Link to'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'show_in',
                'id'=>'show_int',
            'values' => Mage::getModel('mobiconnect/show')->toOptionArray(),
            'onchange' => 'checkSelectedItem(this.value)',    
        ))->setAfterElementHtml('<script>
                                    window.onload = function()
                                    {
                                        var e=document.getElementById("show_in");
                                        var selectvalue = e.options[e.selectedIndex].value;
                                        var id="'.$id.'";
                                        var productvalue="'.$productid.'";
                                        var categoryvalue="'.$categoryid.'";
                                        var productelement=document.getElementsByName("product_id");
                                        var categorylement=document.getElementsByClassName("category_id");
                                        var labelelement=document.getElementsByClassName("widget-option-label");
                                       
                                        if(id!=""){
                                            if(productvalue!="0"){
                                                productelement[0].classList.remove("required-entry");
                                                productelement[0].value=productvalue;
                                            }  
                                            if(categoryvalue!="0"){
                                                categorylement[0].classList.remove("required-entry");
                                                categorylement[0].value=categoryvalue;
                                            }
                                            labelelement[0].style.display="none";
                                            labelelement[1].style.display="none";
                                        }
                                        
                                        if(selectvalue == "1")
                                        {
                                          var previouselem=  document.getElementById("chooserproduct_id").parentElement.parentElement.previousElementSibling;
                                          var element=document.getElementById("chooserproduct_id").parentElement.parentElement;
                                          previouselem.style.display="block";
                                          element.style.display="block";

                                          var categoryelem=  document.getElementById("choosercategory_id").parentElement.parentElement.previousElementSibling;
                                          var catelement=document.getElementById("choosercategory_id").parentElement.parentElement;
                                          categoryelem.style.display="none";
                                          catelement.style.display="none";

                                          document.getElementById("banner_url").parentElement.parentElement.style.display = "none";

                                           var catelem=document.getElementsByClassName("category_id");
                                          
                                          catelem[0].classList.remove("required-entry");

                                          var bannerelem=document.getElementsByClassName("banner_url");
                                          
                                          bannerelem[0].classList.remove("required-entry");
                                        }

                                        else if(selectvalue == "2")
                                        {
                                          var previouselem=  document.getElementById("chooserproduct_id").parentElement.parentElement.previousElementSibling;
                                          var element=document.getElementById("chooserproduct_id").parentElement.parentElement;
                                          previouselem.style.display="none";
                                          element.style.display="none";

                                          var categoryelem=  document.getElementById("choosercategory_id").parentElement.parentElement.previousElementSibling;
                                          var catelement=document.getElementById("choosercategory_id").parentElement.parentElement;
                                          categoryelem.style.display="block";
                                          catelement.style.display="block";

                                          document.getElementById("banner_url").parentElement.parentElement.style.display = "none";

                                          var proelem=document.getElementsByClassName("product_id");
                                          
                                          proelem[0].classList.remove("required-entry");

                                          var bannerelem=document.getElementsByClassName("banner_url");
                                          
                                          bannerelem[0].classList.remove("required-entry");
                                        }
                                        

                                        else if(selectvalue == "3")
                                        {
                                          var previouselem=  document.getElementById("chooserproduct_id").parentElement.parentElement.previousElementSibling;
                                          var element=document.getElementById("chooserproduct_id").parentElement.parentElement;
                                          previouselem.style.display="none";
                                          element.style.display="none";

                                          var categoryelem=  document.getElementById("choosercategory_id").parentElement.parentElement.previousElementSibling;
                                          var catelement=document.getElementById("choosercategory_id").parentElement.parentElement;
                                          categoryelem.style.display="none";
                                          catelement.style.display="none";

                                          document.getElementById("banner_url").parentElement.parentElement.style.display = "block";

                                          var proelem=document.getElementsByClassName("product_id");
                                          
                                          proelem[0].classList.remove("required-entry");

                                          var catelem=document.getElementsByClassName("category_id");
                                          
                                          catelem[0].classList.remove("required-entry");
                                        }
                                    }

                                    function checkSelectedItem(selectvalue)
                                     {
                                      var labelelement=document.getElementsByClassName("widget-option-label");
                                        labelelement[0].style.display="block";
                                        labelelement[1].style.display="block";
                                        if(selectvalue == "1")
                                        {
                                          var previouselem=  document.getElementById("chooserproduct_id").parentElement.parentElement.previousElementSibling;
                                          var element=document.getElementById("chooserproduct_id").parentElement.parentElement;
                                          previouselem.style.display="block";
                                          element.style.display="block";

                                          var categoryelem=  document.getElementById("choosercategory_id").parentElement.parentElement.previousElementSibling;
                                          var catelement=document.getElementById("choosercategory_id").parentElement.parentElement;
                                          categoryelem.style.display="none";
                                          catelement.style.display="none";

                                          document.getElementById("banner_url").parentElement.parentElement.style.display = "none";

                                          var proelem=document.getElementsByClassName("product_id");
                                          
                                          proelem[0].className += " required-entry";

                                           var catelem=document.getElementsByClassName("category_id");
                                          
                                          catelem[0].classList.remove("required-entry");

                                          var bannerelem=document.getElementsByClassName("banner_url");
                                          
                                          bannerelem[0].classList.remove("required-entry");
                                        }

                                        else if(selectvalue == "2")
                                        {
                                          var previouselem=  document.getElementById("chooserproduct_id").parentElement.parentElement.previousElementSibling;
                                          var element=document.getElementById("chooserproduct_id").parentElement.parentElement;
                                          previouselem.style.display="none";
                                          element.style.display="none";

                                          var categoryelem=  document.getElementById("choosercategory_id").parentElement.parentElement.previousElementSibling;
                                          var catelement=document.getElementById("choosercategory_id").parentElement.parentElement;
                                          categoryelem.style.display="block";
                                          catelement.style.display="block";

                                          document.getElementById("banner_url").parentElement.parentElement.style.display = "none";

                                          var proelem=document.getElementsByClassName("product_id");
                                          
                                          proelem[0].classList.remove("required-entry");

                                          var bannerelem=document.getElementsByClassName("banner_url");
                                          
                                          bannerelem[0].classList.remove("required-entry");

                                          var catelem=document.getElementsByClassName("category_id");
                                          
                                          catelem[0].className += " required-entry";
                                        }
                                        

                                        else if(selectvalue == "3")
                                        {
                                          var previouselem=  document.getElementById("chooserproduct_id").parentElement.parentElement.previousElementSibling;
                                          var element=document.getElementById("chooserproduct_id").parentElement.parentElement;
                                          previouselem.style.display="none";
                                          element.style.display="none";

                                          var categoryelem=  document.getElementById("choosercategory_id").parentElement.parentElement.previousElementSibling;
                                          var catelement=document.getElementById("choosercategory_id").parentElement.parentElement;
                                          categoryelem.style.display="none";
                                          catelement.style.display="none";

                                          document.getElementById("banner_url").parentElement.parentElement.style.display = "block";

                                          var proelem=document.getElementsByClassName("product_id");
                                          
                                          proelem[0].classList.remove("required-entry");

                                          var catelem=document.getElementsByClassName("category_id");
                                          
                                          catelem[0].classList.remove("required-entry");

                                          var bannerelem=document.getElementsByClassName("banner_url");
                                          
                                          bannerelem[0].className += " required-entry";
                                        }
                                     }
                                    
                                    </script>');
        
         
         $productLink = $fieldset->addField('product_id', 'label', array(
            'name' => 'product_id',
            'label' => Mage::helper('mobiconnect')->__('Product Id'),
            'class' => 'widget-option product_id',
            'id'=>'product_id',
            'value' => $model->getProductLink(),
            'required' => true,
        ));
        $productConfig = array(
            'input_name'  => 'product_link',
            'input_label' => $this->__('Product'),
            'button_text' => $this->__('Select Product...'),
           // 'required'    => true
        );
        $model->unsProduct1Link();
        $helperBlock = $this->getLayout()->createBlock('adminhtml/catalog_product_widget_chooser');
        if ($helperBlock instanceof Varien_Object) {
            $helperBlock->setConfig($productConfig)
                ->setFieldsetId($fieldset->getId())
                ->setTranslationHelper(Mage::helper('mobiconnect'))
                ->prepareElementHtml($productLink);
        }

        $category1Link = $fieldset->addField('category_id', 'label', array(
            'name' => 'category_id',
            'label' => Mage::helper('mobiconnect')->__('Category Id'),
            'class' => 'widget-option category_id',
            'value' => $categorymodel->getCategoryLink(),
            'required' => true,
        ));
        $categoryConfig = array(
            'input_name'  => 'category_link',
            'input_label' => $this->__('Category'),
            'button_text' => $this->__('Select Category...'),
           // 'required'    => true
        );
        $model->unsProduct1Link();
        $helperBlock = $this->getLayout()->createBlock('adminhtml/catalog_category_widget_chooser');
        if ($helperBlock instanceof Varien_Object) {
            $helperBlock->setConfig($categoryConfig)
                ->setFieldsetId($fieldset->getId())
                ->setTranslationHelper(Mage::helper('mobiconnect'))
                ->prepareElementHtml($category1Link);
        }

        $fieldset->addField('banner_url', 'text', array(
                'name' => 'banner_url',
                'class'=>'banner_url',
                'required'    => true,
                'label' => Mage::helper('mobiconnect')->__('Url'),
                'title' => Mage::helper('mobiconnect')->__('Url'),
        ));
        
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('mobiconnect')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('mobiconnect')->__('Enabled'),
                ),

                array(
                    'value'     => 2,
                    'label'     => Mage::helper('mobiconnect')->__('Disabled'),
                ),
            ),
        ));
        
        $fieldset->addField('description', 'editor', array(
            'name'      => 'description',
            'label'     => Mage::helper('mobiconnect')->__('Description'),
            'title'     => Mage::helper('mobiconnect')->__('Description'),
            'style'     => 'width:280px; height:200px;',
            'required'  => true,
        ));
        
       /* $fieldset->addField('sort_order', 'text', array(
                'label'     => Mage::helper('mobiconnect')->__('Sort Order'),
                'required'  => false,
                'name'      => 'sort_order',
        ));*/
        //$imagepath=$data['image'];
        //$data['image']='Banners/images/'.$imagepath;
        if($this->getRequest()->getParam('id')!=null){
            $imagepath=$data['image'];
            $data['image']='Banners/images/'.$imagepath;
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }
}
?>
<style type="text/css">
.label,.value
{
  display:inline-block;
}
</style>