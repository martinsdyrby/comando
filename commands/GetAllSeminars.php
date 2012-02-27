<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 20, 2012
 * Time: 12:19:27 PM
 * To change this template use File | Settings | File Templates.
 */
 
class GetAllSeminars extends AbstractValidationCommand {

    protected $uid;

    public function required() {
        return array('uid');
    }
    
    public function doExecute() {
        $commandResult = new ComandoResult();

        $seminars = ORM::for_table(SEMINAR_TABLE)->where('user_id',$this->uid)->find_many();

        $formatted_seminars = array();

        foreach($seminars as $seminar) {
            $formatted_seminars[] = $seminar->as_array();
        }

        $commandResult->setData('seminars',$formatted_seminars);

        return $commandResult;
    }
}
