<?php
//     session_start();
//     if (!($_SESSION[authenticated])) {
//       $_SESSION['status'] = 'neverLogin';
//       header('Location: TaiHing.php');
//     }

    //檢查是否登錄,是否管理員
    require ("check_login.php");

     require($DOCUMENT_ROOT . "connect.inc");

     if ($_REQUEST[action] == 'new') {
	   $sql = "SELECT int_dept_id FROM tbl_dept_v_user WHERE int_user_id = $_SESSION[user_id]";
       $result = mysqli_query($con, $sql) or die($sql);
       $record = mysqli_fetch_array($result);
       $dept = $record[0];
       $sql = "SELECT MAX(int_no)+1 AS N_NO FROM tbl_notice;";
       $result = mysqli_query($con, $sql) or die($sql);
       $record = mysqli_fetch_array($result);
       $intno = $record[0];
	   
       $sql = "INSERT INTO tbl_notice (int_no, int_dept, int_user, date_create, date_modify, date_delete)";
       $sql .= " VALUES ('";
       $sql .= $intno;
       $sql .= "','";
       $sql .= $dept;
       $sql .= "','";
       $sql .= $_SESSION['user_id'];
       $sql .= "','";
       $sql .= date("Y-m-d");
       $sql .= "','";
       $sql .= date("Y-m-d");
       $sql .= "','";
       $sql .= "2000-01-02";
       $sql .= "')";
       $result = mysqli_query($con, $sql) or die($sql);
       echo "<script>document.location.href='CMS_notice.php';</script>";
     }
	 if ($_REQUEST[action] == 'del') {
		 $sql="SELECT txt_name FROM tbl_notice where int_no = $_REQUEST[int_no]";
		 $result = mysqli_query($con, $sql) or die($sql);
         $record = mysqli_fetch_array($result);
		 if($record["txt_name"]==null)
		 {
			 $sql= "DELETE FROM tbl_notice where int_no = $_REQUEST[int_no]";
			 mysqli_query($con, $sql) or die($sql);
		 }else{
			 $sql= "UPDATE  tbl_notice SET date_delete='".date("Y-m-d")."' where int_no = $_REQUEST[int_no]";
			 mysqli_query($con, $sql) or die($sql);
	     }
		 echo "<script>document.location.href='CMS_notice.php';</script>";
	 }
?>