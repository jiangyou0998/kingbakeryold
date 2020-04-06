$(function(){
	$('#btnSubmit').click(function(){
		if($('#txtName').val().length == 0){
			alert('請輸入名稱！');
			return false;
		}
/*
		if($('#txtEmail').val().length == 0){
			alert('請輸入電郵！');
			return false;
		}else
*/
		if((!/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/.test($('#txtEmail').val())) && ($('#txtEmail').val().length != 0)){
			alert('電郵格式不正確！');
			return false;
		}

		if($('#txtSort').val().length == 0){
			alert('請輸入排序！');
			return false;
		}else if(!/^\d+$/.test($('#txtSort').val())){
			alert('排序只能為數字！');
			return false;
		}
		// $("#formAdd").ajaxSubmit({
            // type: "post",
            // url: "cms_repairproject_foreigncontractor_post.php",
			// dataType:'text',
			// contentType: "application/x-www-form-urlencoded; charset=utf-8",
            // success: function (data) {
				// data = jQuery.parseJSON(data);
				// if(data.result){
					// alert('提交成功！');
					// window.location = 'cms_repairproject_foreigncontractor_main.php';
				// }else{
					// alert(data.error);
				// }
			// }
		// });
	});
	$('#btnBack').click(function(){
		window.location = 'cms_repairproject_foreigncontractor_main.php';
	});
});