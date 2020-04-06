$(function(){
	//返回主界面按鈕
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
					alert('未有數據！');
				}
			},
			error:function(){
				alert('頁面錯誤！');
			}
		});
	});
	
	$('#CustomerDate,#IncidentDate').click(function(){
		WdatePicker();
	});
	
	//提交數據
	$('#btnSubmit').click(function(){
		if(!confirm('確認提交的數據是否完全正確，提交後將不能修改！')){
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
			alert('請選擇來源！');
			return false;
		}
		if(shop == '--'){
			alert('請選擇投訴分店！');
			return false;
		}
		if(dept == '--' && staff.length == 0){
			alert('請選擇部門或輸入員工！');
			return false;
		}
		if(name.length == 0){
			alert('請輸入投訴人姓名!');
			return false;
		}
		if(customerDate.length == 0){
			alert('選擇投訴日期！');
			return false;
		}else if(!/^(\d{4})-(\d{2})-(\d{2})$/.test(customerDate)){
			alert('投訴日期格式錯誤！');
			return false;
		}
		if(incidentDate.length == 0){
			alert('選擇事發日期！');
			return false;
		}else if(!/^(\d{4})-(\d{2})-(\d{2})$/.test(incidentDate)){
			alert('事發日期格式錯誤！');
			return false;
		}
		if(complaints.length == 0){
			alert('請輸入投訴事項！');
			return false;
		}
		if(content.length == 0){
			alert('請輸入投訴內容！');
			return false;
		}
		if(upload.length == 0 && oldUpload.length == 0){
			alert('請選擇圖片或PDF!');
			return false;
		}
		$("#formAdd").ajaxSubmit({
            type: "post",
            url: "cms_customer_add_post.php",
			dataType:'json',
            success: function (data) {
				if(data.result){
					alert('提交成功！\n編號為：'+data.number);
					window.location = 'cms_customer_main.php';
				}else{
					alert(data.error);
				}
			}
		});
	});
	$('#shop').change();
});