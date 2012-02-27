<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 16, 2012
 * Time: 9:28:06 PM
 * To change this template use File | Settings | File Templates.
 */
 
class GetEmarketeerIdByEmail extends EmarketeerCommand {

    protected $email;
    

    public function init($request) {
        $this->email = $request['email'];
    }

    public function execute() {
        $comandoResult = new ComandoResult();

        $response = file_get_contents("https://app.emarketeer.com/api/".$this->apikey."/contacts/&contacts[0][email]=".$this->email."&response=json");

        $obj = json_decode($response);
        if(isset($obj->rest->info) && $obj->rest->info == "No contacts") {
            $comandoResult->setError("Not found.");
            return $comandoResult;
        }
        foreach($obj->rest->contacts as $contact) {
            $comandoResult->setData("contact",$contact);
            return $comandoResult;
        }
    }
}
