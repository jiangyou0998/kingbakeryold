
var $ = function (id) {
    return "string" == typeof id ? document.getElementById(id) : id;
}
var Bind = function(object, fun) {
    return function() {
        return fun.apply(object, arguments);
    }
}
function AutoComplete(obj,autoObj,arr,left,right){
    this.obj=$(obj);        //輸入框
    this.autoObj=$(autoObj);//DIV的根節點
    this.value_arr=arr;        //不要包含重復值
    this.index=-1;          //當前選中的DIV的索引
    this.search_value="";   //保存當前搜索的字符
	this.left = left;
	this.right = right;
}
AutoComplete.prototype={
    //初始化DIV的位置
    init: function(){
        this.autoObj.style.left = this.left + this.obj.offsetLeft + "px";
        this.autoObj.style.top  = this.right + this.obj.offsetTop + this.obj.offsetHeight + "px";
        this.autoObj.style.width= this.obj.offsetWidth +17+ "px";//減去邊框的長度2px   
    },
    //刪除自動完成需要的所有DIV
    deleteDIV: function(){
        while(this.autoObj.hasChildNodes()){
            this.autoObj.removeChild(this.autoObj.firstChild);
        }
        this.autoObj.className="auto_hidden";
    },
    //設置值
    setValue: function(_this){
        return function(){
            _this.obj.value=this.seq[1];
            _this.autoObj.className="auto_hidden";
			document.getElementById("int_user").value = this.seq[0];
        }       
    },
    //模擬鼠標移動至DIV時，DIV高亮
    autoOnmouseover: function(_this,_div_index){
        return function(){
            _this.index=_div_index;
            var length = _this.autoObj.children.length;
            for(var j=0;j<length;j++){
                if(j!=_this.index ){       
                    _this.autoObj.childNodes[j].className='auto_onmouseout';
                }else{
                    _this.autoObj.childNodes[j].className='auto_onmouseover';
                }
            }
        }
    },
    //更改classname
    changeClassname: function(length){
        for(var i=0;i<length;i++){
            if(i!=this.index ){       
                this.autoObj.childNodes[i].className='auto_onmouseout';
            }else{
                this.autoObj.childNodes[i].className='auto_onmouseover';
                this.obj.value=this.autoObj.childNodes[i].seq;
            }
        }
    }
    ,
    //響應鍵盤
    pressKey: function(event){
        var length = this.autoObj.children.length;
        if(event.keyCode==40){//光標鍵"↓"
            ++this.index;
            if(this.index>length){
                this.index=0;
            }else if(this.index==length){
                this.obj.value=this.search_value;
            }
            this.changeClassname(length);
        }else if(event.keyCode==38){//光標鍵"↑"
            this.index--;
            if(this.index<-1){
                this.index=length - 1;
            }else if(this.index==-1){
                this.obj.value=this.search_value;
            }
            this.changeClassname(length);
        }else if(event.keyCode==13){//回車鍵
            this.autoObj.className="auto_hidden";
            this.index=-1;
        }else{
            this.index=-1;
        }
    },
    //程序入口
    start: function(event){
        if(event.keyCode!=13&&event.keyCode!=38&&event.keyCode!=40){
            this.init();
            this.deleteDIV();
            this.search_value=this.obj.value;
            var valueArr=this.value_arr;
            valueArr.sort();
            if(this.obj.value.replace(/(^\s*)|(\s*$)/g,'')==""){ return; }//值為空，退出
            try{ var reg = new RegExp("(" + this.obj.value + ")","i");}
            catch (e){ return; }
            var div_index=0;//記錄創建的DIV的索引
			var m=0;
            for(var i=0;i<valueArr.length;i++){
                if(reg.test(valueArr[i][1])){ m++;
                    var div = document.createElement("div");
                    div.className="auto_onmouseout";
                    div.seq=valueArr[i];
                    div.onclick=this.setValue(this);
					div.style.fontSize=14;
                    div.onmouseover=this.autoOnmouseover(this,div_index);
					//搜索到的字符粗體顯示
                    div.innerHTML=valueArr[i][1].replace(reg,"<strong>$1</strong>")+"&nbsp;&nbsp;&nbsp;&nbsp;"+valueArr[i][2];
                    this.autoObj.appendChild(div);
                    this.autoObj.className="auto_show";
                    div_index++;
                }
            }
			if(m==0){document.getElementById("errorUser").style.display="inline-block";document.getElementById("int_user").value ="";}
			else {document.getElementById("errorUser").style.display="none";}
			Change_boos();
        }
        this.pressKey(event);
        window.onresize=Bind(this,function(){this.init();});
    }
}