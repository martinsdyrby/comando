<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 21, 2012
 * Time: 1:32:08 PM
 * To change this template use File | Settings | File Templates.
 */
 
class SaveChallengeeResult extends AbstractValidationCommand {

    protected $cid;
    protected $result;
    protected $wid;

    public function required() {
        return array('cid','result','wid');
    }
    
    public function optional() {
        return array();
    }
    
    public function doExecute() {
        $result = new ComandoResult();

        $challenge = ORM::for_table(CHALLENGE_TABLE)->find_one($this->cid);
        if($challenge != null) {
            $challenge->challengee_result_data = $this->result;
            $challenge->challengee_timestamp = date('Y-m-d H:i:s');
            $challenge->winner_id = $this->wid;

            $result->setStatus($challenge->save());

            if($result->status()) {
                if($this->cid==$this->wid) {
                    Comando::execute('SaveUserPoints', array('uid' => $challenge->challengee_id, 'points' => $challenge->points));
                    Comando::execute('SaveUserPoints', array('uid' => $challenge->challenger_id, 'points' => -1*intval($challenge->points)));
                } else {
                    Comando::execute('SaveUserPoints', array('uid' => $challenge->challengee_id, 'points' => -1*intval($challenge->points)));
                    Comando::execute('SaveUserPoints', array('uid' => $challenge->challenger_id, 'points' => $challenge->points));
                }

                $challengerUser = Comando::execute('GetUser', array('uid' => $challenge->challenger_id))->data('user');
                $challengeeUser = Comando::execute('GetUser', array('uid' => $challenge->challengee_id))->data('user');

                $emailResult = Comando::execute('SendEmarketeerEmail', array(
                    'emarketeer_id' => $challengerUser->emarketeer_id,
                    'mid' => RESULT_EMAIL_ID,
                    'upsert_key' => '11078_challenge',
                    'upsert_value' => $challengeeUser->first_name.' '.$challengeeUser->last_name
                ));

                if(!$emailResult->data('upsert_result')||!$emailResult->data('mail_result')) {
                    $result->setError('Error sending result email.');
                }
            }
        } else {
            $result->setError('Challenge not found.');
        }
        
        return $result;
    }
}
