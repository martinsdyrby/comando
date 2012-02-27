<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 16, 2012
 * Time: 2:54:49 PM
 * To change this template use File | Settings | File Templates.
 */
 
class SaveFacebookIds {
    private $userID;
    private $facebookID;
    private $friend1ID;
    private $friend2ID;
    private $friend3ID;
    private $friend4ID;

    public function init($request) {
        $this->userID       = $request['uid'];
        $this->facebookID   = $request['fbid'];
        $this->friend1ID    = $request['f1id'];
        $this->friend2ID    = $request['f2id'];
        $this->friend3ID    = $request['f3id'];
        $this->friend4ID    = $request['f4id'];
    }

    public function execute() {
        $user = ORM::for_table(USER_TABLE)->find_one($this->userID);
        $user->facebook_id = $this->facebookID;
        $user->friend_facebook_ids = $this->friend1ID.";".$this->friend2ID.";".$this->friend3ID.";".$this->friend4ID;

        $success = $user->save();
        
        $result = new Comandoresult();
        $result->setStatus($success);

        return $result;
    }
}
