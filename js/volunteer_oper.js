$(function(){
	$('#dateShow').click(function(){
		WdatePicker({minDate:'%y-%M-%d %D',dateFmt:'yyyy-MM-dd D',vel:'date',isShowClear:false,readOnly:true});
	});
	$('#regdateShow').click(function(){
		
		if($('#dateShow').val().length>0){
			WdatePicker({minDate:'%y-%M-%d %D',maxDate:'#F{$dp.$D(\'dateShow\')}',dateFmt:'yyyy-MM-dd D',vel:'regdeadline',isShowClear:false,readOnly:true});
		}else{
			alert('請選擇活動日期！');
		}
	});	
	$('#btnBack').click(function(){
		window.location = 'cms_volunteer_main.php';
	});
	$('#btnSubmit').click(function(){
		if($('#date').val().length == 0){
			alert('請選擇日期！');
			return false;
		}else if(!/^(\d{4})-(\d{2})-(\d{2})$/.test($('#date').val())){
			alert('日期格式錯誤！');
			return false;
		}
		if($('#content').val().length == 0){
			alert('請選擇活動內容！');
			return false;
		}
		if($('#startTime').val().length == 0){
			alert('請選擇開始時間！');
			return false;
		}
		if($('#endTime').val().length == 0){
			alert('請選擇結束時間！');
			return false;
		}else if(!opinionStartTimeEndTime($('#startTime').val(),$('#endTime').val())){
			alert('開始日期不能大於結束日期！');
			return false;
		}
		if($('[name="qual[]"]:checked').length == 0){
			alert('請選擇參加條件！');
			return false;
		}else if($('[name="qual[]"]:checked').length ==1 &&$('[name="qual[]"]:checked').val() == '3'){
			alert('員工家屬不能單選！');
			return false;
		}
		if($('#adderss').val().length==0){
			alert('請輸入地點！');
			return false;
		}
		if($('#detail').val().length==0){
			alert('請輸入詳細活動內容！');
			return false;
		}
	});
	function opinionStartTimeEndTime(stratTime, endTime) { 
			stratTime = '2016-06-06 '+stratTime;
			endTime = '2016-06-06 '+endTime;
            var sdate = new Date(stratTime);  
            var edate = new Date(endTime);  
            if (sdate.getTime() > edate.getTime()) {  
                return false;  
            }  
            return true;  
        } 
});