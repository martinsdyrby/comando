<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Mar 16, 2012
 * Time: 10:13:40 PM
 * To change this template use File | Settings | File Templates.
 */
 
class ConstantsTestCommand extends AbstractValidationCommand {
    public function required() {
        return array();
    }
    
    public function optional() {
        return array();
    }
    
    public function doExecute() {
        define('FOO', 'foo');
        $result = new ComandoResult();
        $result->setStatus(1);
        return $result;
    }
}
