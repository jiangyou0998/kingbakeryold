$(function(){
	$('#btnSubmit').click(function(){
		if($('#txtName').val().length == 0){
			alert('�п�J�W�١I');
			return false;
		}
/*
		if($('#txtEmail').val().length == 0){
			alert('�п�J�q�l�I');
			return false;
		}else
*/
		if((!/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/.test($('#txtEmail').val())) && ($('#txtEmail').val().length != 0)){
			alert('�q�l�榡�����T�I');
			return false;
		}

		if($('#txtSort').val().length == 0){
			alert('�п�J�ƧǡI');
			return false;
		}else if(!/^\d+$/.test($('#txtSort').val())){
			alert('�Ƨǥu�ର�Ʀr�I');
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
					// alert('���榨�\�I');
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