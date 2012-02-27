<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 16, 2012
 * Time: 9:06:33 PM
 * To change this template use File | Settings | File Templates.
 */
 
class ResolveHash extends AbstractValidationCommand {
    protected $h;
    public function required() {
        return array('h');
    }

    public function execute() {
        $comandoResult = new ComandoResult();
        $users = ORM::for_table(USER_TABLE)->find_many();
        $userObj = null;
        foreach($users as $user) {
            $hash = md5($user->emarketeer_id.''.HASH_SECRET);
            if($this->h == $hash) {
                $userObj = $user;
                break;
            }
        }

        if($userObj != null) {
            $company = ORM::for_table(COMPANY_TABLE)->find_one($userObj->company_id);
            $comandoResult->setData('user',$userObj);
            $comandoResult->setData('company',$company);

            $challengeResult = Comando::execute('GetChallenges',array('uid' => $userObj->id()));
            $comandoResult->setData('hasChallenged','false');
            if($challengeResult->status()) {
                $comandoResult->setData('hasChallenged','false');
                $today = date('Y-m-d');
                $challenges = $challengeResult->data('challenges');
                foreach($challenges as $challenge) {
                    if(substr($challenge['timestamp'], 0, 10) == $today) {
                        $comandoResult->setData('hasChallenged','true');
                    }
                }
            }
        } else {
            $comandoResult->setError('User not found.');
        }

        return $comandoResult;
    }
}
