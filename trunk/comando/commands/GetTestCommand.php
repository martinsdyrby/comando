<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Mar 16, 2012
 * Time: 10:12:35 PM
 * To change this template use File | Settings | File Templates.
 */
 
class GetTestCommand extends AbstractValidationCommand {
    public function required() {
        return array(FOO);
    }

    public function optional() {
        return array();
    }

    public function doExecute() {
        $result = new ComandoResult();
        $result->setData(FOO, parent::getParam(FOO));
        return $result;
    }
}
