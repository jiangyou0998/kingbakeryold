$(function(){
	$('.btnEdit').bind('click',function(){
		window.location="cms_dis_manage_oper.php?id="+$(this).attr('data-id');
	});
	$('.btnDel').bind('click',function(){
		window.location="cms_dis_manage_main.php?action=del&id="+$(this).attr('data-id');
	});
	
	$('.col-sm-2 :button').bind('click',function(){
		var file = $(this).parent().find(':file') ;
		if($(this).text()=='取消選擇'){
			//onchange="preImg(this.id,'imgPre1');"
			file.replaceWith('<input type="file" name="'+file.attr('name')+'" id="'+file.attr('name')+'"  style="display:none;" />');  
			$(this).parent().find(':button').text('選擇圖片');
			$(this).parent().find(':button').addClass('btn-default');
			$(this).parent().find(':button').removeClass('btn-warning');
		}else{
			file.click();
		}
		
	});
	
	$('.col-sm-2').on('change',':file',function(){
	//	alert($(this).attr('data-img'))
		preImg($(this).attr('id'),$(this).attr('data-img'));
		$(this).parent().find(':button').addClass('btn-warning');
		$(this).parent().find(':button').removeClass('btn-default');
		$(this).parent().find(':button').text('取消選擇');
	});
	$('#btnBack').bind('click',function(){
		window.location = "phonebook_1.php";
	});
	$('.user-list-left,.user-list-right').on('click','li',function(){
		if($(this).is('.checked')){
			$(this).removeClass('checked');
		}else{
			$(this).addClass('checked');
		}
	});
	$('#btnUserAdd').click(function(){
		var strAddHtml = '';
		$('.user-list-left ul li.checked').each(function(){
			strAddHtml+='<li data-id="'+$(this).attr('data-id')+'">'+$(this).html()+'</li>';
			$(this).remove();
		});
		$('.user-list-right ul').append(strAddHtml);
	});
	$('#btnUserRemove').click(function(){
		console.log('2');
		var strAddHtml = '';
		$('.user-list-right ul li.checked').each(function(){
			$("#cbx-li-" + $(this).attr('data-id')).remove();
			strAddHtml+='<li data-id="'+$(this).attr('data-id')+'">'+$(this).html()+'</li>';
			$(this).remove();
		});
		$('.user-list-left ul').append(strAddHtml);
	});
	
});
/** 
* 獲取本地圖片
*/ 
function getFileUrl(sourceId) { 
	var url; 
	if (navigator.userAgent.indexOf("MSIE")>=1) { // IE 
		url = document.getElementById(sourceId).value; 
	} else if(navigator.userAgent.indexOf("Firefox")>0) { // Firefox 
		url = window.URL.createObjectURL(document.getElementById(sourceId).files.item(0)); 
	} else if(navigator.userAgent.indexOf("Chrome")>0) { // Chrome 
		url = window.URL.createObjectURL(document.getElementById(sourceId).files.item(0));
	}else{
		url = window.URL.createObjectURL(document.getElementById(sourceId).files.item(0));
	} 
		
	return url; 
} 

/** 
* 顯示本地圖片
*/ 
function preImg(sourceId, targetId) { 
var url = getFileUrl(sourceId); 
var imgPre = document.getElementById(targetId); 
imgPre.src = url; 
}