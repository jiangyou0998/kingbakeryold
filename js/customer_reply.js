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
			alert('�п�J��]�I');
			return false;
		}
		if($('#oldUpload').val().length == 0 &&$('#upload').val().length == 0){
			alert('�п�ܤW�����I');
			return false;
		}
		
		if($('#RealTime').val() == '--'&&$('#textRealTime').val().length == 0){
			alert('�п�J�Ϊ̿�ܧY�ɳB�z�I');
			return false;
		}
		if($('#measures').val().length == 0){
			alert('�п�J�ȥ��ιw�����I�I');
			return false;
		}
		if($('#reply').val().length == 0){
			alert('�п�J��ƤH/�����g�z�I');
			return false;
		}
		$("#formAdd").ajaxSubmit({
            type: "post",
            url: "cms_customer_reply_post.php",
			dataType:'json',
            success: function (data) {
				if(data.result){
					alert('���榨�\�I');
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