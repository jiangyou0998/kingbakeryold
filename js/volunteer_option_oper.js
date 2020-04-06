$(function(){
	$('#btnBack').click(function(){
		window.location = 'cms_volunteer_option_main.php';
	});
	$('#btnSubmit').click(function(){
		if($('#txtName').val().length ==0){
			alert('請輸入名稱！');
			return false;
		}
		if($('#txtSort').val().length==0){
			alert('請輸入排序！');
			return false;
		}else if(!/^[0-9]*$/.test($('#txtSort').val())){
			alert('排序只能為數字！');
			return false;
		}
	});
});