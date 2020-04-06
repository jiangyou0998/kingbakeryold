$(function(){
	$('#btnAdd').click(function(){
		window.location = 'cms_volunteer_oper.php';
	});
	$('[name = "edit"]').click(function(){
		var id = $(this).attr('data-id');
		window.location = 'cms_volunteer_oper.php?id='+id;
	});
	$('[name = "del"]').click(function(){
		if(!confirm('確定要關閉嗎？\n關閉后將無法恢復！')){
			return false;
		}
		var id = $(this).attr('data-id');
		window.location = 'cms_volunteer_post.php?action=del&id='+id;
	});
	$('[name = "look"]').click(function(){
		var id = $(this).attr('data-id');
		window.location = 'cms_volunteer_enroll_list.php?id='+id;
	});
	$('#btnSearch').click(function(){
		var year = $('#year').val();
		var month = $('#month').val();
		var key = $('#key').val();
		var state = $('#state').val();
		window.location = 'cms_volunteer_main.php?key='+key+'&state='+state+'&month='+month+'&year='+year;
	});
	$('#searchDateShow').click(function(){
		WdatePicker({dateFmt:'yyyy-MM-dd D',vel:'searchDate',isShowClear:false,readOnly:true});
	});
});