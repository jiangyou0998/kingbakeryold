<?php
  include("connect.inc");
  require('CMS_check_order_z_all.php');

  for($count = 0; $count < 35; $count++){
	
	order_z_dept($count, "1000");
  }
  

?>