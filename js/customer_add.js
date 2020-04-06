$(function(){
	//��^�D�ɭ����s
	$('#btnBack').click(function(){
		window.location = "cms_customer_main.php";
	});
	
	$('#shop').change(function(){
		var id = $(this).val();
		if(id == '--'){
			$('#area').val('');
			$('#line').val('');
			return false;
		}
		$.ajax({
			url:'cms_customer_add_post.php',
			type:'post',
			dataType:'json',
			data:{action:'getArea',id:id},
			success:function(data){
				if(data.result){
					$('#area').val(data.areaname);
					$('#line').val(data.line);
				}else{
					alert('�����ƾڡI');
				}
			},
			error:function(){
				alert('�������~�I');
			}
		});
	});
	
	$('#CustomerDate,#IncidentDate').click(function(){
		WdatePicker();
	});
	
	//����ƾ�
	$('#btnSubmit').click(function(){
		if(!confirm('�T�{���檺�ƾڬO�_�������T�A�����N����ק�I')){
			return false;
		}
		var source = $('#source').val();
		var shop = $('#shop').val();
		var dept = $('#dept').val();
		var staff = $('#staff').val();
		var name = $('#CustomerName').val();
		var customerDate = $('#CustomerDate').val();
		var incidentDate = $('#IncidentDate').val();
		var complaints = $('#Complaints').val();
		var content = $('#content').val();
		var upload = $('#upload').val();
		var line = $('#line').val();
		var title = $('#title').val();
		var oldUpload = $('#oldUpload').val();
		if(source == '--'){
			alert('�п�ܨӷ��I');
			return false;
		}
		if(shop == '--'){
			alert('�п�ܧ�D�����I');
			return false;
		}
		if(dept == '--' && staff.length == 0){
			alert('�п�ܳ����ο�J���u�I');
			return false;
		}
		if(name.length == 0){
			alert('�п�J��D�H�m�W!');
			return false;
		}
		if(customerDate.length == 0){
			alert('��ܧ�D����I');
			return false;
		}else if(!/^(\d{4})-(\d{2})-(\d{2})$/.test(customerDate)){
			alert('��D����榡���~�I');
			return false;
		}
		if(incidentDate.length == 0){
			alert('��ܨƵo����I');
			return false;
		}else if(!/^(\d{4})-(\d{2})-(\d{2})$/.test(incidentDate)){
			alert('�Ƶo����榡���~�I');
			return false;
		}
		if(complaints.length == 0){
			alert('�п�J��D�ƶ��I');
			return false;
		}
		if(content.length == 0){
			alert('�п�J��D���e�I');
			return false;
		}
		if(upload.length == 0 && oldUpload.length == 0){
			alert('�п�ܹϤ���PDF!');
			return false;
		}
		$("#formAdd").ajaxSubmit({
            type: "post",
            url: "cms_customer_add_post.php",
			dataType:'json',
            success: function (data) {
				if(data.result){
					alert('���榨�\�I\n�s�����G'+data.number);
					window.location = 'cms_customer_main.php';
				}else{
					alert(data.error);
				}
			}
		});
	});
	$('#shop').change();
});