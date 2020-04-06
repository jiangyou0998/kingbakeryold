// JavaScript Document
var xmlHttp; 
function S_xmlhttprequest()
{
    if(window.ActiveXObject)
     {
        xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
     }
     else if(window.XMLHttpRequest)
     {
        xmlHttp = new XMLHttpRequest();
     }
}
///添加分組
function bygroup(name)
{
   S_xmlhttprequest();
   xmlHttp.open("get","CRM_func_show_do.php?groupName="+name,true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');
   xmlHttp.onreadystatechange = function(){
  	 if(xmlHttp.readyState==4 && xmlHttp.status==200){
    		 returnText(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null);  
}
function returnText(txt){
	//document.getElementById("idd").innerHTML=txt;
	if(txt.substr(0,1)==1){
		document.getElementById("errorGroup").innerHTML=txt.substr(1,txt.length);
	}else{
		document.location.reload();
	}
}
///添加權限
function byFunc(name,url,qh,desc)
{
   S_xmlhttprequest();
   xmlHttp.open("get","CRM_func_show_do.php?action=add&funcName="+name+"&funcUrl="+url+"&funcqh="+qh+"&funcDesc="+desc,true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');
   xmlHttp.onreadystatechange = function(){
  	 if(xmlHttp.readyState==4 && xmlHttp.status==200){
    		 returnFunc(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null);  
}
function returnFunc(txt){
	if(txt.substr(0,1)==1){
		document.getElementById("errorGroup").innerHTML=txt.substr(1,txt.length);
	}else if(txt.substr(0,1)==2){
		document.getElementById("errorFunc").innerHTML=txt.substr(1,txt.length);
	}else {
		document.getElementById("idd").innerHTML=txt;
	}
}
///狀態修改
var  fid=0;
function bystate(id,s)
{
	fid=id;
   S_xmlhttprequest();
   xmlHttp.open("POST","CRM_func_show_do.php?stateId="+id+"&int_state="+s,true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');
   xmlHttp.onreadystatechange = function(){
  	 if(xmlHttp.readyState==4 && xmlHttp.status==200){
    		// unescape(xmlHttp.responseText);
   		 }
 	}
   xmlHttp.send(null);  
   return s;
}
///修改權限
function byUpdate(name,mapp,id,desc)
{
   S_xmlhttprequest();
   xmlHttp.open("get","CRM_func_show_do.php?action=update&funcName="+name+"&funcUrl="+mapp+"&funcId="+id+"&funcDesc="+desc,true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');
   xmlHttp.onreadystatechange = function(){
  	 if(xmlHttp.readyState==4 && xmlHttp.status==200){
    		 returnUpdate(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null);  
}
function returnUpdate(txt){
	document.getElementById("idd").innerHTML=txt;
}
///刪除權限
function bydelete(id)
{
   S_xmlhttprequest();
   xmlHttp.open("POST","CRM_func_show_do.php?action=delete&funcId="+id,true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');
   xmlHttp.onreadystatechange = function(){
  	 if(xmlHttp.readyState==4 && xmlHttp.status==200){
    		 returnDelete(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null);  
}
function returnDelete(txt){
	document.getElementById("idd").innerHTML=txt;
}