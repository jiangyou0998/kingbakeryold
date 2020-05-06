<?php
/*
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'CMS_order_c_add.php';
    header('Location: login.php');
}
*/

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");
$timestamp = gettimeofday("sec") + 28800;
$type = 1;

$aryA = Array();
$area_sql = "SELECT * FROM tbl_order_z_area ORDER BY int_sort;";
$result = mysqli_query($con, $area_sql) or die($area_sql);
while ($record = mysqli_fetch_assoc($result)) {
    $aryA[] = $record;
}

$aryS = Array();
$shop_sql = "SELECT int_id, chr_code, CONCAT(chr_code,' ',chr_name) AS 'shopName', int_area FROM tbl_district ORDER BY int_area, RIGHT(CONCAT('000',chr_code),4)";
$result = mysqli_query($con, $shop_sql) or die($shop_sql);
while ($record = mysqli_fetch_assoc($result)) {
    $aryS[] = $record;
}

$aryCat = Array();
$sql = "SELECT CAT.int_id AS CAT_id, CAT.chr_name AS CAT_name FROM tbl_order_z_cat CAT WHERE status <> 4 ORDER BY int_sort";
$result = mysqli_query($con, $sql) or die($sql);
while ($record = mysqli_fetch_assoc($result)) {
    $aryCat[] = $record;
}

$aryGp = Array();
$sql = "SELECT * FROM tbl_order_z_group WHERE status <> 4 ";
$sql .= "Order by int_sort;";
$result = mysqli_query($con, $sql) or die($sql);
while ($record = mysqli_fetch_assoc($result)) {
    $aryGp[] = $record;
}

$aryMenu = Array();
$sql = "SELECT * FROM tbl_order_z_menu WHERE status <> 4 ";
$sql .= "Order by int_sort;";
$result = mysqli_query($con, $sql) or die($sql);
while ($record = mysqli_fetch_assoc($result)) {
    $aryMenu[] = $record;
    //print_r($aryMenu);
}


if (isset($_GET["action"])) {
    if ($_GET["action"] == "update") {
        $report = null;
        $sql = "SELECT * FROM tbl_order_check WHERE int_id = $_GET[id] AND disabled = 0;";
        $result = mysqli_query($con, $sql);
        $report = mysqli_fetch_assoc($result);

        $selectedItem = explode(", ", $report[chr_item_list]);
        if ($report[chr_shop_list] != null)
            $selectedShop = explode(", ", $report[chr_shop_list]);

        foreach ($selectedItem as $value) {
            $sort = explode(":", $value);
            $arySort[] = ("( $sort[0], $sort[1] )");
        }
        $search_id = join(", ", $arySort);

        $sql_d = "drop temporary table if exists tmp;";
        $sql_1 = "create temporary table tmp( sort int, id int);";
        $sql_2 = "insert into tmp VALUES $search_id;";
        $sql_3 = "SELECT id, sort FROM tmp ORDER BY sort;";
        mysqli_query($con, $sql_d) or die ($sql_d);
        mysqli_query($con, $sql_1) or die ($sql_1);
        mysqli_query($con, $sql_2) or die ($sql_2);
        $result = mysqli_query($con, $sql_3) or die($sql_3);
        mysqli_query($con, $sql_d) or die ($sql_d);
        $selectedItem = Array();
        while ($item = mysqli_fetch_assoc($result)) {
            $selectedItem[] = $item;
        }

        $sql = "SELECT * FROM tbl_order_z_print_time WHERE int_report_id = $_GET[id];";
        $result = mysqli_query($con, $sql);
        $print_date = mysqli_fetch_array($result);
    }
}
?>

