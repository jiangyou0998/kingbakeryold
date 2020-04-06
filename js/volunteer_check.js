$(function(){
	$('#btnApproval').click(function(){
		if(confirm('確定要批准嗎？')){
			$('#type').val('0');
		}else{
			return false;
		}
	});
	$('#btnRefuse').click(function(){
		if(confirm('確定要拒絕嗎？')){
			$('#type').val('1');
		}else{
			return false;
		}
	});
});