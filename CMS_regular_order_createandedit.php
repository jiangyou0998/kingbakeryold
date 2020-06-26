<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    header('Location: login.php');
}
require("connect.inc");
$timestamp = gettimeofday("sec");

$maxQTY = 600;
$action = $_REQUEST[action];
// $action = $_SESSION['action'];
$menuid = $_REQUEST['menuid'];
// var_dump($menuid);
$rOrderID = $_REQUEST['rOrderID'];

switch ($action) {
    case 'insert':
        $url = 'CMS_regular_order_insert.php';
        break;
    
    case 'edit':
        $url = '';
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

//中間標題相關數據查詢
$titleArr = array();
$sql = "SELECT chr_name,chr_no FROM tbl_order_z_menu WHERE int_id = ". $menuid .";";

$result = mysqli_query($con, $sql);
$titleResult = mysqli_fetch_array($result);
$titleArr = $titleResult;

?>

<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網-創建範本</title>
    <script src="js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">  
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<style type="text/css">
    <!--

    body{
        margin-left: 40px;
        margin-right: 40px;
    }

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
<div align="left"><a target="_top" href="CMS_regular_order.php?menuid=<?= $menuid ?>" style="font-size: xx-large;">返回</a></div>
<!-- <form action="order_z_dept_2.php?action=confirm&dept=<?= $dept ?>" method="post" id="cart" name="cart" target="_top">-->
<div align="middle"><strong><font color="#FF0000" size="+15"><?= $titleArr['chr_no'] ?>&nbsp&nbsp<?= $titleArr['chr_name'] ?>&nbsp&nbsp固定柯打
        </font></strong></div>
<div align="left" style="padding-top: 15px;"><strong><font color="#FF0000" size="+3">選擇星期:
        </font></strong></div>

<?php
switch ($action) {
    //insert時,查找所有已經增加的星期,查詢結果不顯示
    case 'insert':
        $sql = "SELECT group_concat(orderdates) as orderdates FROM regular_orders
        where menu_id = $menuid AND disabled = 0;";

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
        $linecount = 1;
        //星期日到星期六多選框
        foreach ($weekArr as $key => $value) {

            if(in_array($key,$sampledateArr)){
                continue;
            }  
            
            $check = '<label style="padding-right:15px;">';
            $check .= '<input type="checkbox" name="week" value="' . $key . '" /><span class="checkbox">' . $value.'</span>';
            $check .= '</label>';  

            $linecount++;
            if ($linecount >= 4) {
                $check .= '<br>';
                $linecount = 1;
            } 

            echo $check;
            
        }

        echo '<input type="hidden" name="weekstr" id="weekstr" value=""/>';
        break;
    
    //edit時,不顯示除該id外所有星期
    case 'edit':
        $sql = "SELECT group_concat(orderdates) as orderdates FROM regular_orders 
        where menu_id = ". $menuid . " and id <> ".$_REQUEST['rOrderID']." AND disabled = 0;";

        $result = mysqli_query($con, $sql);
        $sampledateResult = mysqli_fetch_array($result);
        //已新增的天數字符串
        $sampledate = $sampledateResult[0];
                // var_dump($sql);
        $sampledateArr = array();
        if ($sampledate != null){
            $sampledateArr =  explode(',', $sampledate);
        }

        $sql = "SELECT group_concat(orderdates) as orderdates FROM regular_orders
        where menu_id = ". $menuid . " and id = ".$_REQUEST['rOrderID']." AND disabled = 0;";

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

        $linecount = 1;
        //星期日到星期六多選框
        foreach ($weekArr as $key => $value) {

            if(in_array($key,$sampledateArr)){
                continue;
            }  
            
            if(in_array($key,$currentdateArr)){
                $check = '<label style="padding-right:15px;">';
                $check .= '<input type="checkbox" name="week" value="' . $key . '" checked /><span class="checkbox">' . $value.'</span>';
                $check .= '</label>';

            }else{
                $check = '<label style="padding-right:15px;">';
                $check .= '<input type="checkbox" name="week" value="' . $key . '" /><span class="checkbox">' . $value.'</span>';
                $check .= '</label>';
                
            }

            $linecount++;
            if ($linecount >= 4) {
                $check .= '<br>';
                $linecount = 1;
            } 
            echo $check;
                
        }

        echo '<input type="hidden" name="weekstr" id="weekstr" value="'.$currentdate.'"/>';
        break;

}

?>



<table class="table table-bordered table-hover">
  <caption>請填寫固定柯打內容</caption>
  
    <?php 

        if ($action == "edit"){
            $sql = "SELECT * FROM regular_order_items WHERE r_order_id = ". $rOrderID ." AND disabled = 0;";
            $result = mysqli_query($con, $sql) or die($sql);
         
            while ($record = mysqli_fetch_assoc($result)) {
               $resultArr[$record['user_id']] = $record ;
            }
        }
        // var_dump($resultArr);

        $sql = "SELECT int_id,chr_report_name FROM tbl_user WHERE chr_type = 2 && chr_report_name <> '' order by txt_login;";

    // var_dump($sql);

        $result = mysqli_query($con, $sql) or die($sql);
        $count = 0;

        $th = "<thead><tr>";
        $td = "<tbody><tr>";
        while ($record = mysqli_fetch_assoc($result)) {
            $th .= "<th>".$record['chr_report_name']."</th>";
            if ($action == "insert"){
                $td .= "<td><input class=\"qty\" type=\"tel\" style=\"width:50px;\" data-id=\"".$record['int_id']."\"></td>";
            }else if($action == "edit"){
                $td .= "<td><input class=\"qty\" type=\"tel\" style=\"width:50px;\" data-id=\"".$record['int_id']."\" value=\"".$resultArr[$record['int_id']]['qty']."\"></td>";
            }
            
        }
        $th .= "</tr></thead>";
        $td .= "</tr></tbody>";

        echo $th;
        echo $td;


     ?>

</table>

<div>
    <button class="btnsubmit" id="btnsubmit" onclick="sss();">提交</button>
</div>

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

    // $(document).on('change', '.qty', function () {
    //     var qty = $(this).val();
    //     var maxQty = <?=$maxQTY?>;
    //     var base = $(this).data('base');
    //     var min = $(this).data('min');
    //     if (qty > maxQty) {
    //         alert("每項目數量最多只可為「" + maxQty + "」");
    //         $(this).val(maxQty);
    //     } else if (qty < min) {
    //         alert("該項目最少落單數量為「" + min + "」");
    //         $(this).val(min);
    //     } else if (qty % base != 0) {
    //         alert("該項目數量必須以「" + base + "」為單位");
    //         var newQty = qty - qty % base;
    //         $(this).val(newQty);
    //     }
    //     ;
    // });




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
        $(".qty").each(function () {
            var userid = $(this).data('id');
            var qty = $(this).val();
            // console.log($qty);
            if (qty > 0 && qty){
                var item = {'userid': userid, 'qty': qty};
                insertarray.push(item);
            }
            
        });

        // console.log(insertarray);
        // $("#btnsubmit").attr('disabled', false);
        // return false;



        $.ajax({
            type: "POST",
            url: "<?= $url ?>",
            data: {
                'menuid'  : "<?= $menuid ?>",
                'orderdates': weekstr,
                'insertData': JSON.stringify(insertarray)
            },
            success: function (msg) {
                alert('範本設置成功!');
                window.location.href = 'CMS_regular_order.php?menuid='+<?= $menuid ?>;
                console.log(msg);
            }
        });

    }
</script>

</body>
</html>
