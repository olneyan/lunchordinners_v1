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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
var ced_csmarketplace = Class.create();
var ced_csmarketplace = ced_csmarketplace;
ced_csmarketplace.prototype = {
  initialize: function(url,form) {
	this.url  = url;
	this.form  = form;
	this.defaultDetail = '';
  },
  changePaymentToOther: function(elem) {
	if (this.defaultDetail.length <= 0) this.defaultDetail = document.getElementById('beneficiary-payment-detail').innerHTML;
	if(elem && elem.value && elem.value == 'other') {
		document.getElementById('beneficiary-payment-detail').innerHTML = '';
		document.getElementById('payment_code_other').style.display = '';
		document.getElementById('payment_code_other').removeAttribute('disabled');
		document.getElementById('payment_code_other').className = 'required-entry input-text';
		document.getElementById('payment_code').name = 'payment_code_other';
	} else {
		document.getElementById('payment_code_other').style.display = 'none';
		document.getElementById('payment_code_other').setAttribute('disabled','true');
		document.getElementById('payment_code_other').className = 'input-text';
		document.getElementById('beneficiary-payment-detail').innerHTML = this.defaultDetail;
		document.getElementById('payment_code').name = 'payment_code';
	}
  },
  changePaymentDatail: function(elem) {
	if(elem && elem.value && elem.value == 'other') {
		this.changePaymentToOther(elem);
	} else {
		this.changePaymentToOther(elem);
		if (this.defaultDetail.length <= 0) this.defaultDetail = document.getElementById('beneficiary-payment-detail').innerHTML;
		if(elem.value != '') {
			url = this.url + 'method/' + elem.value + '/';
			new Ajax.Updater('beneficiary-payment-detail',url);
		} else {
			document.getElementById('beneficiary-payment-detail').innerHTML = this.defaultDetail;
		}
	}
  },
  
  showVendorFrom: function(elem) {
	if(elem.checked){ 
		document.getElementById('ced-csmarketplace-registration-fields').style.display = '';
		document.getElementById('ced-public-name').style.display = ''; 
		document.getElementById('ced-shop-url').style.display = ''; 
	} else { 
		document.getElementById('ced-csmarketplace-registration-fields').style.display = 'none';
		document.getElementById('ced-public-name').style.display = 'none'; 
		document.getElementById('ced-shop-url').style.display = 'none'; 
	}
  },
  checkUrlAvailability: function() {
		this.csformvalidator = new VarienForm(this.form, true);
		return this.csformvalidator.validator && this.csformvalidator.validator.validate();
	}
};

Validation.add('validate-shopurl', 'Please enter a valid URL Key. For example "example-page", "example-page.html" or "anotherlevel/example-page".', function(v) {     
 
	var url = ced_csmarketplace.url;
	var formId = ced_csmarketplace.form;
	
    var ok = false;
	if(document.getElementById('activity-loading')) document.getElementById('activity-loading').show();
    new Ajax.Request(url, {
        method: 'post',
        asynchronous: false,
        onSuccess: function(transport) {
			if(document.getElementById('activity-loading')) document.getElementById('activity-loading').hide();
            var response = transport.responseText.evalJSON();
            validateTrueEmailMsg = response.message;

            if (response.success == 0) {
				//alert('Error: '+validateTrueEmailMsg);
				if ($('advice-validate-shopurl-ced-shop-url-field')) {
					$('advice-validate-shopurl-ced-shop-url-field').remove();
				}
				if ($('advice-validate-shopurl-shop_url')) {
					$('advice-validate-shopurl-shop_url').remove();
				}
				
				if(document.getElementById('ced-csmarketplace-availability')) {
					if(response.suggestion != '') {
						var shopUrlField = 0;
						if($('ced-shop-url-field')) {
							document.getElementById('ced-csmarketplace-availability-suggestion').innerHTML = response.suggestion +'<input type="checkbox" value="1" name="suggestion_1" onClick="toggleSuggestion(this,$(\'ced-shop-url-field\'),\''+response.raw_shop_url+'\',\''+response.shop_url+'\')"/>';
						}
						if($('shop_url')) {
							document.getElementById('ced-csmarketplace-availability-suggestion').innerHTML = response.suggestion +'<input type="checkbox" value="1" name="suggestion_1" onClick="toggleSuggestion(this,$(\'shop_url\'),\''+response.raw_shop_url+'\',\''+response.shop_url+'\')"/>';
						}
					} else {
						document.getElementById('ced-csmarketplace-availability').className = 'ced-csmarketplace-availability-failed';
					}
				}
                Validation.get('validate-shopurl').error = validateTrueEmailMsg;
                ok = false;
            } else {
				//alert('Success: '+validateTrueEmailMsg);
            	if($('ced-shop-url-field')) {
            		$('ced-shop-url-field').value = response.shop_url;
            	}
            	if($('shop_url')) {
            		$('shop_url').value = response.shop_url;
            	}
				if(document.getElementById('ced-csmarketplace-availability')) {
					document.getElementById('ced-csmarketplace-availability').className = 'ced-csmarketplace-availability-passed';
				}
				if ($('ced-csmarketplace-availability-suggestion')) {
					document.getElementById('ced-csmarketplace-availability-suggestion').innerHTML = response.suggestion;
				}
				if ($('advice-validate-shopurl-ced-shop-url-field')) {
					$('advice-validate-shopurl-ced-shop-url-field').remove();
				}
				if ($('advice-validate-shopurl-shop_url')) {
					$('advice-validate-shopurl-shop_url').remove();
				}
                ok = true; /* return true or false */    
            }
        },
		parameters: Form.serialize(document.getElementById(formId)),
    });
	if(document.getElementById('activity-loading')) document.getElementById('activity-loading').hide();
    return ok;
});
if (typeof imagePreview != 'function') {
	function imagePreview(element){
		if($(element)){
			var win = window.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
			win.document.open();
			win.document.write('<body style="padding:0;margin:0"><img src="'+$(element).src+'" id="image_preview"/></body>');
			win.document.close();
			Event.observe(win, 'load', function(){
				var img = win.document.getElementById('image_preview');
				win.resizeTo(img.width+40, img.height+80)
			});
		}
	}
}

function addGroup() { 
	 var group = document.getElementById('group_select');
	 var group_name = prompt("Please enter a new group name","");
	 group_name = group_name.strip();
	 
	 if(group && group_name.length > 0) {
		group.options[group.options.length] = new Option(group_name, group_name, true, true);
	 }
}

function toggleSuggestion(element,inputElement,raw_shop_url,shop_url) {
	
	csAdviseDivId = document.getElementById('advice-validate-shopurl-'+inputElement.id);
	if(element.checked) {
		inputElement.value = shop_url;
		if(csAdviseDivId) csAdviseDivId.hide();
		inputElement = document.getElementById('ced-csmarketplace-availability');
		inputElement.className = inputElement.className.replace('-failed','-passed');
		ced_csmarketplace.checkUrlAvailability();
	} else {
		inputElement.value = raw_shop_url;
		if(csAdviseDivId) csAdviseDivId.show();
		inputElement = document.getElementById('ced-csmarketplace-availability');
		inputElement.className = inputElement.className.replace('-passed','-failed');
	}
} 
