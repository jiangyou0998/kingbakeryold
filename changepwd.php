<?php 
     session_start();     
     if (!($_SESSION[authenticated])) {
       $_SESSION['status'] = 'neverLogin';
       header('Location: TaiHing.php');
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
.style1 {color: #FF0000}
-->
</style>
<SCRIPT>
function validatePwd() {
  var ValidChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789."; // 只限數字
  var pwdLength = 6; // 密碼長度
  var pw1 = document.form1.newPwd1.value;
  var pw2 = document.form1.newPwd2.value;
  var IsNumber=true;
  var Char;


  // 檢查密碼格是否空白
  if (pw1 == '' || pw2 == '') {
    alert('請輸入新密碼兩次.');
    form1.newPwd1.focus();
    return false;
  }

  // 檢查長度
  //if (pw1.length != pwdLength) {
    //alert('密碼長度必須為 ' + pwdLength + ' 位數');
    //form1.newPwd1.focus();
    //return false;
  //}

  // 檢查是否為數字
  for (i = 0; i < pw1.length && IsNumber == true; i++) { 
    Char = pw1.charAt(i); 
    if (ValidChars.indexOf(Char) == -1) {
      alert("密碼只限數字");
      form1.newPwd1.focus();
      return false;
    }
  }

  //檢查兩個密碼是否一樣
  if (pw1 != pw2) {
    alert ("你輸入的兩個新密碼並不相同\n\n請重試");
    form1.newPwd1.focus();
    return false;
  }

  return true;
}
</script>
<?php
  require($DOCUMENT_ROOT . "connect.inc");

	if(isset($_POST[Submit])){
		// 如果帳號和密碼正確的話，更改密碼

        $sql = "SELECT * FROM tbl_user where int_id = '$_SESSION[user_id]' and txt_password = '$_POST[oldPwd]'";
        $result = mysqli_query($con, $sql) or die("invalid query");
        if (mysqli_num_rows($result)<>0){
            $record=mysqli_fetch_array($result);

            // 寫入 Session 變數值
            $_SESSION['status'] = 'goodPWD';

            // 重導到首頁面
            $sql = "update tbl_user set txt_password='$_POST[newPwd1]' where int_id = '$_SESSION[user_id]'";
            $result = mysqli_query($con, $sql) or die("invalid query");
			echo "<script>alert('密碼已成功更改!');</script>";
			echo "<script>document.location.href='index.php';</script>";
			exit;
		}else{
            $_SESSION['status'] = 'wrongPWD';
			echo "<script>document.location.href='changepwd.php';</script>";
			exit;
		}
	}
?>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="form1.oldPwd.focus();">

<table id="Table_01" width="995" height="1148" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td  colspan="13">
        <?php include "head.php"; ?></td>
	    <td>&nbsp;</td>
	</tr>
	<tr>
		<td rowspan="6">
			<img src="images/TaiHing_12.jpg" width="27" height="890" alt=""></td>
		<td height="890" colspan="11" rowspan="6" align="center" valign="top"><table width="50%" border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td colspan="2" align="center"><span class="SelectMenu">更改密碼</span></td>
          </tr>
          <tr>
            <td colspan="2" align="center" class="SelectMenu">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" align="center"><span class="style1">
              <?php 
                if ($_SESSION['status'] == 'wrongPWD') {
                  echo '密碼錯誤，請重試！';
                } else if ($_SESSION['status'] == 'neverLogin') {
                  echo '請先登入！';
                } else if ($_SESSION['status'] == 'goodPWD') {
                  echo '';
                }
              ?>
            </span></td>
          </tr>
          <tr align="center">
            <td colspan="2">
              <form name="form1" id="form1" method="post" action="" onSubmit="return validatePwd();">
              <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFCC66">
              <tr>
                <td colspan="2" align="center">&nbsp;</td>
              </tr>
              <tr>
                <td width="40%" align="right">使用者：</td>
                <td width="60%" align="left"><?php echo $_SESSION[user]; ?></td>
              </tr>
              <tr>
                <td colspan="2" align="center">&nbsp;</td>
              </tr>
              <tr>
                <td width="40%" align="right">舊密碼：</td>
                <td width="60%" align="left"><input name="oldPwd" type="password" /></td>
              </tr>
              <tr>
                <td width="40%" align="right">新密碼：</td>
                <td width="60%" align="left"><input name="newPwd1" type="password" maxlength="8" /></td>
              </tr>
              <tr>
                <td width="40%" align="right">再次輸入新密碼：</td>
                <td width="60%" align="left"><input name="newPwd2" type="password" maxlength="8" /></td>
              </tr>
              <tr>
                <td colspan="2" align="center">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2" align="center"><input type="submit" name="Submit" value="提交" /></td>
              </tr>
              <tr>
                <td colspan="2" align="center">&nbsp;</td>
              </tr>
              </table>
              </form>
            </td>
          </tr>
        </table>
	    <br></td>        
		<td rowspan="6">
			<img src="images/TaiHing_16.jpg" width="20" height="890" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="210" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/spacer.gif" width="1" height="77" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/spacer.gif" width="1" height="13" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/spacer.gif" width="1" height="288" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/spacer.gif" width="1" height="14" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/spacer.gif" width="1" height="288" alt=""></td>
	</tr>
	<tr>
		<td colspan="13">
			<img src="images/TaiHing_22.jpg" width="994" height="17" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="17" alt=""></td>
	</tr>
	<tr>
		<td colspan="13">
			<img src="images/TaiHing_23.jpg" width="994" height="49" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="49" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/spacer.gif" width="27" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="181" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="77" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="30" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="98" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="127" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="76" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="71" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="57" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="103" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="74" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="53" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="20" height="1" alt=""></td>
		<td></td>
	</tr>
</table>

</body>
</html>