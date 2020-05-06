<?php
session_start();
require($DOCUMENT_ROOT . "connect.inc");
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

        -->
    </style>
</head>

<body onload="test()">
<table width="994" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="14"><?php include("head.php") ?></td>
    </tr>
    <tr>
        <td colspan="14" align="center" valign="top">
            <table width="90%" border="0" cellspacing="0" cellpadding="20" id="mainTable">
                <tr>
                    <?php
                    $sql = "SELECT * FROM tbl_lib_main ORDER BY int_sort ";
                    $result = mysqli_query($con, $sql) or die("invalid query");
                    $main = 0;
                    while ($record = mysqli_fetch_array($result)) {
                        $count_group = 0;
                        $table_text = "<td width=\"33%\" align=\"center\" valign=\"top\">
          <table width=\"100%\" border=\"1\" cellpadding=\"20\" cellspacing=\"0\" bordercolor=\"#8C5388\">
            <tr>
              <td align=\"center\" bgcolor=\"#CCCCCC\">" . $record['txt_name'] . "</td>
              </tr>
            <tr>
              <td height=\"180px\" valign=\"top\">
                <ul>";
                        $sql_detail = "SELECT * FROM tbl_lib_group WHERE main_id = " . $record['int_id'] . "  ORDER BY int_sort desc  ";
                        $result_detail = mysqli_query($con, $sql_detail) or die($sql_detail);
                        while ($record_detail = mysqli_fetch_array($result_detail)) {
                            $sql = "SELECT * FROM tbl_lib_pmd WHERE group_id = " . $record_detail['int_id'] . " AND date_delete = '2000-01-01' ";
                            $res = mysqli_query($con, $sql) or die("invalid query: " . $sql);
                            $i = 0;
                            while ($rec = mysqli_fetch_array($res)) {
                                if ($rec[state_cent] == 1 && $_SESSION['type'] == 2 ||
                                    $rec[state_logs] == 1 && $_SESSION['type'] <> 2 ||
                                    $rec[int_shop] == $_SESSION['user_id'] ||
                                    ($rec[state_dept] == 1 && $_SESSION['dept'] == $rec[int_dept])) {
                                    //echo "分店(".$rec[state_cent].")后台(".$rec[state_logs].")類型(".$_SESSION['type'].")";
                                    $i++;
                                }
                            }
                            if ($i > 0) {
                                $count_group++;
                                $table_text .= "
							<li>
							<a href=\"lib_pmd.php?int_group=" . $record_detail['int_id'] . "\" target=\"_self\">" . $record_detail['txt_name'] . "</a>
							</li>";
                            }
                        }
                        $table_text .= "
				</ul>
				</td> 
				</tr>
				</table>
				</td>";
                        if ($count_group > 0) {
                            $main++;
                            echo $table_text;
                        }
                        if ($main % 3 == 0 && $main != 0) {
                            echo "</tr><tr>";
                        }
                    }
                    if ($main == 1) {
                        echo "<td width=\"33%\" align=\"center\" valign=\"top\">&nbsp;</td>";
                        echo "<td width=\"33%\" align=\"center\" valign=\"top\">&nbsp;</td>";
                    } else if ($main == 2) {
                        echo "<td width=\"33%\" align=\"center\" valign=\"top\">&nbsp;</td>";
                    }
                    ?>
                <tr>
            </table>
        </td>
    </tr>
</table>
<table width="994" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td><img src="images/TaiHing_23.jpg" width="994" height="49"></td>
    </tr>
</table>
</body>
<script>
    function test() {
        <?php
        $script = '
		var lastHTML;
		var mainTable = document.getElementById("mainTable");
		var count = 0;
		var lastRow = 0;
		var tdHTML = \'<table width="100%" border="1" cellpadding="20" cellspacing="0" bordercolor="#8C5388">\';
			tdHTML += \'<tr><td align="center" bgcolor="#CCCCCC">牌照圖則、保養及證書</td></tr>\';
			tdHTML += \'<tr><td height="180px" valign="top"><ul><li><a href="lib_list_cert.php" target="_self">牌照圖則、保養及證書</a></li>\';
			tdHTML += \'<li class="display-s"><a href="lib_license_report.php" target="_self">牌照到期監控</a></li></ul></td></tr>\';
			tdHTML += \'</table>\';
		
		for(var i=0;i<mainTable.rows.length;i++){
			count += mainTable.rows[i].cells.length;
			if (mainTable.rows[i].cells.length > 0){
				lastRow = i;
			}
		}
		if (count % 3 == 0){
			var row = mainTable.insertRow(mainTable.rows.length);
			var x = row.insertCell(row.cells.length);
			x.innerHTML = tdHTML;
		}else{
			var row = mainTable.rows[mainTable.rows.length-2];
			var x = row.insertCell(row.cells.length);
			x.innerHTML = tdHTML;
		}
		';
        if ($_SESSION["dept"] != 2) {
            echo $script;
        }
        ?>
    }

</script>

</html>