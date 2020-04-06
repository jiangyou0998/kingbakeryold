var menu_item =  new Array();
		var timer;
		$(function(){
			
			
			$('#print2').bind('click',function(){//�ˬd�C�L�O�_�[���O�_������u
				
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
						alert('�п�ܤ��u�I');
					}
				}
			});

			
			//�I�}�������
			$('#lineCheck').click(function(){
				
				var line = $('input[name=line]').val();
				if($('.shop_box').is(":visible"))
				{
					//���ÿ���ɭ�
					$('.shop_box').hide();
					$(this).css('border-bottom','1px solid #ccc');
					$('#lineCheck').html(line);
				}
				else
				{
					//�i�}����ɭ�
					$('.shop_box').show(100);
					$(this).css('border-bottom','none');
					
					//�[���ƾ�
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
			
			//���X������������ä�����
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
			
			//���u�I��
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
						//�j�w�l���ɶ�
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
		*�[���C�L�C��
		*@param string line ���u�W��
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
					
					$('#print2').html('<option value="0">�п��</option>');
					if(data!='no_data')
					{
						$('#print2').append(data);
					}
					else
					{
						//alert('�L�ƾڡA�п�ܨ�L���u�I');
					}
				}
			});
		}
		/**
		*�[���s��ƾ�
		*/
		function LoadingData()
		{
			var str_menu_item = $('#menu_item').val();
			if(str_menu_item!=""&&str_menu_item!="[]")
			{
				menu_item = JSON.parse(str_menu_item);
				
				for(var j=0 in menu_item)
				{
					//���o�{���ձN�ͦ��s����
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
			
			
			//�[���ƿ��

			var time_array = $('#menu_time').val().split(',');
			var period_array = $('#menu_period').val().split(',');
			$(':checkbox').each(function(){

				if($(this).attr('name')=='time[]')//�ɬq
				{
					for(var i=0 in time_array)
					{
						if($(this).val() == time_array[i])
						{
							 $(this).attr("checked", true);  
						}
					}
				}
				else if($(this).attr('name')=='Period[]')//�[���P��
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
			
			if($('#lineCheck').html()=='���'&& $('input[name=line]').val()!='')
			{
				$('#lineCheck').html($('input[name=line]').val());
				LoadingPrintList($('input[name=line]').val());
			}
			
		}
		/*
		*�����涵��html 
		*@item  ��涵�ت�JSON�ƾ�
		*/
		function GetMenuItemHtml(item)
		{
			var html;
			html  = '<li id="'+item.id+'" sort="'+item.sort+'"><div>';
			html += '	<font>&nbsp;'+item.receipt+'</font> ';
			html += '	<a href="javascript:del('+item.id+')"  class="del" >�R��</a>';
			html += '	<a href="javascript:Eidt('+item.id+')" class="eidt" >�s��</a>';
			html += '</div></li>';
			
			return html;
		}
		/*
		*�����涵��html 
		*@item  ��涵�ت�JSON�ƾ�
		*/
		function GetMenuItemEidtHtml(item)
		{
			var html;
			//html  = '<li id="'+item.id+'" sort="'+item.sort+'"><div>';
			html = '	<font>&nbsp;'+item.receipt+'</font> ';
			html += '	<a href="javascript:del('+item.id+')"  class="del" >�R��</a>';
			html += '	<a href="javascript:Eidt('+item.id+')" class="eidt" >�s��</a>';
			//html += '</div></li>';
			
			return html;
		}
		
		//�_��
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
		*�s�W�էO
		*@group_id ��?ID  ''�i?�šA��?�ͦ�
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
				html_title+='		<a href="javascript:DelGroup('+group_id+');">�R��</a>';
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
				alert('�̦h3�ӲէO ');
			}
				
		}
		
		/**
		*�ǦC�Ƨ�
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
		//�R���էO
		function DelGroup(group_id)
		{
			if($('.list_title').length>1)
			{
				if(!confirm('�R���ձN�|�p�P�ƾڤ@�_�R���A�T�w�n�R���H'))
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
				alert('�̤�1�ӲէO ');
			}
		}
		
		//�O�s��涵��
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
			//����
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
			
			
			//����
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
				alert('�����J���~�I');
				return;
			}
			
			if(receipt.length==0)
			{
				alert('�п�J���ڦW�١I')
				return;
			}
			
			if(menuName.length==0)
			{
				alert('�п�J�\�P�W�١I')
				return;
			}
			
			if(kitchen.length==0)
			{
				alert('�п�J�p�ЦW�١I')
				return;
			}
			if(ename.length==0){
				alert('�п�J�^��W�١I');
				return;
			}
			
			if(print=='0')
			{
				alert('�п�ܦC�L�ܡI')
				return;
			}
			
			if(dept =='0')
			{
				alert('�п�ܳ����I');
				return ;
			}
			
			if(preparationTime.length==0)
			{
				alert('�п�J�ǳƮɶ��I')
				return;
			}
			else if(!reg.test(preparationTime))
			{
				alert('�п�J�Ʀr�I');
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
				
				alert('�K�[���\�I');
			}
			else//�s��
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
				
				

				alert('�s�覨�\�I');
			}
			
			//Item_Sequence($('.item_list[time='+group_id+'] ul'));
			//�_��
			Reset();
			
			
		}
		/*
		*�]�w�s��ƾ�
		*@id  ����ID
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
					
					//����
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
					//����
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
		*�R����涵��
		*@id
		*/
		function del(id)
		{
			if(!confirm('�T�w�R���ƾڡH'))
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
		
		//��ܲէO
		function SelectGroup(id)
		{
			$('.list_title').css('background','#ccffff');
			$('#'+id).css('background','#FF9224');
			$('#group_id').val(id);
		}
		
		/**
		*�˴����
		*/
		function Check(){
			if($('#txtName').val().length==0){
				alert('�п�J�s�\��W�١I');
				return false;
			}
			
			if($('#txtMenuCode').val().length==0){
				alert('�п�J�s�\��s���I');
				return false;
			}else if($('#oldMenuCode').val()!=$('#txtMenuCode').val() && !IsRepeatCode()){
				alert('�s�\��s���w�Q�e��,�Э��s��J�I');
				return false;
			}
			
			if($('#txtDate').val().length==0){
				alert('�п���\�P�ͮĤ���I');
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
		*�i���������
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
		*�i������Ͽ�
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
			�˴��s�\��s���O�_����
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
					alert('�ˬd�s���ɵo�Ϳ��~�I');
				}
			});
			
			return isRepeat;
		}