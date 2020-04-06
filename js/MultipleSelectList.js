/**
 * Created by bryant on 2017/11/21.
 */
;(function(){
    var Beautifier = function(ele, opt) {
        this.$element = ele,
            this.defaults = {
                'data': [{"id": 1, "title": "标签", "fid": 0, "list": [{"id": 2, "title": '二级标签', "fid": 1}]}],
                'title1': '品牌',
                'title2': '分店',
                'width': '400px',
                'height': '500px',
                'val': 'selectValue'
            },
            this.options = $.extend({}, this.defaults, opt),
            this.$body = $('body'),
            this.$class = null,
            this.$value = $(this.options.val),
            this.selectValue = 0,
            this.data = new Array();
        this.$element.attr('readonly','readonly');//.attr('disabled','disabled');
        var valueData = this.$value.val(),
            deleteIndex = 0, i = 0, k = 0, l = 0;
        if (valueData) {
            valueData = JSON.parse(valueData);
            for(i;i<this.options.data.length;i++){
                k=0;
                for(k;k<this.options.data[i].list.length;k++){
                    // console.log(k+':'+this.options.data[i].list.length+":"+this.options.data[i].list[k].id);
                    l=0;
                    for(l;l<valueData.length;l++){
                        if(this.options.data[i].list[k].id) {
                            if (this.options.data[i].list[k].id == valueData[l]) {
                                this.data.push({
                                    id: this.options.data[i].list[k].id,
                                    title: this.options.data[i].list[k].title,
                                    fid: this.options.data[i].list[k].fid
                                });
                                this.options.data[i].list.splice(k, 1);
                                if(k >=0){
                                    k = k - 1;
                                }
                                break;
                            }
                        }
                    }
                }
            }
            // for (i in this.options.data) {
            //     for (k in this.options.data[i].list) {
            //         for (l in valueData) {
            //             if (this.options.data[i].list[k].id == valueData[l]) {
            //                 this.data.push({
            //                     id: this.options.data[i].list[k].id,
            //                     title: this.options.data[i].list[k].title,
            //                     fid: this.options.data[i].list[k].fid
            //                 });
            //                 this.options.data[i].list.splice(k, 1);
            //                 break;
            //             }
            //         }
            //     }
            // }
        }
    };
    Beautifier.prototype = {
        beautify: function () {

            this.$body.append('<div class="multiple-select-list-background"></div><div class="multiple-select-list-box" style="width:' + this.options.width + '; height:' + this.options.height + ';">'
                + '<div class="multiple-select-list-title">'
                + '<ul><li class="multiple-select-list-title-selected" rel="class1">' + this.options.title1 + '</li>'
                + '<li rel="class2">' + this.options.title2 + '</li><li class="multiple-select-list-close"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></li></ul></div>'
                + ' <ul class="multiple-select-list-show" id="class1">' + this.getClass(0) + '</ul>'
                + ' <ul class="multiple-select-list-show" id="class2"></ul>'
                + ' <div class="multiple-select-list-title">'
                + ' <ul><li class="multiple-select-list-selected-title">Selected</li></ul></div>'
                + ' <ul class="multiple-select-list-select">' + this.loadSelected() + '</ul></div>');
            this.$class = this.$body.find('.multiple-select-list-show');
            this.$class.eq(1).hide();


            return this.$element;
        },
        close: function () {
            var strNames = '',
                ids = new Array();

            for(var i in this.data) {
                strNames += this.data[i].title + ',';
                ids.push(this.data[i].id);
            }
            if(strNames) {
                strNames = strNames.substring(0, strNames.length - 1);
            }
            this.$value.val(JSON.stringify(ids));
            this.$element.val(strNames);
            this.$element.attr('title',strNames);
            $('.multiple-select-list-background').remove();
            $('.multiple-select-list-box').remove();
        },
        getClass: function (fid) {
            var strHtml = '';
            for (var key in this.options.data) {
                if (fid == 0) {
                    strHtml += '<li data-id="' + this.options.data[key].id + '" data-fid="' + this.options.data[key].fid + '">' + this.options.data[key].title + '</li>';
                } else if (fid == this.options.data[key].id) {
                    for (var i in this.options.data[key].list) {
                        strHtml += '<li data-id="' + this.options.data[key].list[i].id + '" data-fid="' + this.options.data[key].list[i].fid + '">' + this.options.data[key].list[i].title + '</li>';
                    }
                }
            }
            return strHtml;
        },
        loadSelected: function () {
            var strHtml = '';
            for (var key in this.data) {
                strHtml += '<li data-id="' + this.data[key].id + '" data-fid="' + this.data[key].fid + '" data-title="' + this.data[key].title + '" >' + this.data[key].title + '&nbsp;<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></li>';
            }
            return strHtml;
        }
    };
    $.fn.MultipleSelectList = function(options) {
        var beautifier = new Beautifier(this, options);
        beautifier.close();
        beautifier.$element.click(function () {
            beautifier.beautify();
        });
        beautifier.$body.on('click','.multiple-select-list-title ul li',function() {
            if($(this).attr('rel')!= undefined) {
                beautifier.$class.hide();
                $('.multiple-select-list-title ul li').removeClass('multiple-select-list-title-selected');
                $(this).addClass('multiple-select-list-title-selected');
                $('#' + $(this).attr('rel')).show();
            }
        });
        beautifier.$body.on('click','.multiple-select-list-show li',function() {
            var id = $(this).attr('data-id'), fid = $(this).attr('data-fid');
            if (fid == 0) {
                beautifier.$class.hide();
                beautifier.$class.eq(1).html(beautifier.getClass(id));
                $('.multiple-select-list-title ul li').removeClass('multiple-select-list-title-selected');
                $('.multiple-select-list-title ul li').eq(1).addClass('multiple-select-list-title-selected');
                beautifier.$class.eq(1).show();
                beautifier.selectValue = id;
            } else {
                $('.multiple-select-list-select').append('<li data-id="'+id+'" data-fid="'+fid+'" data-title="'+$(this).html()+'">' + $(this).html() + '&nbsp;<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></li>');
                beautifier.data.push({id: id, fid: fid, title: $(this).html()});
                for (var key in beautifier.options.data) {
                    for (var i in beautifier.options.data[key].list) {
                        if (beautifier.options.data[key].list[i].id == id && beautifier.options.data[key].list[i].fid == fid) {
                            beautifier.options.data[key].list.splice(i,1);
                        }
                    }
                }
                $(this).remove();
            }
        });
        beautifier.$body.on('click', '.multiple-select-list-title ul li.multiple-select-list-close', function () {
            beautifier.close();
        });
        beautifier.$body.on('click','.multiple-select-list-select li button.close',function() {
            var li = $(this).parent(),
                id = li.attr('data-id'),
                fid = li.attr('data-fid'),
                title = li.attr('data-title'),
                key = 0;
            if(beautifier.selectValue == fid) {
                beautifier.$class.eq(1).append('<li data-id="' + id + '" data-fid="' + fid + '" >' + title + '</li>');
            }
            for (key in beautifier.options.data) {
                if (fid == beautifier.options.data[key].id) {
                    beautifier.options.data[key].list.push({id: id, fid: fid, title: title});
                    break;
                }
            }
            for (key in beautifier.data) {
                if (id == beautifier.data[key].id) {
                    beautifier.data.splice(key,1);
                    break;
                }
            }
            li.remove();
        });
        beautifier.$body.on('dblclick','.multiple-select-list-background',function() {
            beautifier.close();
        });
    }
})();