<?php
require($DOCUMENT_ROOT . "connect.inc");
$timestamp = gettimeofday("sec")+28800;

if(isset($_POST["Sort"])){
    $sql = "UPDATE tbl_user SET int_sort = CASE int_id ";
    foreach($_POST as $id => $new_sort){
        if($id == "Sort") continue;
        if($new_sort == "") continue;
        $sql .= "WHEN $id THEN $new_sort ";
    }
    $sql .= "ELSE int_sort END;";
//		var_dump($_POST);die;
//    die($sql);
    mysqli_query($con, $sql) or die($sql);
}

$aryOR = Array();
$sql = "SELECT int_id,txt_name,int_sort FROM tbl_user WHERE int_dept = 2 order by int_sort, txt_login";
$result = mysqli_query($con, $sql) or die($sql);
while($record  = mysqli_fetch_assoc($result)){
    $aryOR[] = $record;
}

//var_dump($aryOR);
?>

<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5" />
    <title>內聯網</title>
    <script src="//cdnjs.cloudflare.com/ajax/libs/json3/3.3.2/json3.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/parser.js"></script>

    <style>
        <!--
        .cssMenu { list-style-type: none; padding: 0; overflow: hidden; background-color: #ECECEC; float:right;}
        .cssMenuItem { float: right;  width:140px; border-right: 2px solid white; }
        .cssMenuItem a { display: block; color: black; text-align: center; padding: 4px; text-decoration: none; }
        .cssMenuItem a:hover { background-color: #BBBBBB; color:white; }

        .cssImportant{ background-color: #CCFFFF }

        div { margin-top:15px; }
        .cssTable1 { border-collapse: collapse;}
        .cssTable1 { border: 2px solid black;}
        .cssTable1 th{  padding:0px; text-align:center; border: 2px solid black; width:100px;}
        .cssTable1 td{  padding:0px; text-align:center; border: 2px solid black;}
        -->

    </style>
</head>
<body>
<div align="center" width="100%">
    <div align="center" style="width:850px;">
        <h1>車期排序</h1>
        <form id="sort" method="POST" >
            <input type="hidden" name="Sort" value="1">
            <div style="margin-bottom:5px; text-align:right">
                <input type="submit" value="更新排序" style="cursor:pointer; position:relative; right:183px;">
            </div>

            <table class="cssTable1" id="table1">
                <tr class="cssImportant">
                    <th style="width:50px;">#</th>
                    <th style="width:300px;">分店名稱</th>
                    <th>排序</th>

                </tr>

                <?php foreach($aryOR as $key => $value){ ?>
                <?php if($key % 2 == 0){ ?>
                <tr>
                    <?php }else {?>
                <tr bgcolor="#DDDDDD">
                    <?php } ?>

                    <td><?=$key+1?></td>
                    <!--                報告名稱-->
                    <td><?=$value['txt_name']?></td>
                    <!--                排序-->
                    <td><input value='<?=$value['int_sort']?>' style="width:50px; text-align:center" name="<?=$value['int_id']?>" oninput="inputcheck(this)" onpropertychange="inputcheck(this)"></td>

                </tr>
                <?php } ?>

        </form>
        </table>


    </div>
</div>

</body>
<script>
    //禁止input框顯示歷史記錄
    $( document ).ready(function() {
        $("input").attr('autocomplete',"off");
    });

    function inputcheck(sender){

        if(sender.value.match(/\D/g)){
            sender.value = sender.value.replace(/\D/g, "");
        }
    }




</script>

</html>