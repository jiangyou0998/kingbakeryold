$(function(){
	$('#dateShow').click(function(){
		WdatePicker({minDate:'%y-%M-%d %D',dateFmt:'yyyy-MM-dd D',vel:'date',isShowClear:false,readOnly:true});
	});
	$('#regdateShow').click(function(){
		
		if($('#dateShow').val().length>0){
			WdatePicker({minDate:'%y-%M-%d %D',maxDate:'#F{$dp.$D(\'dateShow\')}',dateFmt:'yyyy-MM-dd D',vel:'regdeadline',isShowClear:false,readOnly:true});
		}else{
			alert('�п�ܬ��ʤ���I');
		}
	});	
	$('#btnBack').click(function(){
		window.location = 'cms_volunteer_main.php';
	});
	$('#btnSubmit').click(function(){
		if($('#date').val().length == 0){
			alert('�п�ܤ���I');
			return false;
		}else if(!/^(\d{4})-(\d{2})-(\d{2})$/.test($('#date').val())){
			alert('����榡���~�I');
			return false;
		}
		if($('#content').val().length == 0){
			alert('�п�ܬ��ʤ��e�I');
			return false;
		}
		if($('#startTime').val().length == 0){
			alert('�п�ܶ}�l�ɶ��I');
			return false;
		}
		if($('#endTime').val().length == 0){
			alert('�п�ܵ����ɶ��I');
			return false;
		}else if(!opinionStartTimeEndTime($('#startTime').val(),$('#endTime').val())){
			alert('�}�l�������j�󵲧�����I');
			return false;
		}
		if($('[name="qual[]"]:checked').length == 0){
			alert('�п�ܰѥ[����I');
			return false;
		}else if($('[name="qual[]"]:checked').length ==1 &&$('[name="qual[]"]:checked').val() == '3'){
			alert('���u�a�ݤ�����I');
			return false;
		}
		if($('#adderss').val().length==0){
			alert('�п�J�a�I�I');
			return false;
		}
		if($('#detail').val().length==0){
			alert('�п�J�ԲӬ��ʤ��e�I');
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