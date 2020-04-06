$(function(){
	$('#btnAdd').click(function(){
		var type = $('#type').val();
		window.location = 'cms_volunteer_option_oper.php?&t='+type;
	});
	$('[name="edit"]').click(function(){
		var type = $('#type').val();
		var id = $(this).attr('data-id');
		window.location = 'cms_volunteer_option_oper.php?id='+id+'&t='+type;
	});
	$('[name="del"]').click(function(){
		var id = $(this).attr('data-id');
		var type = $('#type').val();
		window.location = 'cms_volunteer_option_post.php?action=del&t='+type+'&id='+id;
	});
});