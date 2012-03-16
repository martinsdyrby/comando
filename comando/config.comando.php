<?php

    // ******************************************************
    // DON'T EDIT START !!!
    // ******************************************************
    $config = array();
    $config['services'] = array();
    $config['init'] = array();
    $config['utils'] = array();
    // ******************************************************
    // DON'T EDIT END !!!
    // ******************************************************





    // ******************************************************
    // PUT THE PATH TO THE FOLDER
    // WHERE YOUR COMMANDS WILL RESIDE
    // ******************************************************
    $config['commands-path'] = 'commands';







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

    $config['services']['gettest'] = 'PostTestCommand;json;POST';
    $config['services']['posttest'] = 'GetTestCommand;json;GET';
    $config['services']['requesttest'] = 'AllTestCommand;json;REQUEST';
    $config['services']['scripttest'] = 'InternalTestCommand;json;SCRIPT';
    $config['services']['actotest'] = 'ActoTestCommand;acto;REQUEST';
    $config['services']['init'] = 'InitCommand;json;SCRIPT';
    $config['services']['constants'] = 'ConstantsCommand;json;SCRIPT';





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


