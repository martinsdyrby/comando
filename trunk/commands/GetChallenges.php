<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 17, 2012
 * Time: 12:46:07 PM
 * To change this template use File | Settings | File Templates.
 */
 
class GetChallenges extends AbstractValidationCommand {

    protected $uid;
    
    public function required() {
        return array('uid');
    }

    public function doExecute() {
        $challenges = ORM::for_table(CHALLENGE_TABLE)->where_raw('(challenger_id=? or challengee_id=?)',array($this->uid,$this->uid))->find_many();

        $formatted_challenges = array();
        foreach($challenges as $challenge) {
            $formatted_challenges[] = $challenge->as_array();
        }
        $comandoResult = new ComandoResult();
        $comandoResult->setData('challenges',$formatted_challenges);
        return $comandoResult;
    }
}
