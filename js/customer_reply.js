$(function(){

	$('#btnUpload').click(function(){
		$('#upload').click();
	});
	$('div').on('change','#upload',function(){
		$('#btnUpload').removeClass('btn-warning');
		$('#btnUpload').addClass('btn-success');
		$('#btnClear').show();
	});
	$('#btnClear').click(function(){
		$('#btnUpload').removeClass('btn-success');
		$('#btnUpload').addClass('btn-warning');
		$('#btnClear').hide();
		var file = $('#upload');
		file.after(file.clone().val(""));
		file.remove();
	});
	
	$('#btnSubmit').click(function(){
		if($('#Cause').val().length == 0){
			alert('請輸入原因！');
			return false;
		}
		if($('#oldUpload').val().length == 0 &&$('#upload').val().length == 0){
			alert('請選擇上載文件！');
			return false;
		}
		
		if($('#RealTime').val() == '--'&&$('#textRealTime').val().length == 0){
			alert('請輸入或者選擇即時處理！');
			return false;
		}
		if($('#measures').val().length == 0){
			alert('請輸入糾正及預防措施！');
			return false;
		}
		if($('#reply').val().length == 0){
			alert('請輸入當事人/分店經理！');
			return false;
		}
		$("#formAdd").ajaxSubmit({
            type: "post",
            url: "cms_customer_reply_post.php",
			dataType:'json',
            success: function (data) {
				if(data.result){
					alert('提交成功！');
					window.location = 'cms_customer_main.php';
				}else{
					alert(data.error);
				}
			}
		});
	});
	
	$('#btnBack').click(function(){
		window.location = 'cms_customer_main.php';
	});
});