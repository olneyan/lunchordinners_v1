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
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
/**
* Override Controller of Mobiconnect for advance product system 
**/
require_once Mage::getModuleDir('controllers', 'Ced_Mobiconnect').DS.'CategoryController.php';
class Ced_MobiconnectAdvCart_CategoryController extends Ced_Mobiconnect_CategoryController {
   	/**
	* Override get_main_categories of Mobiconnect if advance product system enable
	**/
   	public function getmaincategoriesAction() {  
		$id = $this->getRequest ()->getParam ( 'id' );
		$categories = Mage::getModel ( 'mobiconnectadvcart/catalog_category' )->getCategories ( $id );
		$this->printJsonData ( $categories );
	}
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
		//$filtername = $this->getRequest ()->getParam ( 'filter_parent' );
		//$filterid = $this->getRequest ()->getParam ( 'filter_id' );
		$filter=$this->getRequest()->getParam('multi_filter');
		Mage::log($filter,null,'multifilter.log');
		$filter=json_decode($filter,true);
		/*var_dump($filter);die;*/
		$limit = '5';
		$data = array (
				'id' => $id,
				'order' => $order,
				'dir' => $dir,
				'offset' => $offset,
				'limit' => $limit,
				'customer_id'=>$customerId,
				//'filtername' => $filtername,
				//'filterid' => $filterid ,
				'filter'=>$filter
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
		
		$catproduct = Mage::getModel ( 'mobiconnectadvcart/catalog_product' )->getCategoryProduct ( $data );
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
	/**
	* Override productView of Mobiconnect if advance product system enable
	**/
	public function productViewAction() { 
		$id = $this->getRequest ()->getParam ( 'prodID' );
		if(!isset($id) || $id=="" ){
			echo 'return';return;

		}
		
		$customerid=$this->getRequest ()->getParam ( 'customer_id' );
		$productview = Mage::getModel ( 'mobiconnectadvcart/catalog_product' )->productView ($id,$customerid);
		
		$this->printHtmlJsonData ( $productview );
	}

	public function productSearchAction(){
		
		$query = $this->getRequest ()->getParam ( 'query' );
		$order = $this->getRequest ()->getParam ( 'order' );
		$dir = $this->getRequest ()->getParam ( 'dir' );
		$offset = $this->getRequest ()->getParam ( 'page' );
		$pageNew = $this->getRequest ()->getParam ( 'page_new' );
		//$filtername = $this->getRequest ()->getParam ( 'filter_parent' );
		//$filterid = $this->getRequest ()->getParam ( 'filter_id' );
		$filter= $this->getRequest()->getParam('multi_filter');
		$filter=json_decode($filter,true);
		$limit = '20';
		$data = array (
				'query' => $query,
				'order' => $order,
				'dir' => $dir,
				'offset' => $offset,
				'limit' => $limit,
				'filter'=>$filter,
				'page_new'=>$pageNew
				//'filtername' => $filtername,
				//'filterid' => $filterid
		);
		
		if($query=='')
		{
			echo 'NO_PRODUCTS';
			die;
		}
	
		$searchedProduct = Mage::getModel ( 'mobiconnectadvcart/catalog_product' )->getSearchedProduct ( $data );
		if(is_array($searchedProduct))
		{
	
			$this->printJsonData ( $searchedProduct );
		}
		else{
			echo 'NO_PRODUCTS';
			die;
		}
	
	}	

	public function autocompleteSearchAction(){
		$key=$this->getRequest()->getParam('keyword');
		if($key=='' || $key=='NULL'){
			echo "return";return;
		}
		$suggestion = Mage::getModel ( 'mobiconnectadvcart/catalog_category' )->getSuggestion ($key);
		$this->printJsonData ($suggestion);

	}
}


