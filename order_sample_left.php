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
$action = $_SESSION['action'];
$sampleid = $_SESSION['sampleid'];

switch ($action) {
    case 'insert':
        $url = 'order_sample_insert.php';
        break;
    
    case 'edit':
        $url = 'order_sample_edit.php';
        break;
}


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

    input[type="checkbox"]{
  width: 30px; /*Desired width*/
  height: 30px; /*Desired height*/
}

    .checkbox{
        font-size: 30px;
        margin-bottom: 10px;
    }

    }
    -->
</style>
<body>
<div align="left"><a target="_top" href="order_sample.php" style="font-size: larger;">返回</a></div>
<!-- <form action="order_z_dept_2.php?action=confirm&dept=<?= $dept ?>" method="post" id="cart" name="cart" target="_top">-->
<div align="middle"><strong><font color="#FF0000" size="+15">創建範本
        </font></strong></div>
<div align="left" style="padding-top: 15px;"><strong><font color="#FF0000" size="+3">選擇星期:
        </font></strong></div>

<?php

switch ($action) {
    //insert時,查找所有已經增加的星期,查詢結果不顯示
    case 'insert':
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
            
            $check = '<label style="padding-right:15px;">';
            $check .= '<input type="checkbox" name="week" value="' . $key . '" /><span class="checkbox">' . $value.'</span>';
            $check .= '</label>';

            echo $check;
            
        }

        echo '<input type="hidden" name="weekstr" id="weekstr" value=""/>';
        break;
    
    //edit時,不顯示除該id外所有星期
    case 'edit':
        $sql = "SELECT group_concat(sampledate) as sampledate FROM tbl_order_sample 
        where user_id = ".$order_user." and id <> ".$sampleid.";";

        $result = mysqli_query($con, $sql);
        $sampledateResult = mysqli_fetch_array($result);
        //已新增的天數字符串
        $sampledate = $sampledateResult[0];
                // var_dump($sql);
        $sampledateArr = array();
        if ($sampledate != null){
            $sampledateArr =  explode(',', $sampledate);
        }

        $sql = "SELECT sampledate as sampledate FROM tbl_order_sample 
        where user_id = ".$order_user." and id = ".$sampleid.";";

        $result = mysqli_query($con, $sql);
        $currentdateResult = mysqli_fetch_array($result);
        //當前範本的天數字符串
        $currentdate = $currentdateResult[0];
                // var_dump($sql);
        $currentdateArr = array();
        if ($currentdate != null){
            $currentdateArr =  explode(',', $currentdate);
        }

        // var_dump($sampledateArr);
        // var_dump($currentdateArr);

        //星期日到星期六多選框
        foreach ($weekArr as $key => $value) {

            if(in_array($key,$sampledateArr)){
                continue;
            }  
            
            if(in_array($key,$currentdateArr)){
                $check = '<label style="padding-right:15px;">';
                $check .= '<input type="checkbox" name="week" value="' . $key . '" checked /><span class="checkbox">' . $value.'</span>';
                $check .= '</label>';
                echo $check;
            }else{
                $check = '<label style="padding-right:15px;">';
                $check .= '<input type="checkbox" name="week" value="' . $key . '" /><span class="checkbox">' . $value.'</span>';
                $check .= '</label>';
                echo $check;
            }
                
        }

        echo '<input type="hidden" name="weekstr" id="weekstr" value="'.$currentdate.'"/>';
        break;

}

?>
<table width="100%" height="89%" border="1" cellpadding="10" cellspacing="2" id="shoppingcart">
    <tr>
        <td valign="top">
            <table width="100%" border="0" cellspacing="2" cellpadding="2">

