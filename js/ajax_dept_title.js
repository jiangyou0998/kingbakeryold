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
function bytitle(op,pid)
{
    var boolstr="";
    if(op.checked)
	{
		  boolstr="add";
	}else{
		  boolstr="del";
	}
   S_xmlhttprequest();
   xmlHttp.open("POST","CRM_get_title_2_do.php?pid="+pid+"&tid="+op.value+"&dowhy="+boolstr,true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');//加这个说明字符编码
   xmlHttp.onreadystatechange = function(){
  	 	if(xmlHttp.readyState==4 && xmlHttp.status==200){//侦测读取状态
    		 addTitle(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null);  //这里是xmlHttp不是XmlHttp注意大小写，调用错误！！
}
function addTitle(txt){}
///部門職位添加
function addDept(temp,op)
{
   S_xmlhttprequest();
   if(temp=="dept"){
   	   xmlHttp.open("GET","CRM_get_dept_2_do.php?dept="+op+"&dowhy=no",true);
   }else{
	   xmlHttp.open("GET","CRM_get_dept_2_do.php?pid="+temp+"&title="+op+"&dowhy=no",true);
   }
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');//加这个说明字符编码
   xmlHttp.onreadystatechange = function(){
  	 	if(xmlHttp.readyState==4 && xmlHttp.status==200){//侦测读取状态
    		 addDT(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null);  //这里是xmlHttp不是XmlHttp注意大小写，调用错误！！
}

//排序刪除操作
function orderBy(w,id,pid,tbl)
{
   S_xmlhttprequest();
   if(tbl=="title"){
	   xmlHttp.open("GET","CRM_get_dept_2_do.php?work="+w+"&id="+id+"&tbl="+tbl+"&pid="+pid,true);
   }else{
	   xmlHttp.open("GET","CRM_get_dept_2_do.php?work="+w+"&id="+id+"&tbl="+tbl,true);
   }
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');
   xmlHttp.onreadystatechange = function(){
  	 	if(xmlHttp.readyState==4 && xmlHttp.status==200){
    		 addDT(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null); 
}
//格式化排序部門
function sort_format()
{
   S_xmlhttprequest();
   xmlHttp.open("GET","CRM_get_dept_2_do.php?sortformat='format'",true);
   xmlHttp.setRequestHeader('context-type','text/html; charset=big5');
   xmlHttp.onreadystatechange = function(){
  	 	if(xmlHttp.readyState==4 && xmlHttp.status==200){
    		 addDT(unescape(xmlHttp.responseText));
   		 }
 	}
   xmlHttp.send(null); 
}

var parent = this;
function addDT(txt)//加了参数来传递
{
	document.getElementById("idd").innerHTML = txt;
}

