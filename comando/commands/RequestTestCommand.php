<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Mar 16, 2012
 * Time: 10:12:14 PM
 * To change this template use File | Settings | File Templates.
 */
 
class RequestTestCommand extends AbstractValidationCommand {
    const FOO = "foo";

    public function required() {
        return array(self::FOO);
    }

    public function optional() {
        return array();
    }

    public function doExecute() {
        $result = new ComandoResult();
        $result->setData(self::FOO, $this->getParam(self::FOO));
        return $result;
    }
}
