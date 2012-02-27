<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 20, 2012
 * Time: 10:31:50 AM
 * To change this template use File | Settings | File Templates.
 */
 
class SaveUserHash extends EmarketeerCommand {

    public function execute(){
        date_default_timezone_set('CET');

        $hash = md5($this->emarketeer_id.''.HASH_SECRET);

        $comandoresult = Comando::execute('SendEmarketeerEmail', array(
            'emarketeer_id' => $this->emarketeer_id,
            'mid' => intval(HASH_EMAIL_ID),
            'upsert_key' => '11078_hash',
            'upsert_value' => $hash
        ));



        if($comandoresult->data('upsert_result')) {
            $comandoresult->setStatus(1);
        } else {
            $comandoresult->setStatus(0);
        }

        if($comandoresult->status() && !$comandoresult->data('mail_result')) {
            $comandoresult->setError($comandoresult->data('mail_error'));
        }

        return $comandoresult;
    }

}
