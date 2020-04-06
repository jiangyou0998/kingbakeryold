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
var i=0;
function bytitle(id,op)
{
	i=op;
	if(document.getElementById("disDiv"+i)&&i!=""){
		if(id==2)
		{
			document.getElementById("disDiv"+i).style.display="block";
		}else{
			document.getElementById("disDiv"+i).style.display="none";
		}
	}
   var dis="";
   if(document.getElementById("district"+i)&&i>0){dis = document.getElementById("district"+i).value;}

   S_xmlhttprequest();
   xmlHttp.open("POST","CRM_get_title.php?dis="+dis+"&pid="+id+"&i="+i,true);
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
     document.getElementById('Title'+i).innerHTML = txt;
}
function returnFunc(txt)///加了参数来传递
{
    // alert(txt);
}