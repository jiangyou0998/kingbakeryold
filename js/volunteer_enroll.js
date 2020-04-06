$(function(){
	$('#familyNumber').on('keyup',function(){
		if($(this).val().length > 0&&!/^\d+$/.test($(this).val())){
			alert('請輸入數字！');
			$(this).val('');
			return false;
		}
		if($(this).val().length > 0&&$(this).val()>0){
			var number = $(this).val();
			var count = $('#familylist  table tbody tr').length;
			if(count>number){
				for(count;count>=number;count--){
					$('#familylist  table tbody tr').eq(count).remove();
				}
				
			}else{
				var heml = '';
				for(count;count<number;count++){
					heml+= '<tr>';
					heml+= '	<td class="text-center">'+(count+1)+'</td>';
					heml+= '	<td><input type="text" name="familyName[]"  class="form-control"/></td>';
					heml+= '	<td><input type="text" name="familyRelationship[]"  class="form-control"/></td>';
					heml+= '</tr>'
				}
				$('#familylist  table tbody').append(heml);
			}
			//$('#familylist  table tbody').empty();
			
			$('#familylist').show();
		}else {
			$('#familylist').hide();
		}
	});
	$('#other').change(function(){
		if($(this).is(':checked')){
			$('#time').show();
			$('#nature').show();
		}else{
			$('#time').hide();
			$('#nature').hide();
		}
	});
	$('#btnSubmit').click(function(){
		if($('#Name').val().length == 0){
			alert('請輸入姓名！');
			return false;
		}
		if($('#dept').val().length == 0){
			alert('請輸入部門！');
			return false;
		}
		if($('#email').val().length == 0){
			alert('請輸入電郵！');
			return false;
		}else if(!/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/.test($('#email').val())){
			alert('電郵格式不正確！');
			return false;
		}
		if($('#tel').val().length == 0){
			alert('請輸入聯絡電話！');
			return false;
		}
		if(!$('#agree').is(':checked')){
			alert('請勾選同意條款！');
			return false;
		}
	});
	$('#btnBack').click(function(){
		window.location = 'volunteer_list.php';
	});
});