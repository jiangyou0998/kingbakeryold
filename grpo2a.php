<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'salesdata.php';
    header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");

$sql = "SELECT DATE(order_date) as order_date,  DATE(DATE_ADD(order_date, INTERVAL 1+chr_phase DAY)) as deli_date,
	int_user, int_product, SUM(int_qty) as dept_qty, chr_dept, T1.int_default_price, T2.chr_name as uom, T1.chr_name as item_name
 FROM tbl_order_z_dept T0 
	LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
    LEFT JOIN tbl_order_z_unit T2 ON T1.int_unit = T2.int_id
 WHERE chr_po_no = $_GET[po] AND T0.status = 98 AND int_user = $_SESSION[user_id]
 GROUP BY int_product, chr_dept
 ORDER BY T1.chr_no ";
$po_detail = mysqli_query($con, $sql) or die($sql);
while ($po_record = mysqli_fetch_assoc($po_detail)) {
    $po[$po_record[int_product]]['unit'] = $po_record[uom];
    $po[$po_record[int_product]]['name'] = $po_record[item_name];
    $po[$po_record[int_product]]['price'] = $po_record[int_default_price];
    $po[$po_record[int_product]]['qty']["$po_record[chr_dept]"] = $po_record[dept_qty];

    $dept_price["$po_record[chr_dept]"] += ($po_record[int_default_price] * $po_record[dept_qty]);
    $total_price += ($po_record[int_default_price] * $po_record[dept_qty]);
}

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

    <script>
        $(function () {
            $(".dept-input").on("input", function (e) {
                var v = $(this).val();
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
                } else {
                    $(".reason[data-id='" + $(this).data('id') + "']").prop("disabled", true);
                }

                //更新部門總數
                sum = 0;
                $(".dept-input[data-dept='" + $(this).data('dept') + "']").each(function () {
                    sum += parseFloat($(this).val()) * $(this).data('price');
                });
                $(".dept-total[data-dept='" + $(this).data('dept') + "']").html(formatMoney(sum));

                //更新全單總數
                sum = 0;
                $(".dept-total").each(function () {
                    sum += parseFloat($(this).html());
                });
                $("#all_total").html(formatMoney(sum));
            });

            $(".dept-input").on("blur", function (e) {
                $(this).val(parseInt($(this).val()));
            });
        });

        function checkSubmit() {
            $(".reason:enabled").each(function () {
                if ($(this).val() == '0') {
                    alert('請選擇所有原因');
                }
            });
            return true;
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

        -->
    </style>
</head>

<body>
<table id="Table_01" width="995" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="13">
            <?php include "head.php"; ?></td>
        <td>&nbsp;</td>
    </tr>
</table>
<div align="center" style="width:995; padding:0px 0px;">
    <table width="98%" border="0" cellspacing="0" cellpadding="0" style="margin:auto;">
        <tbody>
        <tr>
            <td align="center"><span class="style3"><u>分店收貨</u></span></td>
            <td rowspan="2" colspan="3" align="center"><span class="style4">貨倉</span></td>
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
            <td width="200px" align="center" bgcolor="#CCCCCC"><strong>貨品</strong></td>
            <td width="100px" align="center" bgcolor="#CCCCCC"><strong>單價($)</strong></td>
            <td width="100px" align="center" bgcolor="#CCCCCC"><strong>落單</strong></td>
            <td width="100px" align="center" bgcolor="#CCCCCC"><strong>派貨</strong></td>
            <td width="100px" align="center" bgcolor="#CCCCCC">實收</strong></td>
            <td width="200px" align="center" bgcolor="#CCCCCC">差異原因</td>
        </tr>
    </table>
    <form action="grpo3.php" onsubmit="return checkSubmit();" method="POST">
        <div style="width:995px; max-height:400px; overflow-y:scroll; overflow-x: hidden; padding-right:30px; margin-right:10px;">
            <div style="width:993px; border:1px solid black; min-height:200px; margin:auto; margin-left:1%;">
                <table class="table1" id="GrpoData" cellspacing="0" cellpadding="0"
                       style="width:993px; border-left:0px; border-right:0px; border-top:0px;">
                    <?php foreach ($po as $itemID => $row) { ?>
                        <tr>
                            <td width="200px" align="center" bgcolor="#FFFFFF"><?= $row[name] ?></td>
                            <td width="100px" align="center" bgcolor="#FFFFFF"><?= $row[price] ?></td>
                            <td width="100px" align="center" bgcolor="#FFFFFF">
						<span style="width:50px; display:inline-block; text-align:right;" class="order-qty"
                              data-id="<?= $itemID ?>">
							<?= number_format(array_sum($row[qty]), 0, '.', ',') ?>
						</span>
                                <?= $row[unit] ?>
                            </td>
                            <td width="100px" align="center" bgcolor="#FFFFFF">
						<span style="width:50px; display:inline-block; text-align:right;" class="deli-qty"
                              data-id="<?= $itemID ?>">
							<?= number_format(array_sum($row[qty]), 0, '.', ',') ?>
						</span>
                                <?= $row[unit] ?>
                            </td>
                            <td width="100px" align="center" bgcolor="#FFFFFF">
                                <input type="text" value="<?= array_sum($row[qty]) ?>" class="dept-input"
                                       style="text-align:right; width:50px;"/>
                                <?= $row[unit] ?>
                                <!--
						<span style="width:50px; display:inline-block; text-align:right;" id="total_<?= $itemID ?>">
							<?= number_format(array_sum($row[qty]), 0, '.', ',') ?>
						</span>
						-->
                            </td>
                            <td width="200px" align="center" bgcolor="#FFFFFF">
                                <select name="reason[<?= $itemID ?>]" class="reason" data-id="<?= $itemID ?>"
                                        style="width:95%; margin:auto; font-size:14px;" disabled>
                                    <option value="0">請選擇原因</option>
                                    <option value="1">品質問題 (壞貨)</option>
                                    <option value="2">執漏貨</option>
                                    <option value="3">執錯貨</option>
                                    <option value="4">分店落錯貨，即日收走</option>
                                    <option value="5">打錯單</option>
                                    <option value="6">運送途中損爛</option>
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
                </td>
                <td width="194px" align="center" bgcolor="#CCFFFF"></td>
                <td width="200px" align="center" bgcolor="#CCFFFF"></td>
            </tr>
        </table>
        </br>
        <input type="hidden" name="po" value="<?= $_GET[po] ?>"/>
        <input style="font-size:24px; padding:4px; " type="submit" value="確認收貨"/>
        <button type="button" onclick="location='grpo.php';">&nbsp;返回&nbsp;</button>
    </form>

</div>
<br/>
<br/>
<br/>
<br/>
<br/>
<table>
    <tr>
        <td colspan="13">
            <img src="images/TaiHing_23.jpg" width="994" height="49" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="1" height="49" alt=""></td>
    </tr>
</table>
</body>

</html>