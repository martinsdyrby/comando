<?php

    // ******************************************************
    // DON'T EDIT START !!!
    // ******************************************************
    $config = array();
    $config['debug'] = true;
    $config['commands'] = array();
    $config['init'] = array();
    $config['expression'] = array();
    $config['logging'] = '';

    // ******************************************************
    // DON'T EDIT END !!!
    // ******************************************************



    // ******************************************************
    // SPECIFY YOUR COMMANDS HERE
    //
    //  RESPONSE-TYPE can be json, acto or xml
    //  REQUEST-TYPE can be:
    //      GET - limits execution to get requests as well as script
    //      POST - limits execution to post requests as well as script
    //      REQUEST - accepts execution for both GET and POST script
    //      SCRIPT - limits execution to script access only
    //
    // $config['commands']['COMMAND-ID'] = array(
    //      'type' => 'PATH-TO-CLASS.CLASS-NAME',
    //      'request' => 'REQUEST-TYPE',
    //      'response' => 'RESPONSE-TYPE',
    //      'params' => array(
    //          'key1' => 'value1',
    //          'key2' => 'value2'
    //      ),
    //      'restriction' => 'EXPRESSION-NAME',
    //      'restricted' => 'COMMAND-NAME'
    //  );
    //
    // ******************************************************



    // ******************************************************
    // INIT COMMAND - specify the ids of the commands to run
    // on init the command will recieve the request array
    //
    // $config['init'][] = 'COMMAND-ID';
    //
    // ******************************************************



    // ******************************************************
    // EXPRESSIONS
    //
    // $config['expression']['EXPRESSION-NAME'] = 'EXPRESSION';
    //
    //  Example. Given a command named login
    //
    // $config['expression']['verify_login'] = 'login.status == true';
    //
    // ******************************************************



    // ******************************************************
    // DON'T EDIT START !!!
    // ******************************************************
    return $config;
    // ******************************************************
    // DON'T EDIT END !!!
    // ******************************************************
?>


