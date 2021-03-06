<?php
session_start();

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");

$sql = "SELECT T0.int_id, DATE(order_date) as order_date,  DATE(DATE_ADD(order_date, INTERVAL 1+chr_phase DAY)) as deli_date,
	int_user, int_product, SUM(int_qty) as dept_qty, chr_dept, T1.int_default_price, T2.chr_name as uom, T1.chr_name as item_name , T3.chr_report_name , SUM(ifnull(T0.int_qty_received,T0.int_qty)) as received_qty , reason
 FROM tbl_order_z_dept T0 
	LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
    LEFT JOIN tbl_order_z_unit T2 ON T1.int_unit = T2.int_id
    LEFT JOIN tbl_user T3 ON T0.int_user = T3.int_id
 WHERE chr_po_no = '$_GET[po]' AND int_user = $_GET[id] AND
 T0.status != 4
 GROUP BY int_product, chr_dept
 ORDER BY T1.chr_no ";
$po_detail = mysqli_query($con, $sql) or die($sql);

$dept_price = array();
$po = array();

while ($po_record = mysqli_fetch_assoc($po_detail)) {
//    var_dump($po);
    $po[$po_record[int_product]]['unit'] = $po_record[uom];
    $po[$po_record[int_product]]['name'] = $po_record[item_name];
    $po[$po_record[int_product]]['price'] = $po_record[int_default_price];
    $po[$po_record[int_product]]['totalqty'] += $po_record[dept_qty];
    $po[$po_record[int_product]]['totalreceivedqty'] += $po_record[received_qty];
    $po[$po_record[int_product]]['reason'] = $po_record[reason];
    $po[$po_record[int_product]]['qty']["$po_record[chr_dept]"]["qty"] = $po_record[received_qty];
    $po[$po_record[int_product]]['qty']["$po_record[chr_dept]"]["deptqty"] = $po_record[dept_qty];
    $po[$po_record[int_product]]['qty']["$po_record[chr_dept]"]["mysqlid"] = $po_record[int_id];


    $dept_price["$po_record[chr_dept]"] += ($po_record[int_default_price] * $po_record[received_qty]);
    $total_price += ($po_record[int_default_price] * $po_record[received_qty]);
}

//var_dump($po);


$po_header = mysqli_query($con, $sql) or die($sql);
$po_header = mysqli_fetch_assoc($po_header);


