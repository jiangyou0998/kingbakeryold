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
///获取上级
function Change_boos()
{
   var uid = document.getElementById("int_user").value;
   if(uid==""){uid=0;}
   S_xmlhttprequest();
   xmlHttp.open("POST","leave_get_boss.php?uid="+uid,true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');//加这个说明字符编码
   xmlHttp.onreadystatechange = function(){
  	 	if(xmlHttp.readyState==4 && xmlHttp.status==200){//侦测读取状态
    		 addTitle(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null);  //这里是xmlHttp不是XmlHttp注意大小写，调用错误！！
}
function addTitle(txt){
	document.getElementById("tbl_boss").innerHTML=txt;
}