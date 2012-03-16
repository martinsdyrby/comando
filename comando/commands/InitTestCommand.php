<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Mar 16, 2012
 * Time: 10:13:20 PM
 * To change this template use File | Settings | File Templates.
 */
 
class InitTestCommand extends AbstractValidationCommand {
    public function required() {
        return array();
    }
    
    public function optional() {
        return array();
    }
    
    public function doExecute() {
        $result = new ComandoResult();
        
        return $result;
    }
}
