var csloadstatustext="<span style='padding: 10px;'><img src='/design/images/processing.gif' /> Requesting content...</span>"; //HTML to indicate Ajax page is being fetched
var showStatusText=true;
var loadinginprogress=false;
var lastRequest = (new Date()).getTime();


function submitAjaxRequest() {   
	_form = arguments[0];
	if(arguments.length > 1)
		_names = arguments[1];
	
	displayAjaxStatus();
	
	var _elements = _form.elements;
	var _url = _form._formAction.value;
	var _params ='';
	for (var i=0;i<_elements.length;i++) {
		var name = _form.elements[i].name;
		var value = encodeURIComponent(_form.elements[i].value);
		_params += ((_params=='')?'':'&') + name + '=' + value;
	  }
	var page_request = findPageRequest();
	
	page_request.onreadystatechange=function(){
		refresh(page_request, _names);
	}
	page_request.open('POST', _url, true);
    page_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    page_request.setRequestHeader("Content-length", _params.length);
    page_request.setRequestHeader("Connection", "close");	
	page_request.send(_params);
}

function refresh(page_request, _names){
		if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1)){
			loadAjaxPage(_names);
		}
}

function displayAjaxStatus(){
	var _status = getElementsByName("ajax-status");
	for(var i=0; i<_status.length; i++){
		var item = _status[i];
		item.style.display="inline";
	}
}

function loadAjaxPage(_names) {

	displayAjaxStatus();

	var array = _names.split(",");
	for (var n = 0; n < array.length; n++){
		var _name = array[n];
		var items = getElementsByName(_name);
		for(var i=0; i<items.length; i++){
			var item = items[i];
			if (typeof item.getAttribute("rel")=="string") {
				ajaxpage(item.getAttribute("rel"), item);
			}
		}
	}
}

function findPageRequest(){
        var page_request = false;
        try {return new ActiveXObject('Msxml2.XMLHTTP');} catch(e) {};
        try {return new ActiveXObject('Microsoft.XMLHTTP');} catch(e) {};
        try {page_request = new XMLHttpRequest();
             page_request.overrideMimeType('text/html');
        	return page_request ;
        } catch(e) {};
        return false;
}

function ajaxpage(_url, thediv){
	var page_request = findPageRequest();
	
	
	
	if(showStatusText)
		thediv.innerHTML=csloadstatustext
		
	page_request.onreadystatechange=function(){
		loadpage(page_request, thediv)
	}

	var d = new Date();
	var time = d.getTime();
	
	page_request.open('GET', _url+'&___time='+time, true);
	page_request.send(null);
}

	
function loadpage(page_request, thediv){
	if(page_request.status==200 || window.location.href.indexOf("http")==-1){			
		if (page_request.readyState == 4 ){
			thediv.innerHTML=page_request.responseText
			var _status = getElementsByName("ajax-status");
			for(var i=0; i<_status.length; i++){
				var item = _status[i];
				item.style.display="none";
			}
	
			if(thediv.innerHTML.match("request-password")){
				TB_show('', '/fbw-app/pub/login/EnterPasswordPanel.faces?height=175&width=250&modal=true', false);
			}
			
			//START for popup notice only, should be reconsidered later.
			if(thediv.getAttribute("name") == '_popupnotice'){
				var _lang = thediv.getAttribute("lang");
				TB_show('', "/fbw-app/design/fbw/"+_lang+"/popupmsg.html?height=375&width=380", false);
			}
			//END for popup notice only
		}
	}
}


//for reference. By Edmund 20070420
getElementsByName=function (name) {
              var returns = document.getElementsByName(name);
              if(returns.length > 0) return returns;
              returns = new Array();
              var e = document.getElementsByTagName('div');
              for(i = 0; i < e.length; i++) {
                            if(e[i].getAttribute("name") == name) {
                                          returns[returns.length] = e[i];
                            }
              }
              return returns;
}


function loadAjaxContent(_url) {
	var result='';
	var d = new Date();
	var time = d.getTime();
	var _t = time - lastRequest ;
	if(!loadinginprogress ||  _t > 2000){
		lastRequest = time;
		loadinginprogress = true;
		var page_request = findPageRequest();
	
		page_request.open('GET', _url+'&___time='+time, false);
		page_request.send(null);
		result = page_request.responseText;
		loadinginprogress = false;
	}
	return result;
}

function displayImg(id) {
	var x = document.getElementById(id);
	x.style.visibility='visible';
}

function hiddenImg(id) {
	var x = document.getElementById(id);
	x.style.visibility='hidden';
}


