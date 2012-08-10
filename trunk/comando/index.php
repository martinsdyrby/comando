<?php
    // ************************************
    // DON'T EDIT
    // ************************************
    require_once('comando.php');
    $comando = new Comando();
    $comando->init($_REQUEST);

    if(isset($_REQUEST['service'])) {
        $result = $comando->display($_REQUEST['service']);

        if($result != null) {
            if($result->hasLocation()) {
                header('Location: '.$result->location());
                exit;
            } else {
                $response = $result->response();
                if(isset($_REQUEST['jsonp_callback'])) {
                    echo $_REQUEST['jsonp_callback'].'('.$response.');';
                } else {
                    echo $response;
                }
            }
        } else {
            echo 'No result';
        }
    } else {
        echo 'No service';
    }
?>