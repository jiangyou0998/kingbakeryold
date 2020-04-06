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
///可視
function Change_main(op)
{
	var temp = op.substring(op.indexOf('.')+1,op.length);
	if(temp==1){
	    document.getElementById("other").style.display = "none";
		document.getElementById("other6").style.display = "block";
	 }else{	 
	 	document.getElementById("other").style.display = "block";
		document.getElementById("other6").style.display = "none";
	 }
}
function addTitle(txt){
	document.getElementById("tbl_boss").innerHTML=txt;
}