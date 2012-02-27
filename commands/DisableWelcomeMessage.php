<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 20, 2012
 * Time: 11:07:37 AM
 * To change this template use File | Settings | File Templates.
 */
 
class DisableWelcomeMessage extends AbstractValidationCommand {

    protected $uid;

    public function required() {
        return array('uid');
    }

    public function doExecute() {
        $comandoResult = new ComandoResult();

        $user = ORM::for_table(USER_TABLE)->find_one($this->uid);
        $user->disable_welcome_message = 1;
        if($user->save()) {
            $comandoResult->setStatus(1);
        } else {
            $comandoResult->SetStatus(0);
        }

        return $comandoResult;
    }

}
