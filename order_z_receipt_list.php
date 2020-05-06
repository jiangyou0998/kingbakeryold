<?php
require($DOCUMENT_ROOT . "connect.inc");
$timestamp = gettimeofday("sec") + 28800;
$sql = "SELECT T3.*,group_concat(T3.reason) as reasons FROM
    (SELECT DATE(DATE_ADD(T0.order_date, INTERVAL 1+T0.chr_phase DAY)) as date, T0.int_user, T1.int_no, T2.txt_name,T0.reason
        FROM tbl_order_z_dept T0
            LEFT JOIN tbl_order_z_receipt T1 ON 
            DATE(DATE_ADD(T0.order_date, INTERVAL 1+T0.chr_phase DAY)) = T1.order_date 
            AND T0.int_user = T1.int_user
            LEFT JOIN tbl_user T2 ON T0.int_user =  T2.int_id
        WHERE T0.status = 99
    ) T3
    
    group by date,txt_name
    order BY date,int_no, int_user;";

?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>內聯網</title>
    <link href="js/My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
    <link href="css/jquery.horizontalmenu.css" rel="stylesheet">
    <script src="js/jquery-1.9.1.min.js"></script>
    <script src="js/My97DatePicker/WdatePicker.js"></script>
    <script src="js/parser.js"></script>
    <script type="text/javascript" src="js/jquery.horizontalmenu.js"></script>
    <style>
        <!--
        .style1 {
            font-size: 34px
        }

        .style3 {
            font-size: 16px;
        }

        .style6 {
            font-size: 22px;
            font-weight: bold;
        }

        .cssImportant {
            background-color: #CCFFFF
        }

        #content td {
            padding: 4px;
        }

        -->
    </style>
</head>

<div align="center">
    <h1>發票列表</h1>

</div>

<!-- 選項卡菜單名 -->
<div class="ah-tab-wrapper">
    <div class="ah-tab">
        <a class="ah-tab-item" data-ah-tab-active="true" href="">未確認</a>
        <a class="ah-tab-item" href="">已確認</a>

    </div>
</div>

<!-- 未確認 -->
<div class="ah-tab-content-wrapper">
    <div class="ah-tab-content" data-ah-tab-active="true">
        <div align="center" width="100%">
            <table id="content" style="width:100%" cellspacing="0" cellpadding="0" border="1">
                <tr>
                    <th class="cssImportant" width="5%">#</th>
                    <th class="cssImportant" width="15%">日期</th>
                    <th class="cssImportant" width="15%">分店</th>
                    <th class="cssImportant" width="15%">編號</th>
                    <th class="cssImportant" width="15%">狀態</th>
                    <th class="cssImportant" width="15%"></th>
                </tr>
                <?php
                $count = 1;
                $result = mysqli_query($con, $sql) or die($sql);
                while ($record = mysqli_fetch_array($result)) {
                    ?>
                    <?php if (!$record[int_no]) { ?>
                        <tr <?php if ($record['reasons']) echo 'style="background: #ffe599"' ?>>
                            <td><?= $count ?></td>
                            <td align="center"><?= $record[date] ?></td>
                            <td><?= $record[txt_name] ?></td>
                            <td colspan="2" style="text-align:center; color:red;">未確認</td>
                            <td align="center">
                                <a target="_blank"
                                   href="order_z_receipt.php?receiptNo=<?= $record[int_no] ?>&shop=<?= $record[int_user] ?>&date=<?= $record[date] ?>">
                                    <button>瀏覽</button>
                                </a>
                            </td>
                        </tr>

                        <?php
                        $count++;
                    }
                } ?>
            </table>
        </div>
    </div>
</div>

<!-- 已確認 -->
<div class="ah-tab-content-wrapper">
    <div class="ah-tab-content">
        <div align="center" width="100%">
            <table id="content" style="width:100%" cellspacing="0" cellpadding="0" border="1">
                <tr>
                    <th class="cssImportant" width="5%">#</th>
                    <th class="cssImportant" width="15%">日期</th>
                    <th class="cssImportant" width="15%">分店</th>
                    <th class="cssImportant" width="15%">編號</th>
                    <th class="cssImportant" width="15%">狀態</th>
                    <th class="cssImportant" width="15%"></th>
                </tr>
                <?php
                $count = 1;
                $result = mysqli_query($con, $sql) or die($sql);
                while ($record = mysqli_fetch_array($result)) {
                    ?>
                    <?php if ($record[int_no]) { ?>
                        <tr <?php if ($record['reasons']) echo 'style="background: #ffe599"' ?>>
                            <td><?= $count ?></td>
                            <td align="center"><?= $record[date] ?></td>
                            <td><?= $record[txt_name] ?></td>
                            <td align="center"><?= STR_PAD($record[int_no], 8, '0', STR_PAD_LEFT) ?></td>
                            <td align="center">已確認</td>
                            <td align="center">
                                <a target="_blank"
                                   href="order_z_receipt.php?receiptNo=<?= $record[int_no] ?>&shop=<?= $record[int_user] ?>&date=<?= $record[date] ?>">
                                    <button>瀏覽</button>
                                </a>
                            </td>
                        </tr>

                        <?php
                        $count++;
                    }
                } ?>
            </table>
        </div>
    </div>
</div>


</body>

<script type="text/javascript">

    $(function () {
        $('.ah-tab-wrapper').horizontalmenu({
            itemClick: function (item) {
                $('.ah-tab-content-wrapper .ah-tab-content').removeAttr('data-ah-tab-active');
                $('.ah-tab-content-wrapper .ah-tab-content:eq(' + $(item).index() + ')').attr('data-ah-tab-active', 'true');
                return false; //if this finction return true then will be executed http request
            }
        });
    });

</script>
</html>