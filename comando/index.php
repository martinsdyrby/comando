<?php
    // ************************************
    // DON'T EDIT
    // ************************************
    require_once('comando.php');
    $comandoResult = Comando::display($_REQUEST['service']);
    echo $comandoResult->response();
    exit;
?>