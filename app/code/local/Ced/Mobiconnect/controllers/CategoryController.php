<?php 
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_Mobiconnect
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_Mobiconnect_CategoryController extends Ced_Mobiconnect_Controller_Action {
	public function indexAction() {
		$this->loadLayout ();
		$this->renderLayout ();
	}
	/**
	 * Get Top level category option
	 */
	public function getmaincategoriesAction() 
	{
		$id = $this->getRequest ()->getParam ( 'id' );
		$categories = Mage::getModel ( 'mobiconnect/catalog_category' )->getCategories ( $id);
		$this->printJsonData ( $categories );
	}
	/**
	 * Get category product,available filter,and sorting option
	 */
	public function categoryProductsAction() {

		$data=$this->getRequest()->getParams();
		
		//var_dump(Mage::helper('core')->urlDecode($data));die('sdfhkjhf');
		$id = $this->getRequest ()->getParam ( 'id' );
		if(!isset($id) || $id=="" ){
			echo 'return';return;

		}
		$order = $this->getRequest ()->getParam ( 'order' );
		$dir = $this->getRequest ()->getParam ( 'dir' );
		$offset = $this->getRequest ()->getParam ( 'page' );
		$customerId= $this->getRequest ()->getParam ('customer_id');
		$filtername = $this->getRequest ()->getParam ( 'filter_parent' );
		$filterid = $this->getRequest ()->getParam ( 'filter_id' );
		$limit = '5';
		$data = array (
				'id' => $id,
				'order' => $order,
				'dir' => $dir,
				'offset' => $offset,
				'limit' => $limit,
				'customer_id'=>$customerId,
				'filtername' => $filtername,
				'filterid' => $filterid 
		);
		$layer = Mage::getModel ( "catalog/layer" );
		
		$category = Mage::getModel ( "catalog/category" )->load ( $id );
		$layer->setCurrentCategory ( $category );
		$attributes = $layer->getFilterableAttributes ();
		
		foreach ( $attributes as $attribute ) {
			if ($attribute->getAttributeCode () == 'price') {
				$filterBlockName = 'catalog/layer_filter_price';
			} elseif ($attribute->getBackendType () == 'decimal') {
				$filterBlockName = 'catalog/layer_filter_decimal';
			} else {
				$filterBlockName = 'catalog/layer_filter_attribute';
			}
			
			$result = $this->getLayout ()->createBlock ( $filterBlockName )->setLayer ( $layer )->setAttributeModel ( $attribute )->init ();
			
			$attCode = $attribute->getAttributeCode ();
			$attlabel=$attribute->getStoreLabel ();
			foreach ( $result->getItems () as $option ) {
				$attOptions [] = $option->getValue () . '#' . $option->getLabel ();
			}
			if (count ( $attOptions ) > 0)
				$filterableAttributes [] = array (
						$attCode => array_values ( $attOptions )
				);
				$filterlabel[]=array(
						'att_code'=>$attCode,
						'att_label'=>$attlabel
						);
			$attOptions = array ();
		}
		
		$catproduct = Mage::getModel ( 'mobiconnect/catalog_product' )->getCategoryProduct ( $data );
		if(is_array($catproduct))
		{
		$catproduct ['data'] ['filter'] = $filterableAttributes;
		$catproduct['data']['filter_label']=$filterlabel;
		// $data=array('data'=>array('products'=>$information,'filter'=>$filterableAttributes,'status'=>'enabled'));
		$this->printJsonData ( $catproduct );
	}
	else{
		echo 'NO_PRODUCTS';
		die;
	}
	}
	public function productViewAction() {
		$id = $this->getRequest ()->getParam ( 'prodID' );
		if(!isset($id) || $id=="" ){
			echo 'return';return;
		}
		$customerid=$this->getRequest()->getParam('customer_id');
		$productview = Mage::getModel ('mobiconnect/catalog_product')->productView ($id,$customerid);
		
		$this->printHtmlJsonData ( $productview );
	}
	public function productSearchAction(){

		$query = $this->getRequest ()->getParam ( 'query' );
		$order = $this->getRequest ()->getParam ( 'order' );
		$dir = $this->getRequest ()->getParam ( 'dir' );
		$offset = $this->getRequest ()->getParam ( 'page' );
		$filtername = $this->getRequest ()->getParam ( 'filter_parent' );
		$filterid = $this->getRequest ()->getParam ( 'filter_id' );
		$limit = '5';
		$data = array (
				'query' => $query,
				'order' => $order,
				'dir' => $dir,
				'offset' => $offset,
				'limit' => $limit,
				'filtername' => $filtername,
				'filterid' => $filterid
		);
		
		if($query=='')
		{
			echo "Query Cann't be blank";
			die;
		}
	
		$searchedProduct = Mage::getModel ( 'mobiconnect/catalog_product' )->getSearchedProduct ( $data );
		if(is_array($searchedProduct))
		{
	
			$this->printJsonData ( $searchedProduct );
		}
		else{
			echo 'NO_PRODUCTS';
			die;
		}
	
	}	

	public function getSecondLevelCategoryAction()
	{
		$store = $this->getRequest()->getParam('store_id',Mage::app()->getStore()->getId());
		$categories = Mage::getModel('catalog/category')->getCollection()
					->setStoreId($store)
					->addAttributeToSelect ( 'name','entity_id','parent_id','level','children_count','is_active','include_in_menu')
					->addAttributeToFilter ( 'is_active', '1' )
					->addAttributeToFilter('include_in_menu', '1');
        
        if ($categories) {
            $categories_array = array();
            foreach($categories as $category) {

                if($category->getId()=='2')
                    continue;
                if($category->getLevel()=='2'){

                    $categories_array[$category->getEntityId()]=[
                        'main_category_id'=> $category->getEntityId(),
                        'main_category_name'=> $category->getName(),
                    ];
                }
                
            }
              
            if(count($categories_array)){
                foreach ($categories_array as $key => $value) {
                    if(isset($value['main_category_id']) && $value['main_category_id'])
                        $categoryObj = Mage::getModel('catalog/category')->load($value['main_category_id']);
                    $subcategories = $categoryObj->getAllChildren(true);
                    if(count($subcategories)>'1'){
                    	foreach($subcategories as $subcategorie) {
                       		$subcategory= Mage::getModel('catalog/category')->load($subcategorie);
                       		if($subcategory->getEntityId()==$value['main_category_id'])
                        		continue;
                       		if($subcategory->getLevel() > 3)
                        		continue;
                        	$categories_array[$value['main_category_id']]['has_child']=true;
                        	$categories_array[$value['main_category_id']]['sub_cats'][]=[
                                                                'sub_category_id'=> $subcategory->getEntityId(),
                                                                'sub_category_name'=>$subcategory->getName() ,
                                                            
                                                                'has_child'=>($subcategory->hasChildren())?true:false
                                                                ];     
                    	}
                    } else {
                        $categories_array[$value['main_category_id']]['sub_cats']=[];
                        $categories_array[$value['main_category_id']]['has_child']=false;     
                    }
                }
            }
              
            $data = [
                    'status' => 'success' 
            ];
            $data['data']['categories']= [$categories_array];
            $this->printJsonData ($data);

        } else {
            $data = array (
                    'data' => array (
                        'categories' =>Mage::helper('mobiconnect')->__('You dont have any categories') 
                    ),
                    'status' => 'success' 
            );
            $this->printJsonData ($data);
        }
	}
}


