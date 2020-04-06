$(function(){
	//½s¿è
	$('button[name="edit"]').click(function(){
		var id = $(this).attr('data-id');
		window.location = "cms_customer_rpt_detail.php?id="+id;
	});
	$('#btnBack').click(function(){
		window.location = "cms_customer_rpt.php";
	});
	//ËÑŒ¤
	$('#btnSearch').click(function(){
		var key = $('#searchKey').val();
		var year = $('#year').val();
		var month = $('#month').val();
		var line = $('#line').val();
		var shop = $('#shop').val();		
		window.location = "cms_customer_main.php?key="+key+"&year="+year+"&month="+month+"&line="+line+"&shop="+shop;
	});

});