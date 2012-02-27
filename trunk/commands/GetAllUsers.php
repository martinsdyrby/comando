<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 17, 2012
 * Time: 1:14:46 PM
 * To change this template use File | Settings | File Templates.
 */
 
class GetAllUsers extends AbstractValidationCommand {

    protected $uid;

    public function required() {
        return array('uid');
    }

    public function doExecute() {

        $commandResult = new ComandoResult();

        try {
            $users = ORM::for_table(USER_TABLE)->raw_query("SELECT User.id,User.first_name,User.last_name,User.facebook_id,Company.company_name,challenger.timestamp as challenger_timestamp,challengee.timestamp as challengee_timestamp FROM `User` JOIN `Company` ON `User`.`company_id` = `Company`.`id` LEFT JOIN `Challenge` as challenger ON (challenger.challenger_id=User.id AND challenger.challengee_id=:user)
LEFT JOIN `Challenge` as challengee ON (challengee.challengee_id=User.id AND challengee.challenger_id=:user)",array('user' => $this->uid))->find_many();
        } catch (Exception $e) {
            $commandResult->setError($e->getMessage());
            return $commandResult;
        }

        $formatted_users = array();
        foreach($users as $user) {
            $formatted_users[] = array(
                'uid' => $user->id(),
                'username' => utf8_encode($user->first_name).' '.utf8_encode($user->last_name),
                'companyname' => $user->company_name,
                'facebook_id' => $user->facebook_id,
                'challenger' => ($user->challenger_timestamp != null) ? $user->challenger_timestamp : null,
                'challengee' =>  ($user->challengee_timestamp != null) ? $user->challengee_timestamp : null
            );
        }
        $commandResult->setData('users',$formatted_users);

        return $commandResult;
    }
}