var idlist = new Array('tab4','tab4off','tab43L','tab43R','tab43off','tab32L','tab32R','tab32off','tab21L','tab21R','tab21off','tab1','tab1off','tab1focus', 'tab2focus', 'tab3focus', 'tab4focus', 'tab1ready','tab2ready','tab3ready','tab4ready','content1','content2','content3','content3_1','content4','content5');

function ManageTabPanelDisplay() {
//
// Between the parenthesis, list the id's of the div's that 
//     will be effected when tabs are clicked. List in any 
//     order. Put the id's in single quotes (apostrophes) 
//     and separate them with a comma all one line.
//

// No other customizations are necessary.

	if(arguments.length < 1) { return; }
	
	for(var i = 0; i < idlist.length; i++) {
	   var block = false;
	   for(var ii = 0; ii < arguments.length; ii++) {
	      if(idlist[i] == arguments[ii]) {
	         block = true;
	         break;
	         }
	      }
	
		var item = document.getElementById(idlist[i]);
		
		try {
		   if(block) { 
				 	item.style.display = "block"; 
				if (typeof item.getAttribute("rel")=="string") {
					ajaxpage(item.getAttribute("rel"), item);
				}
		   }
		   else { item.style.display = "none"; }
	   }catch(err){}
	}
}

function handleMouseOver(img){
	img.src=img.getAttribute("mouseOverSrc");
}

function handleMouseOut(img){
	img.src=img.getAttribute("mouseOutSrc");
}


function popupPanel(item){
	var result = "";
	item_media_url=item.getAttribute("item_media_url");
	item_name=item.getAttribute("item_name");
	item_brand=item.getAttribute("item_brand");
	item_spec=item.getAttribute("item_spec");
	item_color=item.getAttribute("item_color");
	txt_datil_1=item.getAttribute("txt_datil_1");
	txt_datil_2=item.getAttribute("txt_datil_2");
	txt_datil_3=item.getAttribute("txt_datil_3");
	chr_no=item.getAttribute("chr_no");
	var _mediaHTML= "";
	
	if(item_media_url != null && item_media_url.length > 0){
		if(item_media_url.indexOf('.swf') == -1  ){
			_mediaHTML="<img height='100px' width='100px' src='"+item_media_url+"' style='border: 0px solid #999999; margin:0px 0px 0px 0px;'/>";
		}else{
			_mediaHTML += '<object ';
			_mediaHTML += 'classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
			_mediaHTML += 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" '; 
			_mediaHTML += 'width="69" height="69" ';
			_mediaHTML += 'style="display: block; z-index: -100;border: 1px solid #999999;"> ';
			_mediaHTML += '<param name="movie" value="'+item_media_url+'" /> ';
			_mediaHTML += '<param name="quality" value="high" /> '; 
			_mediaHTML += '<param name="bgcolor" value="#FFFFFF" /> ';
			_mediaHTML += '<param name="wmode" value="transparent"/> ';
			_mediaHTML += '<embed src="'+item_media_url+'" quality="high" bgcolor="#FFFFFF" ';
			_mediaHTML += 'type="application/x-shockwave-flash" wmode="transparent" ';
			_mediaHTML += 'width="69" height="69"';
			_mediaHTML += 'name="messageBox" ';
			_mediaHTML += 'PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">  ';
			_mediaHTML += '</embed> ';
			_mediaHTML += '</object>';
		}
	}
	var result = document.getElementById('popupPanel').innerHTML;
	result = result.replace('p{popup_name}',item_name);
	result = result.replace('p{popup_imageUrl}',_mediaHTML);
	result = result.replace('p{popup_brand}',item_brand);
	result = result.replace('p{popup_spec}',item_spec);
	result = result.replace('p{popup_color}',item_color);
	result = result.replace('p{txt_datil_1}',txt_datil_1);
	result = result.replace('p{txt_datil_2}',txt_datil_2);
	result = result.replace('p{txt_datil_3}',txt_datil_3);
	result = result.replace('p{chr_no}',chr_no);
	//result = {popup_temp:"XXXXXX"}
	return result;
}

