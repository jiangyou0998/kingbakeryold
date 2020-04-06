$(function(){
	//返回主界面按鈕
	$('#btnBack').click(function(){
		window.location = "cms_customer_dropdown_main.php?t="+$('#t').val();
	});
		//返回主界面按鈕
	$('#btnAdd').click(function(){
		window.location = "cms_customer_dropdown_add.php?t="+$(this).attr('data-type');
	});
	$('button[name="edit"]').click(function(){
		window.location = "cms_customer_dropdown_add.php?t="+$('#btnAdd').attr('data-type')+'&id='+$(this).attr('data-id');
	});
	$('button[name="del"]').click(function(){
		window.location = "cms_customer_dropdown_post.php?action=del&id="+$(this).attr('data-id')+'&t='+$('#btnAdd').attr('data-type');
	});

	//提交數據
	$('#btnSubmit').click(function(){
		if($('#txtName').val().length == 0){
			alert('請輸入名稱');
			return false;
		}
		if($('#txtSort').val().length == 0){
			alert('請輸入排序');
			return false;
		}else if(!/^\d+$/.test($('#txtSort').val())){
			alert('排序只能為數字！');
			return false;
		}
	
		$("#formAdd").ajaxSubmit({
            type: "post",
            url: "cms_customer_dropdown_post.php",
			dataType:'json',
            success: function (data) {
				if(data.result){
					alert('提交成功！');
					window.location = 'cms_customer_dropdown_main.php?t='+data.t;
				}else{
					alert(data.error);
				}
			}
		});
	});
});