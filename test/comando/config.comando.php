<?php

    // ******************************************************
    // DON'T EDIT START !!!
    // ******************************************************
    $config = array();
    $config['classpath'] = "../";
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
    // $config['services']['COMMAND-ID'] = 'COMMAND-CLASS;RESPONSE-TYPE;REQUEST-TYPE';
    //
    // ******************************************************

    $config['commands']['gettest'] = array(
        'type' => 'Test.GetTestCommand',
        'request' => 'GET'
    );
    $config['commands']['posttest'] = array(
        'type' => 'Test.PostTestCommand',
        'request' => 'POST'
    );
    $config['commands']['requesttest'] = array(
        'type' => 'Test.RequestTestCommand',
    );
    $config['commands']['scripttest'] = array(
        'type' => 'Test.InternalTestCommand',
        'request' => 'SCRIPT'
    );
    $config['commands']['actotest'] = array(
        'type' => 'Test.ActoTestCommand'
    );
    $config['commands']['init'] = array(
        'type' => 'Test.InitTestCommand'
    );
    $config['commands']['constants'] = array(
        'type' => 'Test.ConstantsTestCommand',
        'requestType' => 'SCRIPT'
    );


    $config['commands']['test'] = array(
        'type' => 'utils.test.AjaxTestService',
        'params' => array(
            'services' => array(
                array(
                    'service' => 'gettest',
                    'request' => 'GET',
                    'fields' => array('foo')
                ),
                array(
                    'service' => 'posttest',
                    'request' => 'POST',
                    'fields' => array('foo')
                ),
                array(
                    'service' => 'requesttest',
                    'request' => 'GET',
                    'fields' => array('foo')
                ),
                array(
                    'service' => 'scripttest',
                    'request' => 'GET',
                    'fields' => array('foo')
                ),
            )
        )
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

    $config['utils'][] = '';










    // ******************************************************
    // DON'T EDIT START !!!
    // ******************************************************
    return $config;
    // ******************************************************
    // DON'T EDIT END !!!
    // ******************************************************
?>


