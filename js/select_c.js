
(function($){
	var item,menu,timer = null,type = 'select';

	
	$.fn.Select = function(options){
		var settings = {
			areaTitle: "�ϰ�",
			shopTitle: "����",
			type:'select',
			post_type:1
		};
		
		options = $.extend(settings,options);
		
		type = options.type;
		
		var data = '';
		//alert($('#data').val());
		if($('#data').length>0&&$('#data').val()!=''){
			data = eval('('+$('#data').val()+')');
			this.html(SetDataHtml(data));
		}else{
			this.html('���I������I');
		}
		
		
		$('body').append('<div class="bg"><div class="show"><div class="area" id="menu"><div class="title">&nbsp;'+options.areaTitle+'&nbsp;<a href="javascript:void(0);" id="btnClear">�M��</a></div><ul></ul></div><div class="shop" id="item"><div class="title">&nbsp;'+options.shopTitle+'&nbsp;<a href="javascript:void(0);" id="btnClearShop">����</a>&nbsp;<a href="javascript:void(0);" id="btnCheckAll">����</a></div><ul></ul></div></div></div>');
		
		item = $('#item');
		menu = $('#menu');
		menu.attr('type',options.post_type);
		
		$.ajax({
			type:'post',
			dataType:'json',
			url:'cms_order_c_area_ajax.php',
			data:{action:'area',type:options.post_type},
			success:function(json){
				var i;
				menu.find('ul').eq(0).empty();
				if(json.retStatus==1){
					for(i=0;i<json.retData.length;i++){
						tempHtml = '<li data-code="'+json.retData[i].code+'" >';
						tempHtml+= json.retData[i].name;
						tempHtml+= '</li>';
						menu.find('ul').eq(0).append(tempHtml);
					}
				}
				/*�ϰ춵 �I���ƥ�*/
				AreaBindClick();
			}
		});
		
		
		
		$('#btnClearShop').bind('click',function(){
			item.find('li:visible').removeClass('checked');
		});
		$('#btnCheckAll').bind('click',function(){
			item.find('li:visible').addClass('checked');
		});
		/*�j�w�M�����s�ƥ�*/
		$('#btnClear').bind('click',function(){
			Clear();
		});
		
		/*�I���}���I��ɭ�*/
		this.bind('click',function(e){
			PreLoading();
			$('.bg').show();
		});
		/*���в��}����*/
		$('.show').bind('mouseout',function(e){
			if(checkHover(e,this)){
				timer = setTimeout(function(){
					Close();
				},5000);
			}
		});
		/*���Э��s�^���I��ɭ��M�� �p��*/
		$('.show').bind('mouseover',function(e){
			clearTimeout(timer);
		});

		$('.bg').click(function(event){
			var _con = $('.show');  
			if(!_con.is(event.target) && _con.has(event.target).length === 0){
				Close();         
			}
		});
		
		
	}
	/**
		�������f
	*/
	function Close(){
		$('.bg').hide();
		var data = GetData();
		var strHtml = SetDataHtml(data);
		if(strHtml!=''){
			$('#select').html(strHtml);
			$('#data').val(JSON.stringify(data));
		//	if()
		}else{
			$('#select').html('���I������I');
			$('#data').val('');
		}
		if(type == 'search'){
			$('#data').parents('form').submit();
		}
	}
	/**
		��ƾڹw�[��
	*/
	function PreLoading(){
		if($('#data').length>0&&$('#data').val()!=''){
			var data = eval('('+$('#data').val()+')');
			item.find('li').removeClass('checked');
			menu.find('li').removeClass('checked');
			item.find('ul').empty();
			menu.find('li').each(function(){
				var i,code=$(this).attr('data-code');
				for(i=0;i<data.length;i++){
					if(code == data[i].code){
						//$(this).addClass('checked');
						ShopBind(code);
					}
				}
			});
			
			item.find('li').each(function(){
				var i,f,code=$(this).attr('data-code');
				$(this).hide();
				for(i=0;i<data.length;i++){
					for(f=0;f<data[i].item.length;f++){
						if(code == data[i].item[f].code){
							//alert(code);
							$(this).addClass('checked');
							
						}
					}
				}
			});
		}
	}
	/**
			�ھ�CODE ����ƾ�
	*/
	function ShopBind(code){
		var isAjax = true;
		item.find('li').hide();
		item.find('li').each(function(){
			if($(this).attr('data-fcode') == code){
				$(this).show();
				isAjax = false;
			}
		});
		if(isAjax){
			$.ajax({
				type:'post',
				dataType:'json',
				async:false,
				url:'cms_order_c_area_ajax.php',
				data:{action:'shop',code:code,type:$('#menu').attr('type')},
				success:function(json){
					var i,tempHtml;
					if(json.retStatus==1){
						//item.find('ul').empty();
						for(i=0;i<json.retData.length;i++){
							tempHtml = '<li data-code="'+json.retData[i].id+'" data-fcode="'+json.retData[i].area_code+'">';
							tempHtml+= '#'+json.retData[i].code+'-'+json.retData[i].name;
							tempHtml+= '</li>';
							item.find('ul').append(tempHtml);
						}
					}
					/*������ �I���ƥ�*/
					ShopBindClick();
				}
			});
		}
	}
	
	/**
		�������بƥ�j�w
	*/
	function ShopBindClick(){
		item.find('li').unbind("click");
		item.find('li').bind('click',function(){
			//var num = $(this).attr('data-checknum');
			var fcode = $(this).attr('data-fcode');
			var num = menu.find('[data-code ='+fcode+']').attr('data-checknum');
			if(isNaN(num)){
				num = 0;
			}else{
				num = parseInt(num);
			}
			if($(this).is('.checked')){
				$(this).removeClass('checked');
				num -=1;
			}else{
				$(this).addClass('checked');
				num +=1;
			}
			//alert('1|'+num);
			//alert(menu.find('[data-code ='+fcode+']').html());
			menu.find('[data-code ='+fcode+']').attr('data-checknum',num);
		});
	}
	/**
		�ϰ춵�بƥ�j�w
	*/
	function AreaBindClick(){
		menu.find('li').bind('click',function(){
			menu.find('li').removeClass('checked');
		
			var code = $(this).attr('data-code');
			if($(this).is('.checked')){
				$(this).removeClass('checked');
				item.find('li').each(function(){
					if(code == $(this).attr('data-fcode')){
						$(this).remove();
					}
				});
			}else{
				
				$(this).addClass('checked');
				ShopBind(code);
			}
		});
	}
	/**
		����ƾ�
	*/
	function GetData(){
		var dataArray = new Array();
		menu.find('li').each(function(){
			if(parseInt($(this).attr('data-checknum'))==0){
				return true;
			}
			var code = $(this).attr('data-code');
			var itemArray = new Array();
			item.find('li.checked').each(function(){
				if(code == $(this).attr('data-fcode')){
					itemArray.push({
						code:$(this).attr('data-code'),
						name:$(this).html()
					});
				}
			});
			if(itemArray.length>0)
			{
				dataArray.push({
					code:$(this).attr('data-code'),
					name:$(this).html(),
					item:itemArray
				});
			}
			
		});
		return dataArray;
	}
	/**
		�ھڿ�ܪ��ƾ����
	*/
	function SetDataHtml(data){
		var i,f,strItemHtml='',striMenuHtml='';
		for(i=0;i<data.length;i++){
			strItemHtml = '';
			for(f=0;f<data[i].item.length;f++){
				strItemHtml+='<li>'+data[i].item[f].name+'</li>';
			}
			if(i == (data.length - 1)){
					//alert('1');
				striMenuHtml+='<div class="show-main no-border"><div class="show-area">'+data[i].name+'�G</div><div class="show-shop"><ul>'+strItemHtml+'</ul></div></div>';
			}else{
				//alert('2');
				striMenuHtml+='<div class="show-main"><div class="show-area">'+data[i].name+'�G</div><div class="show-shop"><ul>'+strItemHtml+'</ul></div></div>';
			}
		}
		return striMenuHtml;
	}
	/**
		�M���Ҧ�?�w?��
	*/
	function Clear(){
		item.find('li').removeClass('checked');
		menu.find('li').removeClass('checked');
		item.find('ul').empty();
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
	
})(jQuery);