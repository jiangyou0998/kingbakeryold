<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'library.php';
    header('Location: login.php');
}
?>
<html>
<head>
    <title>內聯網</title>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5">
    <link href="class.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        <!--
        body {
            margin-left: 0px;
            margin-top: 0px;
        }

        .style2 {
            font-size: 24px
        }

        -->
    </style>
</head>
<body>
<table width="994" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="14"><?php include("head.php"); ?></td>
    </tr>
    <tr>
        <td colspan="14" align="center" valign="top">
            <table width="90%" border="0" cellspacing="0" cellpadding="20">
                <tr>
                    <?php
                    require($DOCUMENT_ROOT . "connect.inc");
                    $sql = "SELECT * FROM tbl_lib_pmd WHERE group_id = " . $_REQUEST['int_group'] . " AND date_delete = '2000-01-01' ";
                    $result = mysqli_query($con, $sql) or die("invalid query: " . $sql);
                    $i = 0;
                    $count = mysqli_num_rows($result);

                    while ($record = mysqli_fetch_array($result)) {

                        if ($record[state_cent] == 1 && $_SESSION['type'] == 2 || $record[state_logs] == 1 && $_SESSION['type'] <> 2 || $record[int_shop] == $_SESSION['user_id']) {
                            echo "<td width=\"33%\" align=\"center\" valign=\"top\">
				<table width=\"100%\" border=\"1\" cellpadding=\"20\" cellspacing=\"0\" bordercolor=\"#8C5388\">
				<tr>
				<td align=\"center\" bgcolor=\"#CCCCCC\"><a href=\"" . $record['txt_path'] . " \"target=\"_blank\">" . $record['txt_name'] . "&nbsp;</a></td>
				</tr>
				</table>
				</td>";
                        } else if ($record['state_dept'] == 1 && $record['int_dept'] == $_SESSION['dept']) {
                            echo "<td width=\"33%\" align=\"center\" valign=\"top\">
				<table width=\"100%\" border=\"1\" cellpadding=\"20\" cellspacing=\"0\" bordercolor=\"#8C5388\">
				<tr>
				<td align=\"center\" bgcolor=\"#CCCCCC\"><a href=\"" . $record['txt_path'] . " \"target=\"_blank\">" . $record['txt_name'] . "&nbsp;</a></td>
				</tr>
				</table>
				</td>";
                        } else {
                            continue;
                        }
                        $i++;
                        if ($i % 3 == 0) {
                            echo "</tr><tr>";
                        }
                    }

                    if ($i % 3 == 1) {
                        echo "<td width=\"33%\" align=\"center\" valign=\"top\">&nbsp;</td>
		  <td width=\"33%\" align=\"center\" valign=\"top\">&nbsp;</td>
		  </tr>";
                    } else if ($i % 3 == 2) {
                        echo "<td width=\"33%\" align=\"center\" valign=\"top\">&nbsp;</td>
         </tr>";
                    }
                    ?>
                    <!-- </tr>  -->
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="14" align="center" valign="top">&nbsp;</td>
    </tr>
    <tr>
        <td align="center" valign="top"><a href="library.php" class="style2">返回</a></td>
    <tr>
        <td align="center" valign="top">&nbsp;</td>
    <tr>
</table>
<table width="994" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td><img src="images/TaiHing_23.jpg" width="994" height="49"></td>
    </tr>
</table>
</body>
</html>

