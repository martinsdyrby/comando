<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 16, 2012
 * Time: 9:43:00 PM
 * To change this template use File | Settings | File Templates.
 */
 
class SaveEmarketeerId {
    private $emarketeer_id;
    private $user;

    public function init($request) {
        $this->emarketeer_id = isset($request['emarketeer_id']) ? $request['emarketeer_id'] : null;
        $this->user = isset($request['user']) ? $request['user'] : null;
    }
    public function execute() {
        $comandoResult = new ComandoResult();
        if($this->user != null && $this->emarketeer_id != null) {
            $this->user->emarketeer_id = $this->emarketeer_id;
            $comandoResult->setStatus($this->user->save());
        } else {
            $comandoResult->setError('Missing parameters in request.');
        }
    }
}