function popupPanel2(item){
	var result = "";
	item_img_url = item.getAttribute("item_img_url");
	item_name = item.getAttribute("item_name");
	item_detail_1 = item.getAttribute("item_detail_1");
	item_detail_2 = item.getAttribute("item_detail_2");
	item_detail_3 = item.getAttribute("item_detail_3");	
	chr_no=item.getAttribute("chr_no");

	var _mediaHTML= "";
	if(item_img_url != null && item_img_url.length > 0){
		if(item_img_url.indexOf('.swf') == -1  ){
			_mediaHTML="<img height='100px' width='100px' src='"+item_img_url+"' style='border: 0px solid #999999; margin:0px 0px 0px 0px;'/>";
		}else{
			_mediaHTML += '<object ';
			_mediaHTML += 'classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
			_mediaHTML += 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" '; 
			_mediaHTML += 'width="69" height="69" ';
			_mediaHTML += 'style="display: block; z-index: -100;border: 1px solid #999999;"> ';
			_mediaHTML += '<param name="movie" value="'+item_img_url+'" /> ';
			_mediaHTML += '<param name="quality" value="high" /> '; 
			_mediaHTML += '<param name="bgcolor" value="#FFFFFF" /> ';
			_mediaHTML += '<param name="wmode" value="transparent"/> ';
			_mediaHTML += '<embed src="'+item_img_url+'" quality="high" bgcolor="#FFFFFF" ';
			_mediaHTML += 'type="application/x-shockwave-flash" wmode="transparent" ';
			_mediaHTML += 'width="69" height="69"';
			_mediaHTML += 'name="messageBox" ';
			_mediaHTML += 'PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">  ';
			_mediaHTML += '</embed> ';
			_mediaHTML += '</object>';
		}
	}
	var result = document.getElementById('popupPanel2').innerHTML;
	result = result.replace('p{popup_name}',item_name);
	result = result.replace('p{popup_img_url}',_mediaHTML);
	result = result.replace('p{popup_detail_1}',item_detail_1);
	result = result.replace('p{popup_detail_2}',item_detail_2);
	result = result.replace('p{popup_detail_3}',item_detail_3);
	result = result.replace('p{chr_no}',chr_no);
	return result;
}


function togglePanelDisplay(_name) {
	var items = document.getElementsByName(_name);
 	for(var i=0; i<items.length; i++){
		var item = items[i];
		var status = item.style.display;

			if(status == "block") {
			 	item.style.display = "none";
			 } else {
			 	item.style.display = "block";
			 }
	}
 
}



/*
   Adds an html hidden input field (dynamically created) to the given HTML form element.
   @param formElement the given HTML form element
   @param fieldName the name of the hidden input field
   @param fieldValue the (string) value of the hidden input field
*/
function addHiddenInputField(formElement, fieldName, fieldValue) {
   var inputElement = document.createElement("input")
   inputElement.setAttributeNode(createHtmlAttribute("type", "hidden"))
   inputElement.setAttributeNode(createHtmlAttribute("name", fieldName))
   inputElement.setAttributeNode(createHtmlAttribute("value", fieldValue))
   formElement.appendChild(inputElement)
   return
}
/*
   Creates an html attribute.
   @param name the name of the attribute.
   @param value the (string) value of the attribute.
   @return the newly created html attribute
*/
function createHtmlAttribute(name, value) {
   var attribute = document.createAttribute(name)
   attribute.nodeValue = value
   return attribute
}



function highlight(_name, _state) {
	var	item = document.getElementById(_name);
	if(item != undefined)
		item.style.display = _state;	
}

function onclickAdd(_form, _requestableId) {
	highlight('_slidingProduct'+_requestableId, 'none');
	slideToBasket('slidingProduct'+_requestableId);
	submitAjaxRequest(_form, '_shoppingCart,_restCampaginPanel,_campaginPanel');
}

/*mouse over and highlight*/
function m_over_h(_item, _requestableId) {
	highlight('_slidingProduct'+_requestableId, 'inline');
	handleMouseOver(_item);
}

/*mouse out and highlight*/
function m_out_h(_item, _requestableId) {
	highlight('_slidingProduct'+_requestableId, 'none');
	handleMouseOut(_item);
}


function addOnDemand(_form) {
//	highlight('_slidingProduct'+_requestableId, 'none');
//	slideToBasket('slidingProduct'+_requestableId);
	//submitAjaxRequest(_form, '_shoppingCart');

	var d = new Date();
	var _time = d.getTime();

	var e=document.createElement("script");
	e.setAttribute("type","text/javascript");
	
	var _param = 'refreshDiv='+_form.refreshDiv.value;
	_param += '&requestableId='+_form.requestableId.value;
	_param += '&requestableClass='+_form.requestableClass.value;
	_param += '&_action='+_form._action.value;
	_param += '&_showButton=true';
	_param += '&___time='+_time;
	
	
	e.src="http://api.fbw.hk/fbw-app/api/ShoppingCart.faces?"+_param;
	
	document.body.appendChild(e);
	
}

