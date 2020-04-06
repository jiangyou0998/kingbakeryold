$(function(){
	// $('.line').bind('click',function(){
		// var strHtml = '';
		// strHtml +='<div class="change-line">';
		// strHtml +='	<ul id="lv1">';
		// strHtml +='		<li>太興</li>';
		// strHtml +='		<li>靠得住</li>';
		// strHtml +='		<li>匯麵</li>';
		// strHtml +='		<li>宮崎</li>';
		// strHtml +='		<li>點心瑪莉</li>';
		// strHtml +='	</ul>';
		// strHtml +='	<ul id="lv2">';
		// strHtml +='		<li>太興</li>';
		// strHtml +='		<li>靠得住</li>';
		// strHtml +='		<li>匯麵</li>';
		// strHtml +='		<li>宮崎</li>';
		// strHtml +='		<li>點心瑪莉</li>';
		// strHtml +='	</ul>';
		// strHtml +='</div>';
		// layer.open({
		  // type: 1,
		  // skin: 'layui-layer-rim', 
		  // area: ['420px', '240px'], 
		  // content: strHtml
		// }); 
		
	// });
	
	$('#lv1 li').bind('click',function(){
		var lineID = $(this).attr('data-id');
		if($(this).is('.checked')){
			$(this).removeClass('checked');
			//alert('___');
			$('#lv2').find('li').each(function(){
				if($(this).attr('data-fid')==lineID){
					$(this).remove();
				}
			});
		}else{
			$(this).addClass('checked');
			$.ajax({
				url:'crm_notice_2.php',
				type:'post',
				data:{action:'getLine',lineID:lineID},
				dataType:'json',
				success:function(data){
					if(data.result){
						$('#lv2').empty();
						for(var i=0;i<data.data.length;i++){
							$('#lv2').append('<li data-id="'+data.data[i].id+'" data-fid="'+lineID+'" class="checked">'+data.data[i].name+'</li>');
						}
					}
				},
				error:function(){
					alert('系統錯誤，請重新嘗試！');
				}
			});
		}
	});
	$('#lv2').on('click','li',function(){
		if($(this).is('.checked')){
			$(this).removeClass('checked');
			//alert('2');
		}else{
			$(this).addClass('checked');
		}
	});
	
	$('#btnSubmit').bind('click',function(){
		var strLineID = '';
		$('.line ul li').each(function(){
			if($(this).is('.checked')){
				//alert($(this).attr('data-id'));
				strLineID+=$(this).attr('data-id')+',';
			}
		});
		if(strLineID.length>0){
			$('#lineID').val(strLineID);
		}else{
			alert('請選擇可視分線');
			return false;
		}
	});
	
	loading();
});
function loading(){
	var lineID;
	if($('#lineID').val().length>0){
		lineID = $('#lineID').val().split(',');
		$('.line ul li').each(function(){
			var i=0;
			var data = $(this);
			for(i=0;i<lineID.length;i++){
				if(lineID[i]==data.attr('data-id')){
					data.click();
					break;
				}
			}
		});
	}
}