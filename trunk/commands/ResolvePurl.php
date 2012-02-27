<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 15, 2012
 * Time: 11:20:03 AM
 * To change this template use File | Settings | File Templates.
 */
 
class ResolvePurl {

    private $purl;

    public function init($request) {
        if(!isset($request['pid']) || !isset($request['uid'])) {
            $this->purl="";
        } else {
            if(isset($request['ran'])) {
                $this->purl = 'http://www.deudvalgte.dk/'.$request['ran'].'/'.$request['pid'].'/'.$request['uid'];

            } else {
                $this->purl = 'http://www.deudvalgte.dk/'.$request['pid'].'/'.$request['uid'];
            }

            $this->purl = strtolower($this->purl);
        }
    }

    public function execute() {

        $commandResult = new ComandoResult();


        $user = ORM::for_table(USER_TABLE)->where('purl',$this->purl)->find_one();

        if($user != null) {
            $company = ORM::for_table(COMPANY_TABLE)->find_one($user->company_id);
            $commandResult->setData('user',$user);
            $commandResult->setData('company',$company);
        } else {
            $commandResult->setError('User not found.');
        }

        return $commandResult;

    }
}
