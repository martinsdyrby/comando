<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 14, 2012
 * Time: 4:42:22 PM
 * To change this template use File | Settings | File Templates.
 */
    ini_set('display_errors', 1);
    error_reporting(-1);

    require_once('_constants.php');
    require_once('commands/EmarketeerCommand.php');

    Comando::$instance->setService('savefacebookids', 'SaveFacebookIds');
    Comando::$instance->setService('saveuserhash', 'SaveUserHash');
    Comando::$instance->setService('disablewelcomemessage', 'DisableWelcomeMessage');
    Comando::$instance->setService('registerseminar', 'RegisterSeminar');
    Comando::$instance->setService('getallseminars', 'GetAllSeminars');
    Comando::$instance->setService('getallusers', 'GetAllUsers');
    Comando::$instance->setService('savechallenge', 'SaveChallenge');
    Comando::$instance->setService('savechallengerresult', 'SaveChallengerResult');
    Comando::$instance->setService('savechallengeeresult', 'SaveChallengeeResult');
    Comando::$instance->setService('getchallenges', 'GetChallenges');
    Comando::$instance->setService('gethighscore', 'GetHighscore');
    Comando::$instance->setService('createpurls', 'CreatePurls');










    if($_SERVER['HTTP_HOST'] == 'www.deudvalgte.dk') {
        Comando::$instance->execute('SetupIdiorm', array(
            'host' => 'mysql:host=localhost;dbname=gen_8',
            'username' => 'gen_8',
            'password' => '45AvfaLVK7LuCVTf'
        ), false);
    } else {
        Comando::$instance->execute('SetupIdiorm', array(
            'host' => 'mysql:host=localhost;port=8889;dbname=gen8',
            'username' => 'root',
            'password' => 'root'
        ), false);
    }
?>