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
  initialize: function(url) {
	this.url  = url;
  },
  collectPendingPayments: function(params) {
	var url = ced_csmarketplace.url + params;
	new Ajax.Request(url, {
        method: 'get',
        asynchronous: true,
        onSuccess: function(transport) {
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
				if(document.getElementById('ced-csmarketplace-availability'))
					document.getElementById('ced-csmarketplace-availability').className = 'ced-csmarketplace-availability-failed';
                Validation.get('validate-shopurl').error = validateTrueEmailMsg;
                ok = false;
            } else {
				//alert('Success: '+validateTrueEmailMsg);
				if(document.getElementById('ced-csmarketplace-availability'))
					document.getElementById('ced-csmarketplace-availability').className = 'ced-csmarketplace-availability-passed';
				if ($('advice-validate-shopurl-ced-shop-url-field')) {
					$('advice-validate-shopurl-ced-shop-url-field').remove();
				}
				if ($('advice-validate-shopurl-shop_url')) {
					$('advice-validate-shopurl-shop_url').remove();
				}
                ok = true; /* return true or false */    
            }
        },
    });
  }
};