<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martindyrby
 * Date: 8/10/12
 * Time: 10:22 AM
 * To change this template use File | Settings | File Templates.
 */


class ActoTestCommand extends AbstractValidationCommand {
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

class PostTestCommand extends AbstractValidationCommand {

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

class ScriptTestCommand extends AbstractValidationCommand {
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
