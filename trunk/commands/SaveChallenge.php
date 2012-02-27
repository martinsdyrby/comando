<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 20, 2012
 * Time: 12:35:08 PM
 * To change this template use File | Settings | File Templates.
 */
 
class SaveChallenge extends AbstractValidationCommand {
    protected $challenger;
    protected $challengee;
    protected $quiz;
    protected $points;

    public function required() {
        return array('challenger','challengee','quiz','points');
    }
    
    public function doExecute() {
        $commandResult = new ComandoResult();

        $challenge = ORM::for_table(CHALLENGE_TABLE)->create();
        $challenge->challenger_id = $this->challenger;
        $challenge->challengee_id = $this->challengee;
        $challenge->quiz_data = $this->quiz;
        $challenge->points = $this->points;

        if($challenge->save()) {
            $commandResult->setData('challenge_id', $challenge->id());
        } else {
            $commandResult->setError('An error occured saving the challenge.');
        }

        $challengerUser = Comando::execute('GetUser', array('uid' => $this->challenger))->data('user');
        $challengeeUser = Comando::execute('GetUser', array('uid' => $this->challengee))->data('user');

        $emailResult = Comando::execute('SendEmarketeerEmail', array(
            'emarketeer_id' => $challengeeUser->emarketeer_id,
            'mid' => CHALLENGE_EMAIL_ID,
            'upsert_key' => '11078_challenge',
            'upsert_value' => $challengerUser->first_name.' '.$challengerUser->last_name
        ));
        
        if($emailResult->data('upsert_result')) {
            $commandResult->setStatus(1);
        } else {
            $commandResult->setStatus(0);
        }

        if($commandResult->status() && !$emailResult->data('mail_result')) {
            $commandResult->setError($emailResult->data('mail_error'));
        }

        return $commandResult;
    }
}
