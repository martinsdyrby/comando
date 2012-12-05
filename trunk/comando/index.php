<?php
    require_once('comando.php');
    $comando = new Comando();
    $comando->init($_REQUEST);
    $comando->display();
?>