<?php
    if ($action == 'edit'){
        $sql = "
            SELECT 
            
            tbl_order_z_menu.int_id AS itemID,
            tbl_order_sample_item.id AS sampleItemID,
            tbl_order_z_menu.chr_name AS itemName,
            tbl_order_z_menu.chr_no,
            tbl_order_z_unit.chr_name AS UoM,
            tbl_order_z_menu.chr_cuttime,
            tbl_order_z_menu.int_phase,
            LEFT(tbl_order_z_cat.chr_name, 2) AS suppName,
            tbl_order_sample_item.qty,
            tbl_order_z_menu.int_base,
            tbl_order_z_menu.int_min
            
            FROM
                tbl_order_sample_item
                    INNER JOIN tbl_order_sample ON tbl_order_sample_item.sample_id = tbl_order_sample.id
                    INNER JOIN tbl_order_z_menu ON tbl_order_sample_item.menu_id = tbl_order_z_menu.int_id
                    INNER JOIN tbl_order_z_unit ON tbl_order_z_menu.int_unit = tbl_order_z_unit.int_id
                    INNER JOIN tbl_order_z_group ON tbl_order_z_menu.int_group = tbl_order_z_group.int_id
                    INNER JOIN tbl_order_z_cat ON tbl_order_z_group.int_cat = tbl_order_z_cat.int_id
            WHERE
                tbl_order_sample.user_id = $order_user
                AND tbl_order_sample.id = $sampleid
                AND tbl_order_sample_item.disabled = 0
                    
            ORDER BY tbl_order_z_menu.chr_no;";

    // var_dump($sql);

        $result = mysqli_query($con, $sql) or die($sql);
            $count = 0;

                while ($record = mysqli_fetch_assoc($result)) {
                    if ($count & 1) {
                        $bg = "#F0F0F0";
                    } else {
                        $bg = "#FFFFFF";
                    }
                    $count += 1;
                    ?>
                    <tr bgcolor="<?php echo $bg; ?>" class="cartold" id="<?= "$record[chr_no]"; ?>"
                        data-itemid="<?= $record['itemID']; ?>"
                        data-mysqlid="<?= $record['sampleItemID'];?>">
                        <td width="10" align="right"><?= $count; ?>.</td>
                        <td><font color="blue"
                                  size=-1><?= $record['suppName']; ?> </font><?= "$record[itemName], $record[chr_no]"; ?>
                        </td>
                        <td align="center"></td>
                        <td width="100" align="center">x
                            <input class="qty" type="tel"
                                   id="qty<?= "$record[chr_no]"; ?>"
                                   name=""
                                   type="text" value="<?= round($record['qty'], 2)?>"
                                   data-base="<?= ($record['int_base']); ?>"
                                   data-min="<?= ($record['int_min']); ?>"
                                   size="3" maxlength="4"
                                   autocomplete="off"
                            >
                        </td>
                        <td align="center"><?= $record['UoM']; ?></td>
                        <td align="center">
                            <?php if ($haveoutdate == 0 || $_SESSION[type] == 3)
                                echo "<a href=\"#\" class=\"del\"><font color=\"#FF6600\">X</font></a>";
                            ?>

                        </td>
                    </tr>
                <?php  
                    }
        }
                ?>
              <tr class="blankline">
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <? $sql = "SELECT txt_name FROM db_intranet.tbl_user WHERE int_id = $order_user ";
                    $result = mysqli_query($con, $sql) or die($sql);
                    $record = mysqli_fetch_assoc($result);
                    ?>
                    <!-- <td colspan="3" valign="middle">分店：<?= $record[txt_name] ?><br>柯打日期：<?= date('Y/n/j', $timestamp) ?><br>柯打合共：<?= $count; ?></td> -->
                    <td colspan="6" align="center">
                        <input id="btnsubmit" name="Input" type="image"
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
            url: "<?= $url ?>",
            data: {
                'sampleid'  : "<?= $sampleid ?>",
                'sampledate': weekstr,
                'insertData': JSON.stringify(insertarray),
                'updateData': JSON.stringify(updatearray),
                'delData'   : JSON.stringify(delarray)
            },
            success: function (msg) {
                alert('範本設置成功!');
                // $(location).attr('href', 'order_sample.php');
                // window.location.reload('order_sample.php');
                top.location.href = 'order_sample.php';
                console.log(msg);
            }
        });

    }
</script>


</body>
</html>