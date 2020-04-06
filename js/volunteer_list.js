$(function(){
	$('[name="enroll"]').click(function(){
		var id = $(this).attr('data-id');
		window.location = 'volunteer_enroll.php?id='+id;
	});
	$('[name="look"]').click(function(){
		var id = $(this).attr('data-id');
		window.location = 'volunteer_enroll_list.php?id='+id;
	});
});