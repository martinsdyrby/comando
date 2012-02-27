<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 16, 2012
 * Time: 9:34:59 PM
 * To change this template use File | Settings | File Templates.
 */
 
class EmarketeerCommand extends AbstractValidationCommand {
    protected $apikey = "K7D717BFUBML13X89D";
    protected $emarketeer_id;


    public function init($request){

        parent::init($request);

        $this->emarketeer_id = isset($request['emarketeer_id']) ? $request['emarketeer_id'] : null;

        if($this->emarketeer_id == null) {
            $user_id = isset($request['uid']) ? $request['uid'] : null;

            if($user_id != null) {
                $user = ORM::for_table(USER_TABLE)->find_one($user_id);

                if($user != null) {
                    $this->emarketeer_id = $user->emarketeer_id;
                }
            }
        }

        if($this->emarketeer_id != null) $this->is_valid = true;
    }

    public function required() {
        return array();
    }
}
