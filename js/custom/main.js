
/**
 * Cedcoss
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Cedcoss;
 * @package     Cedcoss_Groupgift
 * @author 		Cedcoss Magento Core Team <Cedcoss_MagentoCoreTeam@cedcoss.com>
 * @copyright   Copyright CEDCOSS Technologies (http://cedcoss.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

 
function callFancyBox(id1){
	gGift('.price').attr('id','-removed');
	gGift('.regular-price').attr('id','-removed');
	var overlay = gGift('<div id="overlay" style=" position: fixed; top: 0; left: 0; width: 100%; height: 100%;background-color: #000; filter:alpha(opacity=50); -moz-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5; z-index: 10000; display: block;"> </div>');
    overlay.appendTo(document.body);
	gGift.ajax({
		type: 'POST',
		url: gGift(id1).attr('value'),
	})
	 .success (function(result){
			overlay.remove();
			gGift.fancybox({
			'content' : result,
		});		
	});
} 

function customFormsubmit(id) {

	var myForm= new VarienForm('product_addtocart_form',true);
		if(myForm.validator.validate()) {
			var overlay = gGift('<div id="overlay" style=" position: fixed; top: 0; left: 0; width: 100%; height: 100%;background-color: #000; filter:alpha(opacity=50); -moz-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5; z-index: 10000; display: block;"> </div>');
			overlay.appendTo(document.body);
			var formData = gGift('#product_addtocart_form').serializeArray();	
			gGift.ajax({
			type: 'POST',
			url: gGift(id).attr('value'),
			data: formData,
			})
			 .success (function(result){
				overlay.remove();
				 if(gGift('.fancybox-inner')[0]){
					gGift('.fancybox-inner').html(result);
				 }else{
					gGift.fancybox({
							'content' : result,
						});
				 }
			}); 
		}		
		else {
			return false;
		}
 	 	
}

// for group gift custom form submit for grouped product

function customFormsubmit2(id) {

	var myForm= new VarienForm('product_addtocart_form',true);
		if(myForm.validator.validate()) {
			var overlay = gGift('<div id="overlay" style=" position: fixed; top: 0; left: 0; width: 100%; height: 100%;background-color: #000; filter:alpha(opacity=50); -moz-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5; z-index: 10000; display: block;"> </div>');
			overlay.appendTo(document.body);
			var formData = gGift('#product_addtocart_form').serializeArray();	
			var finalprice = 0;
			for(var i = 0; i < formData.length; i++){
				if (formData[i].name.indexOf("super_group") >= 0 && formData[i].value > 0){
					var child_price = formData[i].name.split("super_group[");
					var child_id = child_price[1].split("]");
					var pro_id = child_id[0];
					finalprice += formData[i].value; 
				}			
			}
			if(finalprice <= 0){
				alert('Please specify the quantity of products to create group gift');
				return false;
				
			}
			
			gGift.ajax({
			type: 'POST',
			url: gGift(id).attr('value'),
			data: formData,
			})
			 .success (function(result){
				overlay.remove();
				if(gGift('.fancybox-inner')[0]){
					gGift('.fancybox-inner').html(result);
				 }else{
					gGift.fancybox({
						'content' : result,
					});
				 }
			}); 
		}		
		else {
			return false;
		}	 	
}