?>
<html>
<head>
    <title>內聯網</title>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/checkbox-style.css"/>
    <link rel="stylesheet" type="text/css" href="/js/layui/css/layui.css">
    <link href="css/bootstrap.min.css" rel="stylesheet"/>

    <script>
        $(function () {
            $(".dept-input").click(function () {

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

            $(".dept-input").on("input", function (e) {
                var v = $(this).val();
                if (v == '') {
                    $(this).val(0);
                }
                //不是數字 => 還原
                if (isNaN(v)) {
                    $(this).val(e.target.defaultValue);
                }
                //檢查Format
                var patt = /^\d+\.{0,1}\d{0,1}$/gi;
                var res = patt.test($(this).val());
                if (res == false) {
                    $(this).val(e.target.defaultValue);
                }

                //更新數值
                e.target.defaultValue = $(this).val();

                //更新項目總數
                var sum = 0;
                $(".dept-input[data-id='" + $(this).data('id') + "']").each(function () {
                    sum += parseInt($(this).val());// * $(this).data('price');
                });
                $("#total_" + $(this).data('id')).html(sum);
                if (sum != parseInt($(".order-qty[data-id='" + $(this).data('id') + "']").html())) {
                    $(".reason[data-id='" + $(this).data('id') + "']").prop("disabled", false);
                    $parentTr = $(this).parents('tr').parents('tr');
                    $parentTr.removeClass();
                    $parentTr.addClass('warning');
                } else {
                    $(".reason[data-id='" + $(this).data('id') + "']").prop("disabled", true);
                }

                //更新部門總數
                sum = 0;
                $(".dept-input[data-dept='" + $(this).data('dept') + "']").each(function () {
                    sum += parseFloat($(this).val()) * $(this).data('price');
                });
                $(".dept-total[data-dept='" + $(this).data('dept') + "']").html(formatMoney(sum));
                $(".dept-total[data-dept='" + $(this).data('dept') + "']").attr("data-sum", sum);

                //更新全單總數
                sum = 0;
                //直接取html()的數字帶有逗號,,計算失敗
                $(".dept-total").each(function () {
                    // console.log(parseFloat($(this).data("sum")));
                    // sum += parseFloat($(this).html());
                    //空的時候不加,否則出錯
                    if ($(this).attr("data-sum")) {
                        sum += parseFloat($(this).attr("data-sum"));
                    }

                });
                // alert(sum);
                $("#all_total").html(formatMoney(sum));
            });

            $(".dept-input").on("blur", function (e) {
                $(this).val(parseInt($(this).val()));
            });
        });

        function checkSubmit() {
            // alert('1111');

            //原因選擇完畢再提交
            var reasonfinish = true;
            $(".reason:enabled").each(function () {
                if ($(this).val() == '0') {
                    $(this).parent('td').addClass('danger');
                    reasonfinish = false;
                    // return false;
                }
            });

            if (reasonfinish == false) {
                alert('請選擇所有原因');
                return false;
            }

            var updatearray = [];

            $(".dept-input").each(function () {
                var mysqlid = $(this).data("mysqlid");
                //修改後數量
                var receivedqty = $(this).val();
                //修改前數量
                var qty = $(this).data("qty");

                var id = $(this).data("id");
                // 數據庫有記錄的才寫入對象;
                if (mysqlid) {
                    
                    //實收與落單數不同時寫入原因
                    if (receivedqty != qty) {
                        var item = {'mysqlid': mysqlid, 'receivedqty': receivedqty};
                        item.reason = $(".reason[data-id=" + id + "] option:selected").text();
                        updatearray.push(item);
                    }
                    
                }

            });

            // console.log(updatearray);return true;

            $.ajax({
                type: "POST",
                url: "grpo_confirm.php",
                data: {
                    'updateData': JSON.stringify(updatearray)
                },
                success: function (msg) {
                    if (msg) {
                        alert('發生錯誤!\n');
                        console.log(msg);
                    } else {
                        alert('已確認收貨!\n');
                        location.href = "CMS_invoice_edit.php?delidate=<?= $po_header['deli_date'] ?>" ;
                    }
                }
            });

            // console.log(updatearray);

            // alert('1111');
            // return true;
        }

        function formatMoney(n, c, d, t) {
            var c = isNaN(c = Math.abs(c)) ? 1 : c,
                d = d == undefined ? "." : d,
                t = t == undefined ? "," : t,
                s = n < 0 ? "-" : "",
                i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                j = (j = i.length) > 3 ? j % 3 : 0;

            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        };
    </script>
    <style type="text/css">
        <!--
        body {
            margin-left: 0px;
            margin-top: 0px;
        }

        .style2 {
            font-size: 24px;
            font-weight: bold;
        }

        .style3 {
            font-size: 22px
        }

        input.aa {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        input.bb {
            text-align: center;
            font-size: 24px;
        }

        .style4 {
            font-size: 24px
        }

        .style6 {
            font-size: 18px;
            font-weight: bold;
        }

        .style9 {
            color: #FF0000
        }

        .style10 {
            font-size: 36px
        }

        .style11 {
            font-size: 16px
        }

        .table1 td {
            padding: 4px 0px;

        }

        #GrpoData > tbody > tr > td {
            border-right: 1px solid black;
            border-bottom: 1px solid black;
            vertical-align:middle;
            text-align:center;
        }

        #GrpoData > tbody > tr > td:last-child {
            border-right: 0px;
        }

        .dept-total {
            display: inline-block;
            width: 70px;
            color: red;
        }

        .dept-input {
            text-align: center;
            font-weight: bold;
        }

        .btn {
            font-weight:bold;
            font-size: 140%;
        }
        -->
    </style>
</head>

<body>
<div align="center" style="width:995; padding:0px 0px; margin-top: 10px;" >
    <table width="98%" border="0" cellspacing="0" cellpadding="0" style="margin:auto;">
        <tbody>
        <tr>
            <td align="center"><span class="style3"><u>分店收貨</u></span></td>
            <td rowspan="2" colspan="3" align="center"><span class="style4"><?= $po_header[chr_report_name] ?></span></td>
            <td align="right"><span class="style6">送貨日期:<?= $po_header[deli_date] ?></span></td>
        </tr>
        <tr>
            <td width="25%" align="center"><span class="style3">PO#</span><span class="style10"><?= $_GET[po] ?></span>
            </td>
            <td width="25%" align="right"><span class="style3">PO日期:<?= $po_header[order_date] ?></span></td>
        </tr>
    </table>

    <table class="table1" border="1" cellspacing="0" cellpadding="0" style="width:995px; margin:auto; margin-left:1%;">
        <tr>
            <td width="125px" align="center" bgcolor="#CCCCCC"><strong>貨品</strong></td>
            <td width="75px" align="center" bgcolor="#CCCCCC"><strong>單價($)</strong></td>
            <td width="75px" align="center" bgcolor="#CCCCCC"><strong>落單</strong></td>
            <td width="75px" align="center" bgcolor="#CCCCCC"><strong>派貨</strong></td>
            <td width="394px" align="center" bgcolor="#CCCCCC">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td width="24%" align="center"><strong>實收</strong></td>
                        <td width="19%" align="center">烘焙</td>
                        <td width="19%" align="center">水吧</td>
                        <td width="19%" align="center">廚房</td>
                        <td width="19%" align="center">樓面</td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td width="200px" align="center" bgcolor="#CCCCCC">差異原因</td>
        </tr>
    </table>
    <form>
        <div style="width:995px;  padding-right:30px; margin-right:10px;">
            <div style="width:993px; border:1px solid black; min-height:200px; margin:auto; margin-left:1%;">
                <table class="table table-bordered table-striped" id="GrpoData" cellspacing="0" cellpadding="0"
                       style="width:993px; border-left:0px; border-right:0px; border-top:0px;">
                    <?php foreach ($po as $itemID => $row) {
//				    var_dump($row);
                        ?>
                        <tr 
                            <?php 
                                if($row['totalqty'] != $row['totalreceivedqty']){
                                    echo "class='danger'";
                                }
                            ?>
                        >
                            <td width="125px" align="center" bgcolor="#FFFFFF"><?= $row[name] ?></td>
                            <td width="75px" align="center" bgcolor="#FFFFFF"><?= $row[price] ?></td>
                            <td width="75px" align="center" bgcolor="#FFFFFF">
						<span style="width:50px; text-align:right;" class="order-qty"
                              data-id="<?= $itemID ?>">
							<?= number_format($row[totalqty], 0, '.', ',') ?>
						</span>
                                <?= $row[unit] ?>
                            </td>
                            <td width="75px" align="center" bgcolor="#FFFFFF">
						<span style="width:50px; text-align:right;" class="order-qty"
                              data-id="<?= $itemID ?>">
							<?= number_format($row[totalqty], 0, '.', ',') ?>
						</span>
                                <?= $row[unit] ?>
                            </td>
                            <td width="394px" align="center" bgcolor="#FFFFFF">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tbody>
                                    <tr>
                                        <td width="24%" align="center">
								<span style="width:50px; text-align:right;"
                                      id="total_<?= $itemID ?>">
									<?= number_format($row[totalreceivedqty], 0, '.', ',') ?>
								</span>
                                            <?= $row[unit] ?>
                                        </td>
                                        <?php
                                        $depts = array("R", "B", "K", "F");
                                        foreach ($depts as $dept) {
                                            ?>
                                            <td width="19%" align="center">
                                                <input name="item[<?= $itemID ?>][<?= $dept ?>]"
                                                       data-mysqlid="<?= ($row[qty][$dept][mysqlid]) ? $row[qty][$dept][mysqlid] : '' ?>"
                                                       data-price="<?= $row[price] ?>"
                                                       data-dept="<?= $dept ?>"
                                                       data-id="<?= $itemID ?>"
                                                       data-qty="<?= ($row[qty][$dept][qty]) ? number_format($row[qty][$dept][qty], 0, '.', ',') : '0' ?>"
                                                       value="<?= ($row[qty][$dept][qty]) ? number_format($row[qty][$dept][qty], 0, '.', ',') : '0' ?>"
                                                       class="dept-input" type="number" autocomplete="off"
                                                       style="width:95%; margin:auto;"/>
                                            </td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="200px" align="center" bgcolor="#FFFFFF">
                                <select name="reason[<?= $itemID ?>]" class="reason" data-id="<?= $itemID ?>"
                                        style="width:95%; margin:auto; font-size:14px;" 
                                        <?php 
                                            if($row['totalqty'] == $row['totalreceivedqty']){
                                                echo "disabled";
                                            }
                                        ?>
                                >

                                <?php 
                                    $reasonArr = [
                                        0=>'請選擇原因',
                                        1=>'品質問題 (壞貨)',
                                        2=>'執漏貨',
                                        3=>'執錯貨',
                                        4=>'分店落錯貨，即日收走',
                                        5=>'打錯單',
                                        6=>'抄碼',
                                        7=>'運送途中損爛',
                                        8=>'運輸送錯分店',
                                        9=>'缺貨',
                                        10=>'廠派貨',
                                        11=>'分店要求扣數',
                                        12=>'分店要求加單',
                                        13=>'不明原因'
                                    ];
                                    // var_dump($row['reason']);

                                    foreach ($reasonArr as $key => $value) {
                                        if ($row['reason'] == $value){
                                            echo '<option value="'.$key.'" selected>'.$value.'</option>';
                                        }else{
                                            echo '<option value="'.$key.'">'.$value.'</option>';
                                        }
                                    }

                                ?>
                                    <!-- <option value="0">請選擇原因</option>
                                    <option value="1">品質問題 (壞貨)</option>
                                    <option value="2">執漏貨</option>
                                    <option value="3">執錯貨</option>
                                    <option value="4">分店落錯貨，即日收走</option>
                                    <option value="5">打錯單</option>
                                    <option value="6">抄碼</option>
                                    <option value="7">運送途中損爛</option>
                                    <option value="8">不明原因</option> -->
                                </select>
                            </td>
                        </tr>
                    <? } ?>
                </table>
            </div>

        </div>

        <table class="table1" border="1" cellspacing="0" cellpadding="0"
               style="width:995px; margin:auto; margin-left:1%;">
            <tr>
                <td width="575px" align="left" bgcolor="#CCFFFF">&nbsp;&nbsp;
                    <span style="font-size:24px; font-weight:bold;">總數: $
					<span id="all_total"><?= number_format($total_price, 1, '.', ',') ?></span>
				</span>

                    <div style="color:red">
                        &nbsp;&nbsp;
                        烘:$<span class="dept-total" data-dept="R"
                                 data-sum="<?= $dept_price[R] ?>"><?= number_format($dept_price[R], 1, '.', ',') ?></span>
                        水:$<span class="dept-total" data-dept="B"
                                 data-sum="<?= $dept_price[B] ?>"><?= number_format($dept_price[B], 1, '.', ',') ?></span>
                        廚:$<span class="dept-total" data-dept="K"
                                 data-sum="<?= $dept_price[K] ?>"><?= number_format($dept_price[K], 1, '.', ',') ?></span>
                        樓:$<span class="dept-total" data-dept="F"
                                 data-sum="<?= $dept_price[F] ?>"><?= number_format($dept_price[F], 1, '.', ',') ?></span>
                    </div>
                </td>
                <td width="194px" align="center" bgcolor="#CCFFFF"></td>
                <td width="200px" align="center" bgcolor="#CCFFFF"></td>
            </tr>
        </table>
        </br>
        <input type="hidden" name="po" value="<?= $_GET[po] ?>"/>
        <!--	<button style="font-size:24px; padding:4px; "  onclick="checkSubmit();" value="確認收貨"/>-->
        <button class="btn btn-primary" type="button" onclick="checkSubmit();">&nbsp;確認收貨&nbsp;</button>
        <!-- <button class="btn btn-danger" type="button" onclick="location='grpo.php';">&nbsp;返回&nbsp;</button> -->
    </form>

</div>

</body>

</html>