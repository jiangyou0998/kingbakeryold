$(function(){
	$('#btnApproval').click(function(){
		if(confirm('�T�w�n���ܡH')){
			$('#type').val('0');
		}else{
			return false;
		}
	});
	$('#btnRefuse').click(function(){
		if(confirm('�T�w�n�ڵ��ܡH')){
			$('#type').val('1');
		}else{
			return false;
		}
	});
});