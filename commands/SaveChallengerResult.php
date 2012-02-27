<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 21, 2012
 * Time: 1:27:27 PM
 * To change this template use File | Settings | File Templates.
 */
 
class SaveChallengerResult extends AbstractValidationCommand {

    protected $cid;
    protected $result;

    public function required() {
        return array('cid','result');
    }
    
    public function doExecute() {
        $result = new ComandoResult();

        $challenge = ORM::for_table(CHALLENGE_TABLE)->find_one($this->cid);
        if($challenge != null) {
            $challenge->challenger_result_data = $this->result;
            $challenge->challenger_timestamp = date('Y-m-d H:i:s');

            $result->setStatus($challenge->save());
        } else {
            $result->setError('Challenge not found.');
        }

        return $result;
    }
}