function loadOnDemand(_name, _restId, _menuId, _categoryId){
	var d = new Date();
	var _time = d.getTime();

	var e=document.createElement("script");
	e.setAttribute("type","text/javascript");
	
	var _param = '';
	_param += 'refreshDiv='+_name;
	_param += '&restId='+_restId;
	_param += '&menuId='+_menuId;
	_param += '&categoryId='+_categoryId;
	_param += '&___time='+_time;
	
	e.src="http://api.fbw.hk/fbw-app/api/MenuPanel.faces?"+_param;
	
	document.body.appendChild(e);
}

function actionOnDemand(form){
	var d = new Date();
	var _time = d.getTime();

	var e=document.createElement("script");
	e.setAttribute("type","text/javascript");
	
	var _param = '';

	if(form.refreshDiv)	
		_param += 'refreshDiv='+form.refreshDiv.value;
	if(form._action)	
		_param += '&_action='+form._action.value;
	if(form.requestableClass)	
		_param += '&requestableClass='+form.requestableClass.value;
	if(form.requestableId)	
		_param += '&requestableId='+form.requestableId.value;
	if(form.timestamp)	
		_param += '&timestamp='+form.timestamp.value;
	if(form._showButton)	
		_param += '&_showButton='+form._showButton.value;
	if(_time)	
		_param += '&___time='+_time;
	
	e.src="http://api.fbw.hk/fbw-app/api/ShoppingCart.faces?"+_param;
	
	document.body.appendChild(e);
}


function loadFoodMenu(_name) {
	var _url = "http://api.fbw.hk/fbw-app/api/MenuPanel.faces?";
	_url += "&refreshDiv="+_name;
	_url += "&restId="+restId;
	_url += "&lang="+lang;

	UDS_loadScript(_url);
}

function loadShoppingCart(_name) {
	var _url = "http://api.fbw.hk/fbw-app/api/ShoppingCart.faces?";
	_url += "&refreshDiv="+_name;
	_url += "&_showButton=true";
	_url += "&lang="+lang;
	
	UDS_loadScript(_url);	
}

function UDS_loadScript(url) { 
	document.write('<script src="' + url + '" type="text/javascript"></script>');
}


function submitRequest(form) {
	if(apiMode){
		actionOnDemand(form);
	}else{
		submitAjaxRequest(form, form.refreshDiv.value);
	}
}


function refreshDiv(_names, _content) {

	var array = _names.split(",");
	for (var n = 0; n < array.length; n++){
		var _name = array[n];
		var items = getElementsByName(_name);
		for(var i=0; i<items.length; i++){
			var item = items[i];
			item.innerHTML=_content;
		}
	}
}

function checkout(form) {
	if(apiMode){
		_api_checkout(form);
	}else{
		_checkout(form);
	}
}

function _checkout(form) {
	window.location=form._link.value;
}

function _api_checkout() {
	var d = new Date();
	var _time = d.getTime();

	var e=document.createElement("script");
	e.setAttribute("type","text/javascript");
	
	var _param = '';
	_param += 'refreshDiv=_menuPanel';

	_param += '&___time='+_time;
	
	e.src="http://api.fbw.hk/fbw-app/api/OrderConfirmation.faces?"+_param;
	
	document.body.appendChild(e);
}

function _api_thankyou() {
	var d = new Date();
	var _time = d.getTime();

	var e=document.createElement("script");
	e.setAttribute("type","text/javascript");
	
	var _param = '';
	_param += 'refreshDiv=_menuPanel';

	_param += '&___time='+_time;
	
	e.src="http://api.fbw.hk/fbw-app/api/ThankYouPage.faces?"+_param;
	
	document.body.appendChild(e);
}

function submitSearch(_form){
	var _param = '';
	_param += _form.locationId.value != '' ? '&locationId='+_form.locationId.value : '' ;
	_param += _form.deliveryAreaId.value != '' ? '&deliveryAreaId='+_form.deliveryAreaId.value : '' ;
	_param += _form.query.value != '' ? '&query='+_form.query.value : '' ;
	
	window.location=_form.action + "?" + _param;
}


function disableOrderButton() {
	var x = document.getElementById('activeButton');
	x.style.display='none';
	var y = document.getElementById('passiveButton');
	y.style.display='inline';
}

function sendme() 
{ 
   	window.open("","payDWin","width=649,height=857,toolbar=0"); 
    var a = window.setTimeout("document.getElementById('payDForm').submit();",500); 
} 