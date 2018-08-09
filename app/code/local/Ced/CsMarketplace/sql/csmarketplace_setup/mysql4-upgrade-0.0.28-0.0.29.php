<?php
$model = Mage::getResourceModel('catalog/setup','catalog_setup');
					  $data=array(
					  	'type'=>'varchar',
			  			'input'=>'select', //for Yes/No dropdown
			  			'sort_order'=>5,
			  			'label'=>'Restaurants',
			  			'source'   => "csmarketplace/source_restaurants",
			  			'required'=>'0',
			  			'comparable'=>'0',
			  			'searchable'=>'0',
			  			'is_configurable'=>'1',
			  			'user_defined'=>'1',
			  			'visible_on_front' => 1, //want to show on frontend?
			  			'visible_in_advanced_search' => 1,
			  			'is_html_allowed_on_front' => 1,
			  			'required'=> 0,
			  			'unique'=>false,
					  	'is_configurable' => true,
			  			'apply_to' => 'configurable,simple','bundled','grouped','virtual','downloadable' //simple,configurable,bundled,grouped,virtual,downloadable
			  			
			  	                  );
			  	     
$model->addAttribute('catalog_product','restaurants',$data);
$model->addAttributeToSet('catalog_product', 'Default', 'General', 'restaurants');
			  	     