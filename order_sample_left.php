<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    header('Location: login.php');
}
require("connect.inc");
$timestamp = gettimeofday("sec");

$maxQTY = 300;
$action = $_REQUEST[action];
$order_user = $_SESSION[order_user] ? $_SESSION[order_user] : $_SESSION[user_id];

$weekArr = [
    '0' => '星期日',
    '1' => '星期一',
    '2' => '星期二',
    '3' => '星期三',
    '4' => '星期四',
    '5' => '星期五',
    '6' => '星期六',
];

?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網-創建範本</title>
    <script src="js/jquery.min.js"></script>
</head>
<style type="text/css">
    <!--
    input.qty {
        width: 40%
    }

    }
    -->
</style>
<body>
<div align="left"><a target="_top" href="order_sample.php">返回</a></div>
<!-- <form action="order_z_dept_2.php?action=confirm&dept=<?= $dept ?>" method="post" id="cart" name="cart" target="_top">-->
<div align="right"><strong><font color="#FF0000" size="+3">創建範本
        </font></strong></div>
<div align="right"><strong><font color="#FF0000" size="+3">選擇星期
        </font></strong></div>

<input type="hidden" name="weekstr" id="weekstr" value=""/>

<?php

$sql = "SELECT group_concat(sampledate) as sampledate FROM db_intranet.tbl_order_sample 
        where user_id = $order_user;";

$result = mysqli_query($con, $sql);
$sampledateResult = mysqli_fetch_array($result);
//已新增的天數字符串
$sampledate = $sampledateResult[0];
        // var_dump($sql);
$sampledateArr = array();
if ($sampledate != null){
    $sampledateArr =  explode(',', $sampledate);
}

// var_dump(count($sampledateArr));

//星期日到星期六多選框
foreach ($weekArr as $key => $value) {

    if(in_array($key,$sampledateArr)){
            continue;
    }  

    if(count($sampledateArr) > 0){
        
    }
    
    $check = '<label style="padding-right:15px;">';
    $check .= '<input type="checkbox" name="week" value="' . $key . '" />' . $value;
    $check .= '</label>';

    echo $check;
}

?>
<table width="100%" height="89%" border="1" cellpadding="10" cellspacing="2" id="shoppingcart">
    <tr>
        <td valign="top">
            <table width="100%" border="0" cellspacing="2" cellpadding="2">
 
              <tr class="blankline">
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <? $sql = "SELECT txt_name FROM db_intranet.tbl_user WHERE int_id = $order_user ";
                    $result = mysqli_query($con, $sql) or die($sql);
                    $record = mysqli_fetch_assoc($result);
                    ?>
                    <!-- <td colspan="3" valign="middle">分店：<?= $record[txt_name] ?><br>柯打日期：<?= date('Y/n/j', $timestamp) ?><br>柯打合共：<?= $count; ?></td> -->
                    <td colspan="6" align="center"><input id="btnsubmit" name="Input" type="image"
                                                          src="images/Finish.jpg" border="0" onClick="sss();"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- </form>-->
<script>
    $(document).on('click', '.qty', function () {

        var u = navigator.userAgent;
        if (u.indexOf('iPhone') > -1 || u.indexOf('iPad') > -1) {
            // ios端的方法
            this.selectionStart = 0;
            this.selectionEnd = this.val().length;
        } else {
            // pc和安卓端的方法
            $(this).focus().select();
        }

    });

    $(document).on('change', '.qty', function () {
        var qty = $(this).val();
        var maxQty = <?=$maxQTY?>;
        var base = $(this).data('base');
        var min = $(this).data('min');
        if (qty > maxQty) {
            alert("每項目數量最多只可為「" + maxQty + "」");
            $(this).val(maxQty);
        } else if (qty < min) {
            alert("該項目最少落單數量為「" + min + "」");
            $(this).val(min);
        } else if (qty % base != 0) {
            alert("該項目數量必須以「" + base + "」為單位");
            var newQty = qty - qty % base;
            $(this).val(newQty);
        }
        ;
    });

    //刪除(x按鈕),隱藏相應行,原本已經存在的
    $(document).on('click', '.del', function () {
        var parent = $(this).parents(".cartold");
        var parentClass = parent.attr("class");
        parent.removeClass(parentClass).addClass("cartdel");
        parent.hide();
        // console.log(parent.attr("class"));

    });

    //刪除(x按鈕),隱藏相應行,新增的行
    $(document).on('click', '.delnew', function () {
        var parent = $(this).parents(".cart");
        var parentClass = parent.attr("class");
        parent.remove();
        // console.log(parent.attr("class"));

    });

    //鉤選或取消時,修改weekstr(隱藏)的值
        $(document).on('change', 'input[type=checkbox]', function () {
            var weekstr = $('input[type=checkbox]:checked').map(function () {
                return this.value
            }).get().join(',');
            $('#weekstr').val(weekstr);
            // alert(weekstr);
        });

    //點擊完成按鈕提交修改
    function sss() {
        //禁止按鈕重複點擊
        $("#btnsubmit").attr('disabled', true);

        var weekstr = $('#weekstr').val();
        if (weekstr == "") {
            alert("請選擇範本日期！");
            $("#btnsubmit").attr('disabled', false);
            return false;
        }

        var insertarray = [];
        //insert
        $(".cart").each(function () {

            var id = $(this).attr('id');

            var itemid = $(this).data('itemid');
            // console.log($id);

            var qty = $("#qty" + id).val();
            // console.log($qty);

            var item = {'itemid': itemid, 'qty': qty};
            insertarray.push(item);

        });

        var updatearray = [];
        //insert
        $(".cartold").each(function () {

            var id = $(this).attr('id');

            var mysqlID = $(this).data('mysqlid');

            var itemid = $(this).data('itemid');
            // console.log($id);

            var qty = $("#qty" + id).val();
            // console.log($qty);

            var item = {'mysqlid': mysqlID, 'qty': qty};
            updatearray.push(item);

        });

        var delarray = [];
        //insert
        $(".cartdel").each(function () {

            var mysqlID = $(this).data('mysqlid');

            var item = {'mysqlid': mysqlID};
            delarray.push(item);

        });
        console.log(weekstr);
        console.log(JSON.stringify(insertarray));
        console.log(JSON.stringify(updatearray));
        console.log(JSON.stringify(delarray));

        $.ajax({
            type: "POST",
            url: "order_sample_insert.php",
            data: {
                'sampledate': weekstr,
                'insertData': JSON.stringify(insertarray),
                'updateData': JSON.stringify(updatearray),
                'delData'   : JSON.stringify(delarray)
            },
            success: function (msg) {
                // alert('已落貨!');
                // window.location.reload();
                console.log(msg);
            }
        });

    }
</script>


</body>
</html>