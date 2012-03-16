<?php
class EmarketeerCommand extends AbstractValidationCommand {
    protected $apikey = "";
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


class GetEmarketeerIdByEmail extends EmarketeerCommand {

    protected $email;


    public function init($request) {
        $this->email = $request['email'];
    }

    public function execute() {
        $comandoResult = new ComandoResult();

        $request = "https://app.emarketeer.com/api/".$this->apikey."/contacts/&contacts[0][email]=".trim($this->email)."&response=json";

        $response = file_get_contents($request);

        $obj = json_decode($response);
        if(isset($obj->rest->info) && $obj->rest->info == "No contacts") {
            $comandoResult->setError("Not found.");
            return $comandoResult;
        }
        if(isset($obj->rest->contacts)) {
            foreach($obj->rest->contacts as $contact) {
                $comandoResult->setData("contact",$contact);
                return $comandoResult;
            }
        } else {
            $comandoResult->setStatus(0);
            return $comandoResult;
        }
    }
}




class CreateInEmarketeer extends EmarketeerCommand {

    public $user_id;

    public function init($request) {

        $this->user_id = isset($request['uid']) ? $request['uid'] : null;
        $this->is_valid = ($this->user_id != null);
    }

    public function doExecute() {
        $result = new ComandoResult();

        $user =  ORM::for_table(USER_TABLE)->find_one($this->user_id);
        if($user == null) {
            $result->setError('User not found.');
            return $result;
        }

        $company = ORM::for_table(USER_TABLE)->find_one($user->company_id);

        if($company == null) {
            $result->setError('Company not found.');
            return $result;
        }

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
        $result->setData('response',$response);
        if(isset($obj->rest[0]->message) && $obj->rest[0]->message == "OK") {
            $result->setData('emarketeer_id', $obj->rest[0]->id);
        } else {
            $result->setError('Error creating user in emarketeer.');
        }

        return $result;
    }
}






/****************************************************
 * SEND EMARKETEER EMAIL
 *
 *      mid - mail id
 * *************************************************/

class SendEmarketeerEmail extends EmarketeerCommand {

    const EMAIL_ID = 'emailId';
    const UPSERT_VALUE = 'upsertValue';
    const UPSERT_KEY = 'upsertKey';

    protected $mid;
    protected $upsert_value;
    protected $upsert_key;

    public function required() {
        return array(EMAIL_ID,UPSERT_VALUE,UPSERT_KEY);
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