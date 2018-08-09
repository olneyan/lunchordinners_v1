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
 * @package     Ced_CsProduct
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
var varienTabs = new Class.create();

varienTabs.prototype = {
    initialize : function(containerId, destElementId,  activeTabId, shadowTabs){
        this.containerId    = containerId;
        this.destElementId  = destElementId;
        this.activeTab = activeTabId;
		
        this.tabOnClick  = this.tabMouseClick.bindAsEventListener(this);
        this.tabs = $$('#'+this.containerId+' a.tab-item-link');
		
        for (var tab=0; tab<this.tabs.length; tab++) {
            Event.observe(this.tabs[tab],'click',this.tabOnClick);
			var tabContentElement = $(this.getTabContentElementId(this.tabs[tab]));
		//	tabContentElement.container = this;
            tabContentElement.statusBar = this.tabs[tab];
       //     tabContentElement.tabObject  = this.tabs[tab];
			
            if (Element.hasClassName($(this.tabs[tab].id), 'ajax')) {
                Element.addClassName($(this.tabs[tab].id), 'notloaded');
            }

        }
    },
    
	getTabContentElementId : function(tab){
        if(tab){
        //    return tab.id+'_content';
			return tab.name;
        }
        return false;
    },
	
    tabMouseClick : function(event) {
        var tab = Event.findElement(event, 'a');		
		this.activeTab = tab;		
		this.showTabContent(tab);
			
    },
	
	
	showTabContent : function(tab) {

		var isAjax = Element.hasClassName(tab, 'ajax');
        var isNotLoaded = Element.hasClassName(tab, 'notloaded');
		
		if ( isAjax && tab.title.indexOf('#') != tab.title.length-1 && isNotLoaded )
        {
            /*$('activity-loading').show();*/
            new Ajax.Request(tab.title, {
                parameters: {form_key: FORM_KEY},
                evalScripts: true,
                onSuccess: function(transport) {
                    try {
                      	/*$('activity-loading').hide();*/
                        if (transport.responseText.isJSON()) {
                            var response = transport.responseText.evalJSON()
                            if (response.error) {
                                alert(response.message);
                            }
                            if(response.ajaxExpired && response.ajaxRedirect) {
                                setLocation(response.ajaxRedirect);
                            }
                        } else {
                            $(tab.name).update(transport.responseText);
							if (!Element.hasClassName(tab, 'ajax only')) {
								Element.removeClassName(tab, 'notloaded');
							}
                        }
                    }
                    catch (e) {
                        $(tab.name).update(transport.responseText);
						if (!Element.hasClassName(tab, 'ajax only')) {
							Element.removeClassName(tab, 'notloaded');
						}
                    }
                }.bind(this)
            });
        }
    },

}
