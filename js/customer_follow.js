$(function(){
	//��^�D�ɭ����s
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
	//����ƾ�
	$('#btnSubmit').click(function(){
		if($('#replyDate').val().length==0){
			alert('�п���ЫȤ���I');
			return false;
		}
		if($('#contact').val().length==0){
			alert('�п�J�q��/�q�l/�ѭ��I');
			return false;
		}
		if($('#arrangement').val()=='--'){
			alert('�п�ܽ��v�w�ơI');
			return false;
		}
		if($('#coupon1').val().length>0&&!/^\d+$/.test($('#coupon1').val())){
			alert('�u���J�Ʀr�I');
			return false;
		}
		$("#formAdd").ajaxSubmit({
            type: "post",
            url: "cms_customer_follow_post.php",
			dataType:'json',
            success: function (data) {
				if(data.result){
					alert('���榨�\�I');
					window.location = 'cms_customer_main.php';
				}else{
					alert(data.error);
				}
			}
		});
	});
	$('#shop').change();
});