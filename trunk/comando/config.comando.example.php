<?php

    // ******************************************************
    // DON'T EDIT START !!!
    // ******************************************************
    $config = array();
    $config['commands'] = array();
    $config['init'] = array();
    $config['utils'] = array();
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
    // $config['services']['COMMAND-ID'] = 'COMMAND-CLASS;RESPONSE-TYPE;REQUEST-TYPE';
    //
    // ******************************************************

    $config['commands']['gettest'] = array(
        'type' => 'GetTestCommand',
        'requestType' => 'GET'
    );
    $config['services']['posttest'] = array(
        'type' => 'PostTestCommand',
        'requestType' => 'POST'
    );
    $config['services']['requesttest'] = array(
        'type' => 'RequestTestCommand',
    );
    $config['services']['scripttest'] = array(
        'type' => 'InternalTestCommand',
        'requestType' => 'SCRIPT'
    );
    $config['services']['actotest'] = array(
        'type' => 'ActoTestCommand'
    );
    $config['services']['init'] = array(
        'type' => 'InitTestCommand'
    );
    $config['services']['constants'] = array(
        'type' => 'ConstantsTestCommand',
        'requestType' => 'SCRIPT'
    );





    // ******************************************************
    // INIT COMMAND - specify the ids of the commands to run
    // on init the command will recieve the request array
    //
    // $config['init'][] = 'COMMAND-ID';
    //
    // ******************************************************

    $config['init'][] = 'init';
    $config['init'][] = 'constants';



    // ******************************************************
    // UTILS
    // Include utils
    //
    // $config['utils'][] = 'UTIL-NAMES';
    //
    // ******************************************************

    $config['utils'][] = 'emarketeer';
    $config['utils'][] = '';










    // ******************************************************
    // DON'T EDIT START !!!
    // ******************************************************
    return $config;
    // ******************************************************
    // DON'T EDIT END !!!
    // ******************************************************
?>


