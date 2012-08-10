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
            header('HTTP/1.0 404 Not Found');
        }
    } else {
        header('HTTP/1.0 404 Not Found');
    }
?>