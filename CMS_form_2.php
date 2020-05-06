<?php
//     session_start();
//     if (!($_SESSION[authenticated])) {
//       $_SESSION['status'] = 'neverLogin';
//       header('Location: TaiHing.php');
//     }

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");

if (isset($_POST[Submit])) {


    $is_error = false;
    $error_mgs = '';

    include "fileupload.php";
    $uploadedfile_PDF = '';
    $uploadedfile_first = '';

    $up = new fileupload;
    $up->set("path", "forms");
    $up->set("maxsize", 20000000);
    $up->set("allowtype", array("pdf", 'xls', 'xlsx'));
    $up->set("israndname", false);
    //顯示文檔
    if ($up->upload("uploadedfile")) {

        $uploadedfile_PDF = $up->getFileName();
    } else {
        $is_error = true;
        $error_mgs = $up->getErrorMsg();
    }
    //原始文檔
    $up->set("allowtype", array("pdf", 'xls', 'doc', 'docx', 'xlsx'));
    if ($up->upload("uploadedfile_first")) {
        $uploadedfile_first = $up->getFileName();
    } else {
        //$is_error  = true;
        $error_mgs .= $up->getErrorMsg();
    }

    if (!$is_error) {

        if ($_REQUEST[action] == 'new') {
            $sql = "INSERT INTO tbl_forms (txt_name, int_dept, txt_path, int_user, date_create, date_modify, date_delete, int_no,is_multi_print)";
            $sql .= " VALUES ('";
            $sql .= $_POST[txt_name];
            $sql .= "','";
            $sql .= $_REQUEST[dept];
            $sql .= "','";
            $sql .= $uploadedfile_PDF;
            $sql .= "','";
            $sql .= $_SESSION['user_id'];
            $sql .= "','";
            $sql .= date("Y-m-d");
            $sql .= "','";
            $sql .= date("Y-m-d");
            $sql .= "','";
            $sql .= "2000-01-01";
            $sql .= "','";
            $sql .= $_POST[int_no];
            $sql .= "','";
            $sql .= $_POST[is_multi_print] ? '1' : '0';
            $sql .= "')";


            $result = mysqli_query($con, $sql) or die($sql);
            $id = mysqli_insert_id($con);

            $brand = explode(',', $_REQUEST[brand]);
            foreach ($brand as $b) {
                $sql = "INSERT INTO tbl_forms_v_brand VALUES(NULL, $b, $id)";
                mysqli_query($con, $sql) or die($sql);
            }
            //echo "<script>alert(\"檔案 " . basename($_FILES['uploadedfile']['name']) . " 已成功上載\")</script>";
            echo "<script>alert(\" 原始文檔 （" . $uploadedfile_first . "） \\n\\n 顯示文檔（" . $uploadedfile_PDF . "）\\n\\n 已成功上載\")</script>";
            echo "<script>document.location.href='CMS_form.php?type=$_REQUEST[type]';</script>";
        }
    } else {
        //echo "檔案 '" . basename($_FILES['uploadedfile']['name']) . "' 上傳發生錯誤";
        //if ($_FILES['uploadedfile']['error'] == 1) echo "<br>檔案大小限 2MB 以下";
        echo $error_mgs;
    }
}
?>
<?php
if ($_REQUEST[action] == 'del') {
    $sql = "SELECT txt_path FROM tbl_forms WHERE int_id = $_REQUEST[id]";
    $result = mysqli_query($con, $sql) or die("invalid query");
    $record = mysqli_fetch_array($result);

    $myFile = "forms/" . basename($record[0]);
    unlink($myFile);

    $sql = "UPDATE tbl_forms SET date_delete = '" . date("Y-m-d") . "', int_user = $_SESSION[user_id] WHERE int_id = $_REQUEST[id]";
    $result = mysqli_query($con, $sql) or die("invalid query");
    echo "<script>document.location.href='CMS_form.php?type=$_REQUEST[type]';</script>";
} else {
    ?>
    <html>
    <head>
        <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
        <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
        <title>內聯網 - 後勤系統</title>
        <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="js/json2.js" type="text/javascript"></script>
        <script src="js/select.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/MultipleSelect/multiple-select.js"></script>
        <script src="js/My97DatePicker/WdatePicker.js"></script>

        <link href="css/select.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="js/MultipleSelect/multiple-select.css"/>
        <link rel="stylesheet" type="text/css" href="css/checkbox-style.css"/>
        <link rel="stylesheet" type="text/css" href="css/MultipleSelectList-checkbox.css"/>

        <link href="My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
        <script>
            $(function () {
                $("#brand").multipleSelect({
                    allSelected: "已選擇所有",
                    selectAllText: "全部",
                    ellipsis: true,
                    selectAll: true,
                    countSelected: '已選擇 # 項',
                    minimumCountSelected: 10,
                    multiple: true,
                    multipleWidth: 130,
                    onClose: function () {
                        $("#brandVal").val($("#brand").multipleSelect('getSelects').join(','));
                    }
                });
                $("#brandVal").val($("#brand").multipleSelect('getSelects').join(','));
            });

            function check_sub() {
                var file_name = document.getElementById("uploadedfile").value;
                if (changeType(file_name)) {
                    alert('顯示文檔只能為PDF,WORD或EXCEL文檔！');
                    return false;
                }
            }

            function changeType(objFile) {

                var objtype = objFile.substring(objFile.lastIndexOf(".")).toLowerCase();

                var fileType = new Array('.pdf', '.xls', '.xlsx');

                for (var i = 0; i < fileType.length; i++) {

                    if (objtype == fileType[i]) {

                        return false;

                        break;

                    }

                }

                return true;

            }
        </script>
    </head>

    <body>
    <form enctype="multipart/form-data" action="" method="POST" name="upload" onSubmit="return check_sub()" id="upload">

        <table border="1">
            <tr>
                <td>表格編號：</td>
                <td><input name="int_no" type="text" id="int_no" value="0" size="10"></td>
            </tr>
            <tr>
                <td>主旨：</td>
                <td><input name="txt_name" type="text" id="txt_name" size="31"></td>
            </tr>
            <tr>
                <td>大量列印：</td>
                <td>
                    <input name="is_multi_print" type="checkbox" id="is_multi_print" value="1">
                </td>
            </tr>
            <tr>
                <td>部門</td>
                <td>
                    <select name="dept">
                        <?
                        $sql = "SELECT * FROM tbl_dept WHERE int_id IN($_SESSION[dept]);";
                        $result = mysqli_query($con, $sql) or die($sql);
                        while ($record = mysqli_fetch_assoc($result)) { ?>
                            <option value="<?= $record[int_id] ?>"><?= $record[txt_dept] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>可視品牌：</td>
                <td>
                    <select id="brand" style="width:300px;" multiple>
                        <?php
                        $sql = "SELECT * FROM tbl_order_z_area ORDER BY int_sort; ";
                        $brand = mysqli_query($con, $sql);
                        while ($d = mysqli_fetch_assoc($brand)) {
                            ?>
                            <option value="<?= $d['int_id'] ?>" selected><?= $d['chr_name'] ?></option>
                        <?php } ?>
                    </select>
                    <input name="brand" id="brandVal" value="" type="hidden"/>
                </td>
            </tr>
            <tr>
                <td colspan="2">選擇上傳之檔案(顯示文檔PDF)</td>
            </tr>

            <tr>
                <td colspan="2"><input name="uploadedfile" type="file" id="uploadedfile"
                                       accept="application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                       size="30" single/></td>
            </tr>
        </table>
        <!--
        選擇上傳之檔案（原始文檔）　(容許格式：doc, docx, xls, xlsx)
        <br>
        <input name="uploadedfile_first" type="file" size="30" />
        <br>
        -->
        <br>
        <input type="submit" name="Submit" value="提交"/>
        <input name="Back" type="button" id="Back" onClick="history.go(-1);" value="返回">
        <input type="hidden" name="MAX_FILE_SIZE" value="20000000"/>
    </form>
    </body>
    </html>
    <?php
}
?>