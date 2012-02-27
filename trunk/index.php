<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 16, 2012
 * Time: 2:56:43 PM
 * To change this template use File | Settings | File Templates.
 */

    require_once('comando.php');

    echo Comando::display($_REQUEST['service']);
    exit;

?>
