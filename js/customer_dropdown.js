$(function(){
	//��^�D�ɭ����s
	$('#btnBack').click(function(){
		window.location = "cms_customer_dropdown_main.php?t="+$('#t').val();
	});
		//��^�D�ɭ����s
	$('#btnAdd').click(function(){
		window.location = "cms_customer_dropdown_add.php?t="+$(this).attr('data-type');
	});
	$('button[name="edit"]').click(function(){
		window.location = "cms_customer_dropdown_add.php?t="+$('#btnAdd').attr('data-type')+'&id='+$(this).attr('data-id');
	});
	$('button[name="del"]').click(function(){
		window.location = "cms_customer_dropdown_post.php?action=del&id="+$(this).attr('data-id')+'&t='+$('#btnAdd').attr('data-type');
	});

	//����ƾ�
	$('#btnSubmit').click(function(){
		if($('#txtName').val().length == 0){
			alert('�п�J�W��');
			return false;
		}
		if($('#txtSort').val().length == 0){
			alert('�п�J�Ƨ�');
			return false;
		}else if(!/^\d+$/.test($('#txtSort').val())){
			alert('�Ƨǥu�ର�Ʀr�I');
			return false;
		}
	
		$("#formAdd").ajaxSubmit({
            type: "post",
            url: "cms_customer_dropdown_post.php",
			dataType:'json',
            success: function (data) {
				if(data.result){
					alert('���榨�\�I');
					window.location = 'cms_customer_dropdown_main.php?t='+data.t;
				}else{
					alert(data.error);
				}
			}
		});
	});
});