var menu_item =  new Array();
		var timer;
		$(function(){
			
			
			$('#print2').bind('click',function(){//檢查列印是否加載是否選取分線
				
				var print = $(this);
				var line = $('input[name=line]').val();
				if(print.children().length==1)
				{
					if(line!='')
					{
						LoadingPrintList(line);
					}
					else
					{
						alert('請選擇分線！');
					}
				}
			});

			
			//點開選取分店
			$('#lineCheck').click(function(){
				
				var line = $('input[name=line]').val();
				if($('.shop_box').is(":visible"))
				{
					//隱藏選取界面
					$('.shop_box').hide();
					$(this).css('border-bottom','1px solid #ccc');
					$('#lineCheck').html(line);
				}
				else
				{
					//展開選取界面
					$('.shop_box').show(100);
					$(this).css('border-bottom','none');
					
					//加載數據
					if(line=='')
					{
						$('.class1 ul li').eq(0).click();
					}
					else
					{
						var shop_ids = $('input[name=shopid]').val();
						var shop_array = shop_ids.split(',');
						
						$('.class1 ul li[line='+line+']').click();
						$('.class2 ul li').each(function(){

							for(var i=0 in shop_array)
							{
								if($(this).attr('shop_id')==shop_array[i])
								{
									$(this).addClass('check');
								}										
							}
						});
						$('input[name=shopid]').val(shop_ids);
					}
				}
			});
			
			//移出選取分店框隱藏分店框
			$('#line_select').bind('mouseout',function(e){
				if(checkHover(e,this))
				{
					timer = setTimeout(function(){
						
						var line = $('input[name=line]').val();
						$('.shop_box').hide(400);
						$('#lineCheck').css('border-bottom','1px solid #ccc');
						if(line!='')
						{
							$('#lineCheck').html(line);
							LoadingPrintList(line);
						}
					},400);
				}
			});
			$('#line_select').bind('mouseover',function(e){
				//alert('timer');
				clearTimeout(timer);
			});
			
			//分線點選
			$('.class1 ul li').click(function(){
				$('.class1 ul li').removeClass('check');
				$(this).addClass('check');
				
				var line = $(this).attr('line');
				$('input[name=line]').val(line);
				$('input[name=shopid]').val('');
				$.ajax({
					type: "post",
					url: "cms_menu_oper.php",
					data: {action:'loading_shop', line:line},
					dataType:'json',
					async:false,
					success: function(data){
						$('.class2 ul').empty();
						for (var i=0;i<data.length;i++)
						{
							$('.class2 ul').append('<li shop_id="'+data[i].id+'">#'+data[i].code+' - '+data[i].name+'</li>');
						}
						//綁定子項時間
						$('.class2 ul li').click(function(){
							var shop_ids = '';
							var shop_id = $(this).attr('shop_id');
							var shop_array = $('input[name=shopid]').val().split(',');
							var isChecked = false;
							
							for(var i=0 in shop_array)
							{
								if(shop_array[i] == shop_id)
								{
									isChecked=true;
								}
								else
								{
									if(shop_array[i]!='')
									{
										shop_ids +=','+shop_array[i];
									}
								}
							}
							
							if(!isChecked)
							{
								$('input[name=shopid]').val(shop_ids+','+shop_id);
								$(this).addClass('check');
							}
							else
							{
								$('input[name=shopid]').val(shop_ids);
								$(this).removeClass('check');
							}
							
						});
					}
				});
			});
			
			LoadingData();
			
			
			
		//	alert($('[time=2] ul').html())
		});
		
		/**
		*加載列印列表
		*@param string line 分線名稱
		*/
		function LoadingPrintList(line)
		{
			$.ajax({
				url:'cms_menu_oper.php',
				type:'post',
				timeout:10000,
				data:{action:'print',line:line},
				//dataType:'json',
				error:function(){
					
				},
				success:function(data){
					
					$('#print2').html('<option value="0">請選擇</option>');
					if(data!='no_data')
					{
						$('#print2').append(data);
					}
					else
					{
						//alert('無數據，請選擇其他分線！');
					}
				}
			});
		}
		/**
		*加載編輯數據
		*/
		function LoadingData()
		{
			var str_menu_item = $('#menu_item').val();
			if(str_menu_item!=""&&str_menu_item!="[]")
			{
				menu_item = JSON.parse(str_menu_item);
				
				for(var j=0 in menu_item)
				{
					//未發現分組將生成新分組
					if($('#'+menu_item[j].group_id).length==0)
					{
						NewGroup(menu_item[j].group_id);
					}
					
					$('.item_list').each(function (){
						if($(this).attr('time')==menu_item[j].group_id)
						{
							$(this).find('ul').append(GetMenuItemHtml(menu_item[j]));
						}
					});
					
				}
			}
			else
			{
				NewGroup('');
			}
			
			
			//加載複選框

			var time_array = $('#menu_time').val().split(',');
			var period_array = $('#menu_period').val().split(',');
			$(':checkbox').each(function(){

				if($(this).attr('name')=='time[]')//時段
				{
					for(var i=0 in time_array)
					{
						if($(this).val() == time_array[i])
						{
							 $(this).attr("checked", true);  
						}
					}
				}
				else if($(this).attr('name')=='Period[]')//加載星期
				{
					for(var i=0 in period_array)
					{
						if($(this).val() == period_array[i])
						{
							 $(this).attr("checked", true);  
						}
					}	
				}
			});
			
			if($('#lineCheck').html()=='選擇'&& $('input[name=line]').val()!='')
			{
				$('#lineCheck').html($('input[name=line]').val());
				LoadingPrintList($('input[name=line]').val());
			}
			
		}
		/*
		*獲取菜單項目html 
		*@item  菜單項目的JSON數據
		*/
		function GetMenuItemHtml(item)
		{
			var html;
			html  = '<li id="'+item.id+'" sort="'+item.sort+'"><div>';
			html += '	<font>&nbsp;'+item.receipt+'</font> ';
			html += '	<a href="javascript:del('+item.id+')"  class="del" >刪除</a>';
			html += '	<a href="javascript:Eidt('+item.id+')" class="eidt" >編輯</a>';
			html += '</div></li>';
			
			return html;
		}
		/*
		*獲取菜單項目html 
		*@item  菜單項目的JSON數據
		*/
		function GetMenuItemEidtHtml(item)
		{
			var html;
			//html  = '<li id="'+item.id+'" sort="'+item.sort+'"><div>';
			html = '	<font>&nbsp;'+item.receipt+'</font> ';
			html += '	<a href="javascript:del('+item.id+')"  class="del" >刪除</a>';
			html += '	<a href="javascript:Eidt('+item.id+')" class="eidt" >編輯</a>';
			//html += '</div></li>';
			
			return html;
		}
		
		//復位
		function Reset()
		{
			$('.tabl_item').find('input[type=text]').val('');
			$('.tabl_item').find('input[name=txtPrice1]').val('0');
			$('.tabl_item').find('input[name=txtPrice2]').val('0');
			$('.tabl_item').find('input[name=txtPreparationTime]').val('0');
			$('#item_id').val('');
			$('#print2').val('0');
			$('#selDept').val('0');
			//$('#group_id').val('');
		}

		/*
		*新增組別
		*@group_id 分?ID  ''可?空，自?生成
		*/
		function NewGroup(group_id)
		{
			if($('.list_title').length<3)
			{
				var html_title;
				var group_list
				var check;
				
				if(group_id.length==0)
				{
					group_id = new Date().getTime();
				}
				
				group_list = eval('(' + $('#GroupType').val() + ')');
				
				html_title='	<div class="list_title" onClick="SelectGroup('+group_id+')" id="'+group_id+'" >';
				html_title+='		<select name="sel'+group_id+'">';	
				
				for(var i=0 in group_list)
				{
					check = group_list[i].id==group_id?'selected':'';
					html_title+='			<option value="'+group_list[i].id+'" '+check+'>'+group_list[i].name+'</option>';	
				}
				
				html_title+='		</select>';
				html_title+='		<a href="javascript:DelGroup('+group_id+');">刪除</a>';
				html_title+='	</div>';
				
				$('.list_box').append(html_title);
				$('.items_list').append('<div class="item_list" time="'+group_id+'"><ul class="sortable"></ul></div>');
				
				$( ".sortable" ).sortable({
					connectWith: '.sortable',
					placeholder: 'ui' ,
					cursor: "move",
					items :"li",                       
					opacity: 0.6,                      
					revert: false,                      
					stop: function(event, ui) {
						
						Item_Sequence(ui.item.parent());
						// var group_id = ui.item.parent().parent().attr('time');
						// var item_id = ui.item.attr('id');
						
						// for(var i=0 in menu_item)
						// {
							// if(menu_item[i].id==item_id)
							// {
								// menu_item[i].group_id=group_id;
							// }
						// }
					}
				});
				
			}
			else
			{
				alert('最多3個組別 ');
			}
				
		}
		
		/**
		*序列排序
		*@param  list obj
		*/
		function Item_Sequence(list)
		{
			var group_id = list.parent().attr('time');
			//alert(group_id);
			var i = 0;
			list.find('li').each(function(index){
				var id = $(this).attr('id');
				
				for(i=0;i<menu_item.length;i++)
				{
					if(id==menu_item[i].id)
					{
						
						menu_item[i].sort = index;
						menu_item[i].group_id = group_id; 
						//alert(menu_item[i].sort);
						$(this).attr('sort',index);
					}
				}
			});
			
			i = null;
		}
		//刪除組別
		function DelGroup(group_id)
		{
			if($('.list_title').length>1)
			{
				if(!confirm('刪除組將會聯同數據一起刪除，確定要刪除？'))
				{
					return;
				}
				
				$('#'+group_id).remove();
				$('.item_list[time='+group_id+']').remove();
				for(var i=0 in menu_item)
				{
					if(menu_item[i].group_id==group_id)
					{
						menu_item.splice(i,1);								
					}
				}
			}
			else
			{
				alert('最少1個組別 ');
			}
		}
		
		//保存菜單項目
		function SaveMenuItem()
		{
			var receipt = $('#txtReceipt').val();
			var menuName = $('#txtMenuName').val();
			var kitchen = $('#txtKitchen').val();
			var dept = $('#selDept').val();
			var print = $('#print2').val();
			var preparationTime = $('#txtPreparationTime').val();
			var id = $('#item_id').val();
			var group_id = $('#group_id').val();
			var ename = $('#txtEName').val();
			
			var price1 = new Array();
			var price2 = new Array();
			var b = true;
			var reg = new RegExp("^[0-9]+.?[0-9]*$");  
			//假日
			$('input[name=txtPrice1]').each(function(){
				if($(this).val().length==0)
				{
					b = false;
					return;
				}
				else if(!reg.test($(this).val()))
				{
					b = false;
					return;
				}
				
				price1.push({
					sid:$(this).attr('typeprice'),
					price:$(this).val()
					});
			});
			
			
			//平日
			$('input[name=txtPrice2]').each(function(){
				if($(this).val().length==0)
				{
					b = false;
					return;
				}
				else if(!reg.test($(this).val()))
				{
					b = false;
					return;
				}
				price2.push({
					sid:$(this).attr('typeprice'),
					price:$(this).val()
					});
			});
			
			
			if(!b)
			{
				alert('價格輸入錯誤！');
				return;
			}
			
			if(receipt.length==0)
			{
				alert('請輸入收據名稱！')
				return;
			}
			
			if(menuName.length==0)
			{
				alert('請輸入餐牌名稱！')
				return;
			}
			
			if(kitchen.length==0)
			{
				alert('請輸入廚房名稱！')
				return;
			}
			if(ename.length==0){
				alert('請輸入英文名稱！');
				return;
			}
			
			if(print=='0')
			{
				alert('請選擇列印至！')
				return;
			}
			
			if(dept =='0')
			{
				alert('請選擇部門！');
				return ;
			}
			
			if(preparationTime.length==0)
			{
				alert('請輸入準備時間！')
				return;
			}
			else if(!reg.test(preparationTime))
			{
				alert('請輸入數字！');
				return;
			}
			
			if(id=='')
			{
				var id = new Date().getTime();
				group_id = group_id==''?$('.item_list:eq(0)').attr('time'):group_id;
				
				menu_item.push({
					id:id,
					receipt:receipt,
					menuName:menuName,
					ename:ename,
					kitchen:kitchen,
					dept:dept,
					print:print,
					preparationTime:preparationTime,
					W:price1,
					H:price2,
					group_id:group_id,
					sort:$('.item_list[time='+group_id+'] ul li').length
				});	
				
				$('.item_list[time='+group_id+'] ul').append(GetMenuItemHtml(menu_item[menu_item.length-1]));
				
				alert('添加成功！');
			}
			else//編輯
			{
				for(var i=0 in menu_item)
				{
					if(menu_item[i].id==id)
					{
						menu_item[i].receipt = receipt;
						menu_item[i].menuName = menuName;
						menu_item[i].ename = ename;
						menu_item[i].kitchen = kitchen;
						menu_item[i].dept = dept;
						menu_item[i].print = print;
						menu_item[i].preparationTime = preparationTime;
						menu_item[i].W = price1;
						menu_item[i].H = price2;
						
						if(group_id!='')
						{
							$('#'+id).remove();
							menu_item[i].group_id = group_id;
							menu_item[i].sort = $('.item_list[time='+group_id+'] ul li').length;
							//$('#'+id).html(GetMenuItemEidtHtml(menu_item[i]));
							$('.item_list[time='+group_id+'] ul').append(GetMenuItemHtml(menu_item[i]));
						}
						else
						{
							$('#'+id).html(GetMenuItemEidtHtml(menu_item[i]));
						}
					}
				}
				
				

				alert('編輯成功！');
			}
			
			//Item_Sequence($('.item_list[time='+group_id+'] ul'));
			//復位
			Reset();
			
			
		}
		/*
		*設定編輯數據
		*@id  項目ID
		*/
		function Eidt(id)
		{
			var i;
			var f;
			var sid;
			$('#print2').val('0');
			$('#selDept').val('0');
			for( var i=0 in menu_item)
			{
				if(menu_item[i].id==id)
				{
					$('#txtReceipt').val(menu_item[i].receipt);
					$('#txtMenuName').val(menu_item[i].menuName);
					$('#txtKitchen').val(menu_item[i].kitchen);
					$('#selDept').val(menu_item[i].dept);
					$('#print2').val(menu_item[i].print);
					$('#txtEName').val(menu_item[i].ename);
					$('#txtPreparationTime').val(menu_item[i].preparationTime);
					$('#item_id').val(id);
					
					//假日
					$('input[name=txtPrice1]').each(function(){
						sid = $(this).attr('typeprice');
						
						for(var f=0 in menu_item[i].W)
						{
							if(menu_item[i].W[f].sid==sid)
							{
								$(this).val(menu_item[i].W[f].price)
							}
						}
					});
					//平日
					$('input[name=txtPrice2]').each(function(){

						sid = $(this).attr('typeprice');
						
						for(var f=0 in menu_item[i].H)
						{
							if(menu_item[i].H[f].sid==sid)
							{
								$(this).val(menu_item[i].H[f].price)
							}
						}
					});
				}
			}
		}
		/**
		*刪除菜單項目
		*@id
		*/
		function del(id)
		{
			if(!confirm('確定刪除數據？'))
			{
				return;
			}
			
			for(var i in menu_item)
			{
				if(menu_item[i].id==id)
				{
					menu_item.splice(i,1);
					var a = $('#'+id).parent();
					$('#'+id).remove();
					Item_Sequence(a);
					
				}
			}
			
		}
		
		//選擇組別
		function SelectGroup(id)
		{
			$('.list_title').css('background','#ccffff');
			$('#'+id).css('background','#FF9224');
			$('#group_id').val(id);
		}
		
		/**
		*檢測表單
		*/
		function Check(){
			if($('#txtName').val().length==0){
				alert('請輸入新餐單名稱！');
				return false;
			}
			
			if($('#txtMenuCode').val().length==0){
				alert('請輸入新餐單編號！');
				return false;
			}else if($('#oldMenuCode').val()!=$('#txtMenuCode').val() && !IsRepeatCode()){
				alert('新餐單編號已被占用,請重新輸入！');
				return false;
			}
			
			if($('#txtDate').val().length==0){
				alert('請選擇餐牌生效日期！');
				return false;
			}
			$('#json').val(JSON.stringify(menu_item));
		}

		
		function contains(parentNode, childNode) { 
			if (parentNode.contains) { 
				return parentNode != childNode && parentNode.contains(childNode); } 
			else { 
				return !!(parentNode.compareDocumentPosition(childNode) & 16); 
			} 
		}
	   function checkHover(e,target){
			var rel = getEvent(e).relatedTarget ,
			from = getEvent(e).fromElement ,
			to = getEvent(e).toElement;
			if (getEvent(e).type=="mouseover")  {
				return !contains(target,rel||from) && !( (rel||from)===target );
			} else {
				return !contains(target,rel||to) && !( (rel||to)===target );
			}
		}
		function getEvent(e){
			return e||window.event;
		}
		/**
		*可選分店全選
		*/
		function CheckAll()
		{
			var shop_ids = '';
			$('.class2 ul li').each(function (){
				
				var shop_id = $(this).attr('shop_id');
				$(this).addClass('check');
				
				shop_ids += ',' + shop_id;
			});
			
			$('input[name=shopid]').val(shop_ids);
		}
		/**
		*可選分店反選
		*/
		function CheckReverse()
		{
			var shop_ids = '';
			$('.class2 ul li').each(function (){
				
				var shop_id = $(this).attr('shop_id');
				$(this).toggleClass('check');
				if($(this).hasClass('check'))
				{
					shop_ids += ',' + shop_id;
				}
				
			});
			
			$('input[name=shopid]').val(shop_ids);
		}
		/**
			檢測新餐單編號是否重複
		*/
		function IsRepeatCode(){
			var code = $('#txtMenuCode').val();
			var isRepeat = true;
			$.ajax({
				url:'cms_menu_oper.php',
				type:'post',
				async:false,
				data:{action:'repeat',code:code},
				dataType:'json',
				success:function(data){
					isRepeat = data;
				},
				error:function(){
					alert('檢查編號時發生錯誤！');
				}
			});
			
			return isRepeat;
		}