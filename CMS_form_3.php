<?
//session_start();
//if (!($_SESSION[authenticated])) {
//  $_SESSION['status'] = 'neverLogin';
//  header('Location: TaiHing.php');
//}

//檢查是否登錄,是否管理員
require ("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");
$form_id = $_REQUEST[id];

$sql = "SELECT * FROM tbl_forms WHERE int_id = $form_id";
$result = mysqli_query($con, $sql) or die("invalid query");
$record = mysqli_fetch_array($result, mysqli_NUM);
$title = $record[1];
$target_path = "forms/samples/";
$renew_path = "forms/samples/".$record[10];

if(isset($_POST[Submit])){

  if ($_REQUEST[action] == 'edit') {
    unlink($renew_path);
  }
    $target_path = $target_path . basename($_FILES['uploadedfile']['name']);
    if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {

      $sql = "UPDATE tbl_forms SET is_sample = 1, sample_path = '".basename($_FILES['uploadedfile']['name'])."' WHERE int_id = $form_id";
      $result = mysqli_query($con, $sql) or die($sql);
      echo "<script>alert(\"樣本檔案 " . basename($_FILES['uploadedfile']['name']) . " 已成功上載\")</script>";
      echo "<script>document.location.href='CMS_form.php?type=14';</script>";
    } else {
      echo "樣本檔案 '" . basename($_FILES['uploadedfile']['name']) . "' 上傳發生錯誤";
      echo $target_path;
    }

} else {
?>
<html>
<head>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>內聯網 - 後勤系統</title>
</head>

<body>
<form enctype="multipart/form-data" action="" method="POST" name="upload" id="upload">
  <p>標題：<?=$title?></p>
  <p>選擇上傳之樣本檔案
    <br>
    <input name="uploadedfile" type="file" size="30" />
    <br>
    <br>
    <input type="submit" name="Submit" value="提交" />
    <input name="Back" type="button" id="Back" onClick="history.go(-1);" value="返回">
    </p>
</form>
</body>
</html>
<?
}
?>