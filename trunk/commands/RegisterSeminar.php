<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 20, 2012
 * Time: 11:14:18 AM
 * To change this template use File | Settings | File Templates.
 */
 
class RegisterSeminar extends AbstractValidationCommand {
    protected $uid;
    protected $date;
    protected $location;

    public function required() {
        return array('uid','date','location');
    }

    public function doExecute() {
        $seminar = ORM::for_table(SEMINAR_TABLE)->create();
        $seminar->user_id = $this->uid;
        $seminar->date = $this->date;
        $seminar->location = $this->location;

        $seminar_created = $seminar->save();

        if($seminar_created) {
            return Comando::execute('SaveUserPoints', array('uid' => $this->uid, 'points' => POINTS_FOR_CREATING_SEMINAR));
        }

        $commandResult = new ComandoResult();
        if($seminar_created) {
            $commandResult->setError('Seminar created, but points not saved.');
        } else {
            $commandResult->setError('Seminar not created.');
        }

        return $commandResult;
    }
}
