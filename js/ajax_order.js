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
///部門分配
function order(op)
{
   S_xmlhttprequest();
   xmlHttp.open("POST","order_do.php?cid="+op,true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');//加这个说明字符编码
   xmlHttp.onreadystatechange = function(){
  	 	if(xmlHttp.readyState==4 && xmlHttp.status==200){//侦测读取状态
    		 returnOrder(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null);  //这里是xmlHttp不是XmlHttp注意大小写，调用错误！！
}
function returnOrder(txt)//加了参数来传递
{
	document.getElementById("staff_name").value = txt;
}

