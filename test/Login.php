<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: May 15, 2012
 * Time: 1:54:09 PM
 * To change this template use File | Settings | File Templates.
 */

class Login extends AbstractValidationCommand {
    const USERNAME = "username";
    const PASSWORD = "password";

    public function required() {
        return array('u','p');
    }
    
    public function optional() {
        return array(self::USERNAME, self::PASSWORD);
    }
    
    public function doExecute() {
        $result = new ComandoResult();
        $username = $this->getParam(self::USERNAME);
        $password = $this->getParam(self::PASSWORD);
        $u = $this->getParam('u');
        $p = $this->getParam('p');
        if($username == null && $password == null) {
           if(isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
                $_SESSION['logged']=true;
                $result->setStatus(1);
           } else {
                $_SESSION['logged']=false;
                $result->setStatus(0);
           }
        } else if($username == $u && $password == $p) {
            $_SESSION['logged']=true;
            $result->setStatus(1);
        } else {
            $_SESSION['logged']=false;
            $result->setStatus(0);
        }
        return $result;
    }
}

class SetConf extends AbstractValidationCommand {
    const LEGAL_TEXT_PC = "legalTextPc";
    const LEGAL_TEXT_MOBILE = "legalTextMobile";
    const RATE_MONTH = "rateMonth";
    const RATE_TOTAL = "rateTotal";
    public function required() {
        return array(
            self::LEGAL_TEXT_PC, 
            self::LEGAL_TEXT_MOBILE,
            self::RATE_MONTH,
            self::RATE_TOTAL);
    }

    public function optional() {
        return array();
    }

    public function doExecute() {
        $result = new ComandoResult();

        $legalTextPc = $this->getParam(self::LEGAL_TEXT_PC);
        $legalTextMobile = $this->getParam(self::LEGAL_TEXT_MOBILE);
        $rateMonth = $this->getParam(self::RATE_MONTH);
        $rateTotal = $this->getParam(self::RATE_TOTAL);

        $entity = ORM::for_table("conf")->create();
        $entity->legal_text_pc = $legalTextPc;
        $entity->legal_text_mobile = $legalTextMobile;
        $entity->rate_month = $rateMonth;
        $entity->rate_total = $rateTotal;

        if($entity->save()) {
            $result->setStatus(1);
        } else {
            $result->setStatus(0);
        }
        
        return $result;
    }
}

class GetConf extends AbstractValidationCommand {
    const LEGAL_TEXT_PC = "legalTextPc";
    const LEGAL_TEXT_MOBILE = "legalTextMobile";
    const RATE_MONTH = "rateMonth";
    const RATE_TOTAL = "rateTotal";
    public function required() {
        return array();
    }

    public function optional() {
        return array();
    }

    public function doExecute() {
        $result = new ComandoResult();
        $entity = ORM::for_table("conf")->order_by_desc('id')->find_one();
        $result->setData(self::LEGAL_TEXT_PC, $entity->legal_text_pc);
        $result->setData(self::LEGAL_TEXT_MOBILE, $entity->legal_text_mobile);
        $result->setData(self::RATE_MONTH, $entity->rate_month);
        $result->setData(self::RATE_TOTAL, $entity->rate_total);

        return $result;
    }
}
