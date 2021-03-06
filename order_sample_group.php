<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    header('Location: login.php');
}
$timestamp = gettimeofday("sec");

$order_user = $_SESSION[order_user] ? $_SESSION[order_user] : $_SESSION[user_id];
?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網</title>
    <style type="text/css">
        <!--
        body {
            margin-top: 0px;
            margin-bottom: 0px;
            margin: 0px;
        }

        .bgImage {
            background-image: url("images/1_02.gif");
            width: 105px;
            height: 35px;
        }

        a {
            text-decoration: none
        }

        -->
    </style>
    <script>
        function changeBgImage(list) {
            var alinks = document.getElementById("tblist").getElementsByTagName("a");
            for (var i = 0; i < alinks.length; i++) {
                alinks[i].className = "";
            }
            list.className = "bgImage";
        }


    </script>
</head>

<body bgcolor="#697caf" text="#FFFFFF" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
<table id="tblist" border="0" cellpadding="0" cellspacing="2">
    <tr>
        <?php
        require($DOCUMENT_ROOT . "connect.inc");

        $sql = "
		SELECT T0.int_id, T0.chr_name
		FROM tbl_order_z_group T0
			LEFT JOIN (
				SELECT int_group, COUNT(*) as item_count 
				FROM tbl_order_z_menu MT0
				WHERE MT0.status IN (1) 
				GROUP BY int_group
			) T2 ON T0.int_id = T2.int_group
		WHERE T0.status <> 4
			AND T0.int_cat = '$_REQUEST[catid]'
			AND T2.item_count > 0 AND T2.item_count IS NOT NULL
		GROUP BY T0.int_id
		ORDER BY T0.int_sort;";
        
        //die($sql);
        $result = mysqli_query($con, $sql) or die($sql);
        $count = 1;
        $countdisplay = 65;
        $bgimage = "images/10.jpg";
        WHILE ($record = mysqli_fetch_array($result)) {


        IF ($count <= 5) {
            ?>
            <td width="106" height="38" align="center" background="<?= $bgimage; ?>"
                style="max-height:38px; max-width:106px;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr id="tblist">
                        <td align="center"><a onClick="changeBgImage(this)"
                                              href="order_sample_menu.php?groupid=<?= $record['int_id']; ?>"
                                              target="mainFrame"
                                              title="電話:<?= $record['chr_tel']; ?>/<?= $record['chr_tel2']; ?>"><?= $record['chr_name']; ?> </a>
                        </td>
                    </tr>
                </table>
            </td>
            <?php
        } ELSE {
        ?>
    </tr>
    <tr>
        <td width="106" height="38" align="center" background="<?= $bgimage; ?>"
            style="max-height:38px; max-width:106px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center"><a onClick="changeBgImage(this)"
                                          href="order_sample_menu.php?groupid=<?= $record['int_id']; ?>"
                                          target="mainFrame"
                                          title="電話:<?= $record['chr_tel']; ?>/<?= $record['chr_tel2']; ?>"><?= $record['chr_name']; ?></a>
                    </td>
                </tr>
            </table>
        </td>
        <?php
        $count = 1;
        }
        $count += 1;
        $countdisplay += 1;
        }
        ?>
    </tr>
</table>
</body>
</html>
