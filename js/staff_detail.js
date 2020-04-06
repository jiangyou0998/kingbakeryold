$(function(){
				
	var _clickNumber = 0;
	$('#quit').datetimepicker({lang:'en',format: 'Y/m/d', timepicker:false});
	
	/**
		提交表單
	*/
	$('#btnSubmit').bind('click',function(){
		
		return CheckForm();
	});
	
	/** 
		選擇不同的狀態顯示
	*/
	$(':radio').bind('change',function(){
		
		if($(this).val() == 0)
		{
			$('#quitBox').addClass('hidden');
		}
		else
		{
			$('#quitBox').removeClass('hidden');
		}
	});
	
	/**
		搜索員工資料
	*/
	$('#staffCode').bind('blur',function(){
		
		var staffCode = $(this).val();
		var dept = $('input[name=dept]').val();
		if(staffCode!='')
		{
			$.ajax({
				type:'post',
				url:'getStaffDetail.php',
				data:{action:'get',code:staffCode,dept:dept},
				dataType:'JSON',
				success:function(jsonData){
					
					var i;
					$('#dropdownMenu1').empty();
					if(jsonData!=null)
					{
						for(i=0;i<jsonData.length;i++)
						{
							$('#dropdownMenu1').append('<li><a href="javascript:void(0)" data-code="'+jsonData[i].staffcode+'" data-name="'+jsonData[i].staffname+'" data-date="'+jsonData[i].date+'" data-state="'+jsonData[i].state+'">'+jsonData[i].staffcode+'-'+jsonData[i].staffname+'</a></li>');
						}
						
						BindMenu();
					}
					else
					{
						$('#dropdownMenu1').append('<li><a href="javascript:void(0)">無<a></li>');	
					}
					
					$('.dropdown').addClass('open');
					_clickNumber = 0;
				},
				error:function(){
					alert('ajax錯誤！');
				}
			});
		}
		else
		{
			$('.dropdown').removeClass('open');
		}
		
	});
	
	$(document).click(function(){
		if(_clickNumber>0)
		{
			$('.dropdown').removeClass('open');
			_clickNumber = 0;
		}
		else
		{
			_clickNumber++;
		}
		
	})
	
	
});

/**
	綁定員工下拉列表事件
*/
function BindMenu()
{
	$('.dropdown-menu li a').bind('click',function(){
	//	alert('1');
	
		if( $(this).html()=='無')
			return;
		
		var code = $(this).attr('data-code');
		var name = $(this).attr('data-name');
		var date = $(this).attr('data-date');
		var state = $(this).attr('data-state');
		
		$('#staffName').val(name);
		$('#staffCode').val(code);
		$('#code').val(code);
		
		if(date!='null')
		{
			$('#quit').val(date);
			
		}
		
		if(state == 0)
		{
			$(':radio').eq(0).attr('checked','true');
		}
		else if(state == 1)
		{
			$(':radio').eq(1).attr('checked','true');
			$('#quitBox').removeClass('hidden');
		}
		if(state!='null')
		{
			$('#action').val('update');
			$('#btnSubmit').val('更新');
		}
		else
		{
			$('#action').val('add');
			$('#btnSubmit').val('添加');
		}
		
		$('.dropdown').removeClass('open');
		$('#dropdownMenu1').empty();
	});
}

/**
	檢查表單數據
*/
function CheckForm()
{
	var code = $('#staffCode').val();
	var name = $('#staffName').val();
	var date = $('#quit').val();
	
	if(code.length == 0)
	{
		alert('請輸入員工編號！');
		return false;
	}
	
	if(name.length == 0)
	{
		alert('還未選擇員工！');
		return false;
	}
	
	if($('#quitRadio').is(':checked')&&date.length == 0)
	{
		alert('請選擇離職日期！');
		return false;
	}
}