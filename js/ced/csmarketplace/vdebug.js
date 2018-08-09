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
var isCedAdmin = false;
function captureVScreen(){
	var target = document.getElementsByTagName('body');
	document.getElementById("activity-loading-debug").style.display = 'block';
	  html2canvas(target, {
	  onrendered: function(canvas) {
	  var data = canvas.toDataURL();
	  if(isCedAdmin) document.getElementById("activity-loading-debug").style.display = 'none';
	  
	   new Ajax.Request(baseUrl+"lodmarketplace/vdebug/captureScreenShot", {
           method: 'Post',
           parameters: {"data":data},
           onComplete: function(response) {
			   if(isCedAdmin) document.getElementById("loading-mask").hide();
			   var imgAttachment = document.getElementById("bugpicture_image");
			   var attachment = document.getElementById("attachment");
			   imgAttachment.src = baseUrl+"var/vdebug/"+response.responseText;
			   attachment.value = response.responseText;
			   attachment.checked = true; 
			   if(document.getElementById("screen_capture"))
				document.getElementById("screen_capture").style.display = 'block';
				if(document.getElementById("activity-loading")) 
					document.getElementById("activity-loading").style.display = 'none';
				if(document.getElementById("activity-loading-debug")) 
					document.getElementById("activity-loading-debug").style.display = 'none';
			   reportVBug();
           }
       });
	 }
	 });
}

function reportVBug(){
	 jQuery( "#report_vbug_form" ).dialog({
			  minWidth: 700
			});
}

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

var bugAttach = 0;
function addMoreScreenShot(){
	
	var attachScreen = document.createElement("INPUT");
	attachScreen.setAttribute("type", "file");
	attachScreen.setAttribute("name", "attachment[]");
	
	var iDiv = document.createElement('div');
	iDiv.id = 'attach_'+bugAttach;
	iDiv.className = 'attach';
	iDiv.appendChild(attachScreen);
	
	var removeAttach = document.createElement("a");
	removeAttach.setAttribute("href", "javascript:;");
	removeAttach.setAttribute("onClick", "removeAttachment('"+iDiv.id+ "');");
	removeAttach.innerHTML = '<i class="fa fa-times fa-fw"></i>';
	iDiv.appendChild(removeAttach);
	
	
	document.getElementById('target_div').appendChild(iDiv);
	
	bugAttach++;
}

function removeAttachment(id){
	var divAttach = document.getElementById(id);
	divAttach.remove();
}

function showVdebugToolbar(){
	jQuery("#vdebug_panel").show("slide", { direction: "right" }, 500);
}

function hideVdebugToolbar(){
	jQuery("#vdebug_panel").hide("slide", { direction: "right" }, 500);
}