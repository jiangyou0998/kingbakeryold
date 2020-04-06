// JavaScript Document
// coder: By Hada
// explain: 跟随滚动条滚动的块
// paramet: obj为要滚动对象的ID

$.myPlugin = {
 rollBar:function(obj){
  var rollId = $(obj);             

  var rollIdW = rollId.parent().width();
  var header = $(obj).find('div.header').eq(0);
  var offset = header.offset();
  
  $(window).scroll(function(){
   var scrollTop = $(window).scrollTop();
   if(scrollTop >= offset.top){
	rollId.css({'position':'relative'});
    header.css({'position':'fixed','top':'0'});
	var  tr_html = '';
	var  tr_height = 0;
	//alert('2');
	
	
	
	var  header_height = header.height();
	//alert(tr_height);
	if($('#Occupying').length>0)
	{
		tr_height = $('#tab_occ').height();
		$('#Occupying').height(header_height-tr_height);
		$('#Occupying').html(header_height+'-'+tr_height+'='+(header_height-tr_height));
	}
	else
	{
		rollId.find('table tr').each(function ()
		{
			if($(this).find('th').size()>0)
			{
				tr_html +='<tr>'+$(this).html()+'</tr>';
				tr_height+= $(this).height();
			}
			else
			{
				return false;
			}
			
		});
		header.append('<table class="table_rep" id="tab_occ">'+tr_html+'</table>');
		rollId.prepend('<div id="Occupying" style="height:'+(header_height-tr_height)+'px; width:100%;float:left">1</div>');
	}
	
	//alert(rollId.parent().width());
    //以下code为IE6执行，本来不想兼容IE6，不过还是写了吧
    if ($.browser.msie){
     isIE = $.browser.version;
     switch(isIE){
      case '6.0':
      header.css({'position':'absolute','top':scrollTop,'left':'0'});
      break;
     }
    }
   }else{
   // header.css({'position':'static','width':'auto'});
   }
  });
 }
}