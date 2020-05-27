<?php
require($DOCUMENT_ROOT . "connect.inc");
session_start();
?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網 - 後勤系統</title>
    <script>
        function checkDaohang(url, op) {
            console.log(op.value);
            parent.topFrame.location.href = "CRM_head.php?type=" + op.value;
            parent.mainFrame.location.href = url + ".php";
        }
    </script>
    <style type="text/css">
        .left {
            width: 150px;
            text-align: left;
            padding-left: 7px;
        }
    </style>
</head>

<body>
後台系統
<br>
<br><input class="left" type="button" value='修改工場落貨' onClick="checkDaohang('CMS_order',this)"/>
<br><input class="left" type="button" value='新增／刪除通告' onClick="checkDaohang('CMS_notice',this)"/>
<br><input class="left" type="button" value='新增／刪除表格' onClick="checkDaohang('CMS_form',this)"/>
<br><input class="left" type="button" value='圖書館管理' onClick="checkDaohang('CMS_library',this)"/>
<br><input class="left" type="button" value='使用者管理' onClick="checkDaohang('CMS_manage',this)"/>
<br><input class="left" type="button" value='維修項目管理' onClick="checkDaohang('CMS_RepairProject_manage',this)"/>
<br><input class="left" type="button" value='IT求助項目管理' onClick="checkDaohang('CMS_itsupport_manage',this)"/>
<br><input class="left" type="button" value='營業數' onClick="checkDaohang('CMS_salesdata',this)"/>
<br><input class="left" type="button" value='營業數項目管理' onClick="checkDaohang('CMS_salesdata_manage',this)"/>


<br>
<br>
<a href="index.php" target="_parent">退出後台系統</a>
</body>
</html>

