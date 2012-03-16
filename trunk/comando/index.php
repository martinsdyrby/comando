<?php
    // ************************************
    // DON'T EDIT
    // ************************************

    require_once('comando.php');
    $comandoResult = Comando::display($_REQUEST['service']);
    if($comandoResult == null) echo '<h1>Result null</h1>';
    echo $comandoResult->response();
    exit;
?>