<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網</title>
    <script src="//cdnjs.cloudflare.com/ajax/libs/json3/3.3.2/json3.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/parser.js"></script>
    <script type='text/javascript' src="js/MultipleSelect/multiple-select.js"></script>
    <script type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
    <link rel="stylesheet" type="text/css" href="js/MultipleSelect/multiple-select.css">
    <link href="css/checkbox-style.css" rel="stylesheet" type="text/css"/>
    <style type="text/css">
        <!--
        .cssTable1 {
            border-collapse: collapse;
            border: 2px solid black;
            position: absolute;
            left: 80px;
            top: 70px;
        }

        .cssTable1 .cssTableField {
            width: 150px;
            border: 2px solid black;
            padding: 10px;
        }

        .cssTable1 .cssTableInput {
            width: 300px;
            border: 2px solid black;
            padding: 10px;
        }

        .cssTable2 {
            border-collapse: collapse;
            border: 2px solid black;
            position: absolute;
            left: 585px;
            top: 70px;
            margin-left: 10px;
            background-color: #697CAF;
            cursor: default;
        }

        .cssTable2 .cssTableField {
            width: 150px;
            border: 2px solid black;
            padding: 10px;
        }

        .cssTable2 .cssTableInput {
            width: 600px;
            border: 2px solid black;
            padding: 5px;
        }

        .cssTable3 {
            border-collapse: collapse;
            border: 2px solid black;
            position: absolute;
            left: 595px;
            top: 70px;
            background-color: #697CAF;
            cursor: default;
            display: none;
        }

        .cssTable3 .cssTableInput {
            width: 600px;
            border: 2px solid black;
            padding: 5px;
        }

        .brand {
            text-align: center;
            cursor: pointer;
            color: white;
            background: url("images/2.jpg");
            width: 106;
            height: 35;
            text-align: center;
            float: left;
            padding-top: 5px;
        }

        .brand_all {
            float: left;
            text-align: center;
            cursor: pointer;
            color: white;
            background: url("images/3.jpg");
            width: 106;
            height: 35;
            text-align: center;
            margin: 1px;
            display: none;
            padding-top: 5px;
        }

        .shop {
            float: left;
            text-align: center;
            cursor: pointer;
            color: white;
            background: url("images/1.jpg");
            width: 106;
            height: 60;
            text-align: center;
            margin: 1px;
            display: none;
            padding-top: 5px;
        }

        .shop_select {
            background: url("images/6.jpg");
        }

        .cat {
            text-align: center;
            cursor: pointer;
            color: white;
            background: url("images/2.jpg");
            width: 106;
            height: 35;
            text-align: center;
            padding-top: 5px;
        }

        .gp {
            float: left;
            text-align: center;
            cursor: pointer;
            color: white;
            background: url("images/3.jpg");
            width: 106;
            height: 35;
            text-align: center;
            margin: 1px;
            display: none;
            padding-top: 5px;
        }

        .item {
            float: left;
            text-align: center;
            cursor: pointer;
            color: white;
            background: url("images/1.jpg");
            width: 106;
            height: 65;
            text-align: center;
            margin: 1px;
            display: none;
            padding-top: 5px;
        }

        .item_selected {
            background: url("images/6.jpg");
        }


        #item_list {
            width: 100%;
        }

        .item_list_th {
            text-align: center;
            font-weight: bold;
        }

        .item_list_td {
            width: 150px;
        }

        .item_list_td_1 {
            text-align: center;
        }

        .item_delete {
            width: 25px;
            text-align: center;
        }

        .tab1 {
            position: absolute;
            left: 595px;
            top: 30px;
            width: 100px;
            height: 38px;
            border: 2px solid black;
            text-align: center;
            cursor: pointer;
            padding: 0px;
        }

        .tab2 {
            position: absolute;
            left: 697px;
            top: 30px;
            width: 100px;
            height: 38px;
            border: 2px solid black;
            text-align: center;
            cursor: pointer;
            padding: 0px;
        }

        .active {
            background-color: yellow;
        }

        .cssMenu {
            list-style-type: none;
            padding: 0;
            overflow: hidden;
            background-color: #ECECEC;
        }

        .cssMenuItem {
            float: right;
            width: 140px;
            border-right: 2px solid white;
        }

        .cssMenuItem a {
            display: block;
            color: black;
            text-align: center;
            padding: 4px;
            text-decoration: none;
        }

        .cssMenuItem a:hover {
            background-color: #BBBBBB;
            color: white;
        }

        .cssImportant {
            background-color: #CCFFFF;
        }

        .ms-drop .container.checkbox-help {
            top: 0px;
            left: 0px;
        }

        .ms-drop span.text {
            margin-left: 25px;
        }

        .checkmark {
            height: 18px;
            width: 18px;
        }

        .container .checkmark:after {
            left: 6px;
            top: 1px;
            width: 5px;
            height: 11px;
        }

        .ms-drop ul > li {
            height: 25px;
        }

        .ms-drop ul > li.multiple label {
            height: 25px;
        }

        -->
    </style>
    <script>
        $(function () {
            $("#email_week").multipleSelect({
                selectAllText: '所有',
                allSelected: '每天',
                countSelected: '已選擇 # 項',
                minimumCountSelected: 6,
                multiple: true,
                multipleWidth: 85,
                onClose: function () {
                    $("#email_weel_val").val($("#email_week").multipleSelect('getSelects').join(','));
                }
            });
            $("#email_week").multipleSelect('setSelects', [<?=$print_date[chr_weekday]?>]);
        });
        var WdatePickerOpt2 = {
            dateFmt: 'HH:mm',
            isShowClear: false,
            vel: 'time',
            onpicked: function (dp) {

            }
        };
    </script>
