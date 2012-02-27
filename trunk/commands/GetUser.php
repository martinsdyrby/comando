<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 23, 2012
 * Time: 2:49:26 PM
 * To change this template use File | Settings | File Templates.
 */
 
class GetUser extends AbstractValidationCommand {
    protected $uid;
    public function required() {
        return array('uid');
    }
    
    public function optional() {
        return array();
    }
    
    public function doExecute() {
        $result = new ComandoResult();
        $user = ORM::for_table(USER_TABLE)->join(COMPANY_TABLE,COMPANY_TABLE.'.id='.USER_TABLE.'.company_id')->where(USER_TABLE.'.id',$this->uid)->find_many();
        $userObj = null;
        if(count($user) > 0) {
            $userObj = $user[0];
        }
        if($userObj != null) {
            $result->setData('user',$userObj);
        } else {
            $result->setStatus(0);
        }

        return $result;
    }
}
