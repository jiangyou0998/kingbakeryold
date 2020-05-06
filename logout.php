<?php
session_start();
session_unset();
session_destroy();
setcookie('cookie_user_login', "");
setcookie('cookie_user_pwd', "");
header('Location: index.php');

?>