$(function(){
	$('#btnBack').click(function(){
		window.location = 'cms_volunteer_option_main.php';
	});
	$('#btnSubmit').click(function(){
		if($('#txtName').val().length ==0){
			alert('�п�J�W�١I');
			return false;
		}
		if($('#txtSort').val().length==0){
			alert('�п�J�ƧǡI');
			return false;
		}else if(!/^[0-9]*$/.test($('#txtSort').val())){
			alert('�Ƨǥu�ର�Ʀr�I');
			return false;
		}
	});
});