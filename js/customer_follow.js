$(function(){
	//返回主界面按鈕
	$('#btnBack').click(function(){
		window.location = "cms_customer_main.php";
	});
	$('#replyDate').click(function(){
		WdatePicker();
	});
	$('#arrangement').change(function(){
		var unit = $(this).find("option:selected").attr('data-unit');
		$('.input-group-addon').html(unit);
	});
	//提交數據
	$('#btnSubmit').click(function(){
		if($('#replyDate').val().length==0){
			alert('請選擇覆客日期！');
			return false;
		}
		if($('#contact').val().length==0){
			alert('請輸入電話/電郵/書面！');
			return false;
		}
		if($('#arrangement').val()=='--'){
			alert('請選擇賠償安排！');
			return false;
		}
		if($('#coupon1').val().length>0&&!/^\d+$/.test($('#coupon1').val())){
			alert('只能輸入數字！');
			return false;
		}
		$("#formAdd").ajaxSubmit({
            type: "post",
            url: "cms_customer_follow_post.php",
			dataType:'json',
            success: function (data) {
				if(data.result){
					alert('提交成功！');
					window.location = 'cms_customer_main.php';
				}else{
					alert(data.error);
				}
			}
		});
	});
	$('#shop').change();
});