$(function(){
	//�K�[
	$('#btnAdd').click(function(){
		window.location = "cms_customer_add.php";
	});
	//�j�M
	$('#btnSearch').click(function(){
		var key = $('#searchKey').val();
		var year = $('#year').val();
		var month = $('#month').val();
		var line = $('#line').val();
		var shop = $('#shop').val();		
		window.location = "cms_customer_main.php?key="+key+"&year="+year+"&month="+month+"&line="+line+"&shop="+shop;
	});
	//�s��
	$('button[name="edit"]').click(function(){
		var id = $(this).attr('data-id');
		window.location = "cms_customer_view.php?id="+id;
	});
	//�R��
	$('button[name="del"]').click(function(){
		var case_num = $(this).attr('data-number');
		if(!confirm('�T�{�R���ɮ׽s��:' + case_num + '?')){
			return false;
		}
		var id = $(this).attr('data-id');
		
		window.location = "cms_customer_add_post.php?action=del&id="+id;
	});
	//�^��
	$('button[name="reply"]').click(function(){
		var id = $(this).attr('data-id');
		var number = $(this).attr('data-number');
		var replyID = $(this).attr('data-replayID');
		window.location = "cms_customer_reply.php?CCID="+id+"&number="+number+'&id='+replyID;
	});
		//�^��
	$('button[name="follow"]').click(function(){
		var CCID = $(this).attr('data-id');
		var number = $(this).attr('data-number');
		var replyID = $(this).attr('data-replayID');
		var id = $(this).attr('data-follow');
		window.location = "cms_customer_follow.php?CCID="+CCID+"&number="+number+'&replyID='+replyID+'&id='+id;
	});
});