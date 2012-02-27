<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 22, 2012
 * Time: 12:19:25 PM
 * To change this template use File | Settings | File Templates.
 */
 
class CreateInEmarketeer extends EmarketeerCommand {

    public $user_id;

    public function init($request) {

        $this->user_id = isset($request['uid']) ? $request['uid'] : null;
        $this->is_valid = ($this->user_id != null);
    }

    public function doExecute() {
        $result = new ComandoResult();

        $user =  ORM::for_table(USER_TABLE)->find_one($this->user_id);
        $company = ORM::for_table(USER_TABLE)->find_one($user->company_id);

        // The request URL prefix
        $request =  'https://app.emarketeer.com/api/'.$this->apikey.'/upsert_contacts/';
        // urlencode and concatenate the POST arguments
        $postargs = 'contacts[0][firstname]='.$user->first_name.'&';
        $postargs .= 'contacts[0][lastname]='.$user->last_name.'&';
        $postargs .= 'contacts[0][email]='.$user->email.'&';
        $postargs .= 'contacts[0][company]='.$company->company_name.'&';
        $postargs .= 'response=json';

        $session = curl_init($request);

        // Tell curl to use HTTP POST
        curl_setopt ($session, CURLOPT_POST, true);
        // Tell curl that this is the body of the POST
        curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
        // Tell curl not to return headers, but do return the response
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($session);
        curl_close($session);

        $obj = json_decode($response); // 25004731

        if($obj->rest[0]->message == "OK") {
            $result->setData('emarketeer_id', $obj->rest[0]->id);
        } else {
            $result->setError('Error creating user in emarketeer.');
        }

        return $result;
    }
}
