<?php

session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    header('Location: index.php');
}
if ($_SESSION[type] != 3) {
    header('Location: notadmin.php');
}