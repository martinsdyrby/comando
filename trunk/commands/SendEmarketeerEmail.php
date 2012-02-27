
<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 21, 2012
 * Time: 1:53:54 PM
 * To change this template use File | Settings | File Templates.
 */
 
class SendEmarketeerEmail extends EmarketeerCommand {

    protected $mid;
    protected $upsert_value;
    protected $upsert_key;

    public function required() {
        return array('mid','upsert_value','upsert_key');
    }
    
    public function optional() {
        return array();
    }
    
    public function doExecute() {
        $result = new ComandoResult();

    // The request URL prefix
        $request =  'https://app.emarketeer.com/api/'.$this->apikey.'/upsert_contacts/';
        // urlencode and concatenate the POST arguments
        $postargs = 'contacts[0][id]='.$this->emarketeer_id.'&';
        $postargs .= 'contacts[0][c__'.$this->upsert_key.']='.$this->upsert_value.'&';
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

        $obj = json_decode($response);

        $result->setData('upsert_result',($obj->rest[0]->message == 'OK'));

        // The request URL prefix
        $request =  'https://app.emarketeer.com/api/'.$this->apikey.'/enqueue/';
        // urlencode and concatenate the POST arguments
        $postargs = 'contacts[0][id]='.$this->emarketeer_id.'&';
        $postargs .= 'type=mail&';
        $postargs .= 'id='.$this->mid.'&';
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

        $obj = json_decode($response);

        if(property_exists($obj->rest, 'enqueue')) {
            $result->setData('mail_result', true);
        } else {
            $result->setData('mail_result', false);
            $result->setData('mail_error', $obj->rest->error[0]);
        }

        return $result;
    }
}
