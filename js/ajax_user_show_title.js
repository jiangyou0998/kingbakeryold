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
//下拉列表無刷新
function bytitle(id)
{
   S_xmlhttprequest();
   xmlHttp.open("POST","CRM_get_title_add.php?pid="+id,true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');//加这个说明字符编码
   xmlHttp.onreadystatechange = function(){
  	 	if(xmlHttp.readyState==4 && xmlHttp.status==200){//侦测读取状态
    		 getTitle(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null);  //这里是xmlHttp不是XmlHttp注意大小写，调用错误！！
}
function getTitle(txt)///加了参数来传递
{
     document.getElementById('Title').innerHTML = txt;
}
