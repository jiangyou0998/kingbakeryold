$(function(){
	//��^�D�ɭ����s
	$('#btnBack').click(function(){
		window.location = "cms_customer_workshop_main.php?t="+$('#t').val();
	});
		//��^�D�ɭ����s
	$('#btnAdd').click(function(){
		window.location = "cms_customer_workshop_add.php";
	});
	$('button[name="edit"]').click(function(){
		window.location = "cms_customer_workshop_add.php?&id="+$(this).attr('data-id');
	});
	$('button[name="del"]').click(function(){
		if(!confirm('�T�{�R��?')){
			return false;
		}
		window.location = "cms_customer_workshop_post.php?action=del&id="+$(this).attr('data-id');
	});

	//����ƾ�
	$('#btnSubmit').click(function(){
		if($('#txtName').val().length == 0){
			alert('�п�J�W��');
			return false;
		}
		if($('#txtUnit').val().length == 0){
			alert('�п�J���');
			return false;
		}
		if($('#txtSort').val().length == 0){
			alert('�п�J�Ƨ�');
			return false;
		}else if(!/^\d+$/.test($('#txtSort').val())){
			alert('�Ƨǥu�ର�Ʀr�I');
			return false;
		}
	});
});