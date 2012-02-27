<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 16, 2012
 * Time: 9:56:18 PM
 * To change this template use File | Settings | File Templates.
 */
 
class SetPurlUsedInEmarketeer extends EmarketeerCommand {

    public function execute(){


        $comandoResult = new ComandoResult();
        $request =  'https://app.emarketeer.com/api/'.$this->apikey.'/upsert_contacts/';
        // urlencode and concatenate the POST arguments
        $postargs = 'contacts[0][id]='.$this->emarketeer_id.'&';
        $postargs .= 'contacts[0][c__11078_purl_used]='.date('Y-m-d H:i:s', time()).'&';
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

        if($obj->rest[0]->message == 'OK') {
            $comandoResult->setStatus(1);
        } else {
            $comandoResult->setStatus(0);
        }

        return $comandoResult;
    }
}