</head>

<body>

<div style="width:95%;">
    <?php if ($_GET["action"] == "update") { ?>
        <h1 id="title" style="position:absolute; left:95px;">修改報告</h1>
    <?php } else { ?>
        <h1 id="title" style="position:absolute; left:95px;">建立新報告</h1>
    <?php } ?>
    <div class="tab1 active">項目</div>
    <!-- <div class="tab2" >分店</div> -->

    <table class="cssTable1">
        <tr>
            <th class="cssTableField cssImportant">報告名稱</th>
            <td class="cssTableInput"><input type="text" id="report_name"></td>
        </tr>
        <tr>
            <th class="cssTableField cssImportant" valign="top">報表 日期</th>
            <td class="cssTableInput">
                星期:
                <select type="text" id="email_week" style="width:173px;" multiple>
                    <option value="0">星期一</option>
                    <option value="1">星期二</option>
                    <option value="2">星期三</option>
                    <option value="3">星期四</option>
                    <option value="4">星期五</option>
                    <option value="5">星期六</option>
                    <option value="6">星期日</option>
                </select>
                <input type="hidden" id="email_weel_val" value="<?= $print_date[chr_weekday] ?>"/>
                <br/>
                時間:
                <input type="text" id="email_time" readonly onclick="WdatePicker(WdatePickerOpt2);" style="width:173px;"
                       value="<?= $print_date[chr_time] ?>"/>
            </td>
        </tr>
        <tr>
            <th class="cssTableField cssImportant" valign="top">相隔日數</th>
            <td class="cssTableInput"><input type="text" id="num_of_day" oninput="inputChange(this)"
                                             onpropertychange="inputChange(this)" value="1"></td>
        </tr>
        <!--
        <tr>
            <th class="cssTableField cssImportant" valign="top">分開加單</th>
            <td class="cssTableInput">
                <input type="radio" name="separate" value="1" checked id="separate_t">是
                <input type="radio" name="separate" value="0" id="separate_f" style="margin-left:40px">否
            </td>
        </tr>
        <tr>
            <th class="cssTableField cssImportant" valign="top">車牌</th>
            <td class="cssTableInput">
                <input type="radio" name="car" value="1" checked id="car_t">公司車
                <input type="radio" name="car" value="0" id="car_f" style="margin-left:8px">街車
            </td>
        </tr>
        -->
        <tr>
            <th class="cssTableField cssImportant" valign="top">隱藏總數為0的項目</th>
            <td class="cssTableInput">
                <input type="radio" name="hide" value="1" checked id="hide_t">是
                <input type="radio" name="hide" value="0" id="hide_f" style="margin-left:40px">否
            </td>
        </tr>
        <tr>
            <th class="cssTableField cssImportant" valign="top">欄位</th>
            <td class="cssTableInput">
                <input type="radio" name="col" value="1" checked id="main_item">項目
                <input type="radio" name="col" value="0" id="main_shop" style="margin-left:25px">分店
            </td>
        </tr>
        <!--
        <tr>
            <th class="cssTableField cssImportant" valign="top">顯示未確定的項目</th>
            <td class="cssTableInput">
                <input type="radio" name="showNC" value="1" id="showNC_t">是
                <input type="radio" name="showNC" value="0" id="showNC_f" style="margin-left:40px" checked>否
            </td>
        </tr>
        -->
        <tr>
            <th class="cssTableField cssImportant" valign="top">排序</th>
            <td class="cssTableInput"><input style="width:50px;" id="sort" value="1" oninput="inputChange(this)"
                                             onpropertychange="inputChange(this)"></td>
        </tr>
        <!--
        <tr>
            <th class="cssTableField cssImportant" valign="top">分店</th>
            <td class="cssTableInput">
            <div style="height:120px; overflow:scroll; overflow-y:scroll; overflow-x:hidden;" id="shop_message">
            </div>
            </td>
        </tr>
        -->
        <tr>
            <th class="cssTableField cssImportant" valign="top">項目</th>
            <td class="cssTableInput">
                <div style="height:362px; overflow:scroll; overflow-y:scroll; overflow-x:hidden;">
                    <table id="item_list">

                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <!-- <img src="./images/Confirm2.jpg" onclick="console.log(reportInfo());" style="cursor:pointer"> -->
                <img src="./images/Confirm2.jpg" onclick="submit()" style="cursor:pointer">
                <img src="./images/Return.jpg" onclick="document.location='CMS_order_c_check_list.php';"
                     style="cursor:pointer">
            </td>
        </tr>
    </table>

    <table class="cssTable2">
        <tr style="color:white;">
            <th class="cssTableField">按貨品編號查找</th>
            <td class="cssTableInput" valign="middle" style="width:300px;">
                <div>
                    <input type="text" id="search_no" style="width:150px;"
                           onkeypress='search_code($("#search_no").val(), event);'>
                    <button onclick='search_code($("#search_no").val());'>查找</button>
                </div>
            </td>
        </tr>
        <tr>
            <td class="cssTableInput" valign="top" colspan="2">
                <table>
                    <tr>
                        <?php foreach ($aryCat as $key => $value) { ?>
                            <td class="cat" id="<?= $value[CAT_id] ?>" background="images/2.jpg" valign="top">
                                <div>
                                    <?= $value[CAT_name] ?>
                                </div>
                            </td>
                            <?php if (($key + 1) % 5 == 0) {
                                echo "</tr>";
                                echo "<tr>";
                            }
                            ?>
                        <?php } ?>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="cssTableInput" style="height:200px;" valign="top" colspan="2">
                <?php foreach ($aryGp as $value) { ?>
                    <div id="<?= $value[int_id] ?>" class="gp cat_<?= $value[int_cat] ?>"><?= $value[chr_name] ?></div>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td class="cssTableInput" valign="top" colspan="2">
                <div style="float:left; height:20px; width:30px; background-color:#00356B"></div>
                <span style="float:left; color:white">可選擇</span>
                <div style="float:left; height:20px; width:30px; margin-left:50px; background-color:#717171"></div>
                <span style="color:white">已選擇</span><br><br>

                <div style="height:369px; overflow:scroll; overflow-y:scroll; overflow-x:hidden;">
                    <?php foreach ($aryMenu as $value) { ?>
                        <div id="item_<?= $value[int_id] ?>"
                             class="item gp_<?= $value[int_group] ?>"><?= $value[chr_name] ?><input type="hidden"
                                                                                                    id="code_<?= $value[chr_no] ?>"
                                                                                                    value="<?= $value[chr_no] ?>"
                                                                                                    class="no"></div>
                    <?php } ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="cssTable3">
        <tr>
            <td class="cssTableInput" style="height:100px;" valign="top">
                <div class="custom">
                    <?php foreach ($aryA as $area) { ?>
                        <div class="brand th" valign="center" id="<?= $area[int_id] ?>">
                            <span><?= $area[chr_name] ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div style="float:right; color:white; padding:2px;">
                    <input type="checkbox" id="choose_all" checked/>不篩選
                </div>
            </td>
        </tr>
        <tr>
            <td class="cssTableInput" style="height:100px;" valign="top">
                <div class="brand_all" id="brand_all" valign="center" name="">
                    全選
                </div>
                <div class="brand_all" id="brand_all_none" valign="center" name="">
                    全不選
                </div>
            </td>
        </tr>
        <tr>
            <td class="cssTableInput" valign="top">
                <div style="float:left; height:20px; width:30px; background-color:#00356B"></div>
                <span style="float:left; color:white">可選擇</span>
                <div style="float:left; height:20px; width:30px; margin-left:50px; background-color:#717171"></div>
                <span style="color:white">已選擇</span><br><br>

                <div class="custom">
                    <div style="height:461px; overflow:scroll; overflow-y:scroll; overflow-x:hidden;">
                        <?php foreach ($aryS as $value) { ?>
                            <div id="<?= $value[int_id] ?>" class="shop area_<?= $value[int_area] ?> shop_select">
                                <?= $value[shopName] ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>


</div>

<form action="CMS_order_c_check_save.php" method="POST" id="add_check">
    <input type="hidden" id="type" name="type" value="<?= $_REQUEST['type'] ?>">
    <input type="hidden" id="action" name="action">
    <input type="hidden" id="report_id" name="report_id">
    <input type="hidden" id="report_info" name="report_info">
</form>
</body>
<script>
    $(document).ready(function () {
        <?php if($_GET["action"] == "update"){?>
        $("#title").html("修改報告");
        <?php } ?>

        $(".cssTable2").show();
        $(".cssTable3").hide();

        $("#shop_message").html(_shopMessage());

        //brand//
        $("#custom").change(function () {
            $(".brand").removeClass("brand_select")
        });
        $("#all").change(function () {
            $(".brand").addClass("brand_select")

        });

        $(".brand").click(function () {
            $(".shop").hide();
            $(".area_" + this.id).show();
            $(".brand_all").show();
            $(".brand_all").prop("name", "area_" + this.id);
        });
        $(".brand_all").click(function () {
            $("#choose_all").removeAttr('checked');
            if (this.id == "brand_all") {
                $("." + this.name).addClass("shop_select");
            } else if (this.id == "brand_all_none") {
                $("." + this.name).removeClass("shop_select");
            }

            $("#shop_message").html(_shopMessage());
        });
        $(".shop").click(function () {
            $("#choose_all").removeAttr('checked');
            if ($(this).attr("class").match(/shop_select/g)) {
                $(this).removeClass("shop_select");
            } else {
                $(this).addClass("shop_select");
            }

            var brand_name = this.className.match(/brand_[a-z]+/g);
            var brand = $("." + brand_name).length;
            var selectShop = $("." + brand_name + ".shop_select").length;

            if (brand != selectShop) {
                $("#" + brand_name).removeClass("brand_select");
            } else {
                $("#" + brand_name).addClass("brand_select");
            }

            $("#shop_message").html(_shopMessage());
            //alert($(".shop_select").length);
        });

        $("#choose_all").change(function () {
            if (this.checked) {
                $(".shop").addClass("shop_select");
            }
            $("#shop_message").html(_shopMessage());
        });
        //brand//

        //item//
        $(".cat").click(function () {
            $(".gp").hide();
            $(".gp.cat_" + this.id).show();
        });
        $(".gp").click(function () {
            $(".item").hide();
            $(".item.gp_" + this.id).show();
        });
        $(".item").click(function () {
            //console.log(this.id);
            if ($(this).attr("class").match(/item_selected/g)) {
                $(this).removeClass("item_selected");
                var v = findElementsByName(this.id);
                _deleteRow(v[0]);
                return;
            }

            $(this).addClass("item_selected");
            addToItemList(this);
        });
        //item//
        $(".tab1").click(function () {
            $(".cssTable3").hide();
            $(".cssTable2").show();
            $(".tab2").removeClass("active");
            $(this).addClass("active");
        });
        $(".tab2").click(function () {
            $(".cssTable3").show();
            $(".cssTable2").hide();
            $(".tab1").removeClass("active");
            $(this).addClass("active");
        });

        <?php if($_GET["action"] == "update"){?>
        restoreReportData()
        <?php } ?>
    });

    function _deleteRow(r) {
        $("#" + r.name).removeClass("item_selected");
        var i = r.parentNode.parentNode.rowIndex;

        document.getElementById("item_list").deleteRow(i);
        var v = $("b.count");
        for (i = 0; i < v.length; i++) {
            v[i].innerHTML = i + 1 + ".";
        }
    }

    function _deleteRow2(r) {
        $("#" + r.name).removeClass("shop_select");
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("shop_list").deleteRow(i);
    }

    function _shopMessage() {
        var result = "";
        var shopNum = $(".shop").length;
        var selectNum = $(".shop_select").length;

        if (shopNum == selectNum) {
            result = "已選擇全部分店";
        } else if (selectNum == 0) {
            result = "沒有選擇任何分店";
        } else {
            var c = $(".shop_select");
            result = "<table id='shop_list' style='width:98%'>";
            for (i = 0; i < c.length; i++) {
                result += "<tr>";
                result += "<td>";
                result += c[i].innerHTML + "<br>";
                result += "</td>";
                result += "<td align='right'>";
                result += "<img src='images/delete.png' style='cursor:pointer;' onclick='_deleteRow2(this)' name='" + c[i].id + "'>";
                result += "</td>";
                result += "</tr>";
            }

            result += "</table>";
        }

        return result;
    }

    function inputChange(s) {
        if (s.value.match(/\D+/g)) {
            s.value = s.value.replace(/\D+/g, "");
        }
    }

    function addToItemList(obj, sort) {
        var s = 1;
        if (sort != null)
            s = sort;

        var count = $("#item_list")[0].rows.length;
        if (count == 0) {
            var r = $("#item_list")[0].insertRow();
            var c1 = r.insertCell(0);
            var c2 = r.insertCell(1);
            var c3 = r.insertCell(2);
            var c4 = r.insertCell(3);

            c1.className = c2.className = c3.className = c4.className = "item_list_th";

            c1.innerHTML = "#";
            c2.innerHTML = "項目";
            c3.innerHTML = "排序";
            c4.innerHTML = "刪除";
            count++;
        }

        var no = $("#" + obj.id + " > .no")[0].value;
        var n = obj.innerHTML.split(/<input/ig)[0];

        var r = $("#item_list")[0].insertRow();
        var c1 = r.insertCell(0);
        var c2 = r.insertCell(1);
        var c3 = r.insertCell(2);
        var c4 = r.insertCell(3);
        c4.className = "item_delete";
        c2.className = "item_list_td";
        c3.className = "item_list_td_1";

        c1.innerHTML = "<b class='count'>" + count + ".</b>";
        c2.innerHTML = n + ", " + no;
        c3.innerHTML = "<input type='text' style='width:50px;' id='sort_" + obj.id + "' value='" + s + "'>";
        c4.innerHTML = "<img src='images/delete.png' style='cursor:pointer;' onclick='_deleteRow(this)' name='" + obj.id + "'>";

    }

    function search_code(v, e) {
        console.log(e);
        if ((e != null && e.charCode == 13) || e == null) {
            var s = $("#code_" + v)
            var sl = s.length;
            if (sl >= 1 && v != "") {
                var cat = $("#" + s[0].parentNode.className.split(" ")[1].split("_")[1])[0].className.split(" ")[1];
                var gp = s[0].parentNode.className.split(" ")[1].split("_")[1];
                var item = s[0].click();
                $(".gp").hide();
                $(".gp." + cat).show();
                $(".item").hide();
                $(".item.gp_" + gp).show();
            } else {
                alert("沒有找到任何項目");
            }
        }
    }

    <?php if($_GET["action"] == "update"){?>
    function restoreReportData() {
        $("#report_name").val("<?=$report[chr_report_name]?>")
        $("#num_of_day").val("<?=$report[int_num_of_day]?>")
        $("#sort").val("<?=$report[int_sort]?>")

        if (<?=$report[int_hide]?> == 0
    )
        {
            $("#hide_f").prop("checked", true);
        }
        if (<?=$report[int_main_item]?> == 0
    )
        {
            $("#main_shop").prop("checked", true);
        }

        <?php for($i = 0;$i < count($selectedItem);$i++) { ?>
        <?php $value = $selectedItem[$i];?>
        var item_id = "item_" + <?=$value[id]?> ;
        var item = document.getElementById(item_id);
        if (item != null) {
            addToItemList(document.getElementById(item_id), <?=$value['sort']?>);
            $("#" + item_id).addClass("item_selected");
        }
        <?php } ?>



        <?php
        if ($report[int_all_shop] == 1) {
            echo '$(".shop").addClass("shop_select");';
        }
        if ($report[int_all_th] == 1) {
            echo '$(".shop.brand_th").addClass("shop_select");';
        }
        if ($report[int_all_tw] == 1) {
            echo '$(".shop.brand_tw").addClass("shop_select");';
        }
        if ($report[int_all_ctc] == 1) {
            echo '$(".shop.brand_ctc").addClass("shop_select");';
        }
        if ($report[int_all_other] == 1) {
            echo '$(".shop.brand_other").addClass("shop_select");';
        }
        ?>

        <?php if (isset($selectedShop)) { ?>
        var shop_id = 0;
        <?php foreach($selectedShop as $value){ ?>
        shop_id = "<?=$value?>";

        $(".shop").each(function () {
            if ($(this).attr("id") == shop_id) {
                $(this).addClass("shop_select");
                return false;
            }
        })
        <?php } ?>
        <?php } ?>

        $("#shop_message").html(_shopMessage());
    }
    <?php } ?>

    function submit() {
        if (document.getElementById("report_name").value == "") {
            alert("請輸入報告名稱");
            return;
        }
        if ($(".shop_select").length == 0) {
            alert("請選擇分店");
            return;
        }
        if ($(".item_selected").length == 0) {
            alert("請選擇最少一項項目");
            return;
        }

        document.getElementById('report_id').value = "<?=$_GET['id']?>";
        document.getElementById('action').value = "<?=$_GET["action"]?>";
        document.getElementById('report_info').value = JSON.stringify(reportInfo());
        //console.log(reportInfo());
        document.getElementById('add_check').submit();
    }

    function reportInfo() {
        var no = [];
        var shop = [];
        var all_shop = 0;
        var all_th = 0;
        var all_tw = 0;
        var all_ctc = 0;
        var all_other = 0;

        var select_item = $(".item_selected");
        var select_shop = $(".shop_select");
        var all_shop = $(".shop");

        var name = document.getElementById("report_name").value;
        var num_of_day = document.getElementById("num_of_day").value;
        var sort = document.getElementById("sort").value;
        //var separate = (document.getElementById("separate_t").checked)? 1 : 0;
        var hide = (document.getElementById("hide_t").checked) ? 1 : 0;
        //var car = (document.getElementById("car_t").checked)? 1 : 0;
        var mainItem = (document.getElementById("main_item").checked) ? 1 : 0;
        //var showNC = (document.getElementById("showNC_t").checked)? 1 : 0;
        var print_weekday = $("#email_weel_val").val();
        ;
        var print_time = $("#email_time").val();


        for (var m = 0; m < select_item.length; m++) {
            var s = select_item[m].id.split("item_")[1];
            s = $("#sort_item_" + s).val() + ":" + s;
            no.push(s);
        }
        if (select_shop.length == all_shop.length) {
            return {
                "item": no,
                "all_shop": 1,
                "name": name,
                "num_of_day": num_of_day,
                "hide": hide,
                "mainItem": mainItem,
                "sort": sort,
                "print_weekday": print_weekday,
                "print_time": print_time
            }
        } else {
            var temp = {
                "item": no,
                "all_shop": 0,
                "name": name,
                "num_of_day": num_of_day,
                "hide": hide,
                "mainItem": mainItem,
                "sort": sort,
                "print_weekday": print_weekday,
                "print_time": print_time
            }
            var list = $(".shop_select");
            for (j = 0; j < list.length; j++)
                shop.push(list[j].id);
            temp.shop = shop;

            return temp;
        }
    }
</script>
</html>