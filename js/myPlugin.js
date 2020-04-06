// JavaScript Document
// coder: By Hada
// explain: 跟随滚动条滚动的块
// paramet: obj为要滚动对象的ID

$.myPlugin = {
 rollBar:function(obj){
  var rollId = $(obj);             
  var offset = rollId.offset();
  var rollIdW = rollId.parent().width();
  $(window).scroll(function(){
   var scrollTop = $(window).scrollTop();
   if(scrollTop >= offset.top){ 
	//alert("123");
    rollId.css({'position':'fixed','top':'0','width':'100%'});
	
	/*
	if($("#Occ").html()!="1")
	{
		//rollId.parent().prepend("<div id='Occ' style='float:left;width:100%;height:"+rollId.height()+"px;'>1</div>");
	}
	else
	{
		//$("#Occ").height(rollId.height());
	}
	*/
	$("#Occ").height(rollId.height())
	//;
	//alert(rollId.parent().width());
    //以下code为IE6执行，本来不想兼容IE6，不过还是写了吧
    if ($.browser.msie){
     isIE = $.browser.version;
     switch(isIE){
      case '6.0':
      rollId.css({'position':'absolute','top':scrollTop});
      break;
     }
    }
   }else{
    rollId.css({'position':'static','width':'auto'});
   }
  });
 }
}