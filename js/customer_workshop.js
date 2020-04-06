$(function(){
	//返回主界面按鈕
	$('#btnBack').click(function(){
		window.location = "cms_customer_workshop_main.php?t="+$('#t').val();
	});
		//返回主界面按鈕
	$('#btnAdd').click(function(){
		window.location = "cms_customer_workshop_add.php";
	});
	$('button[name="edit"]').click(function(){
		window.location = "cms_customer_workshop_add.php?&id="+$(this).attr('data-id');
	});
	$('button[name="del"]').click(function(){
		if(!confirm('確認刪除?')){
			return false;
		}
		window.location = "cms_customer_workshop_post.php?action=del&id="+$(this).attr('data-id');
	});

	//提交數據
	$('#btnSubmit').click(function(){
		if($('#txtName').val().length == 0){
			alert('請輸入名稱');
			return false;
		}
		if($('#txtUnit').val().length == 0){
			alert('請輸入單位');
			return false;
		}
		if($('#txtSort').val().length == 0){
			alert('請輸入排序');
			return false;
		}else if(!/^\d+$/.test($('#txtSort').val())){
			alert('排序只能為數字！');
			return false;
		}
	});
});