<?php
date_default_timezone_set('CET');

class Comando {

    static private $commands_path = 'commands';
    static public $services = array();

    static public function setService($serviceName, $commandName, $responseType, $requestType) {
        self::$services[$serviceName] = array(
            'commandClassName' => $commandName,
            'responseType' => $responseType,
            'requestType' => $requestType
        );
    }

    static public function display($service) {
        $method = isset($_POST['service']) ? RequestType::POST : RequestType::GET ;
        return self::doExecute($service, $method, $_REQUEST);
    }

    static public function init() {

        $config = include("config.comando.php");

        // SETUP COMMANDS PATH
        self::$commands_path = $config['commands-path']; 

        // SETUP SERVICES
        foreach($config['services'] as $serviceName => $service) {
            $service_parts = explode(';', $service);
            $num_parts = count($service_parts);
            switch($num_parts) {
                case 1:
                    Comando::setService($serviceName, $service_parts[0], ResponseType::JSON, RequestType::REQUEST);
                    break;
                case 2:
                    Comando::setService($serviceName, $service_parts[0], $service_parts[1], RequestType::REQUEST);
                    break;
                case 3:
                    Comando::setService($serviceName, $service_parts[0], $service_parts[1], $service_parts[2]);
                    break;
            }
        }


        // INCLUDE UTILS
        foreach($config['utils'] as $util) {
            if($util != '') require_once('utils/'.$util.'.php');
        }

        // EXECUTE INITS
        foreach($config['init'] as $initCommandName) {
            self::doExecute($initCommandName, RequestType::SCRIPT, $_REQUEST);
        }
    }

    static private function doExecute($commandName, $method, $initRequest = null, $log = true) {
        
		if(!isset(self::$services[$commandName])) return null;

		$service = self::$services[$commandName];
		$commandClassName = $service['commandClassName'];
		$responseType = $service['responseType'];
        $requestType = strtolower($service['requestType']);
        $method = strtolower($method);

        if($method != RequestType::SCRIPT) {

            if($requestType != RequestType::REQUEST) {
                if($requestType != $method) {
                    $result = new ComandoResult();
                    $result->setResponseType($responseType);
                    $result->setError("Service '".$commandName."' is not accessible through request type '".$method."'");
                    return $result;
                }
            }
        }

        $request = ($initRequest != null) ? $initRequest : $_REQUEST ;

        require_once("commands/".$commandClassName.".php");

        $comando = null;

        eval('$comando = new '.$commandClassName.'();');
        $comando->init($request);
        $result = $comando->execute();
        $result->setResponseType($responseType);

        if($log) {
            $log = new LogRequest();
            $log->command = $commandClassName;
            $log->init($initRequest);
            $log->execute();
        }

        return $result;
    }
    static public function execute($commandName, $initRequest = null, $log = true) {
        return self::doExecute($commandName, RequestType::SCRIPT, $initRequest, $log);
    }
}


class ComandoResult {

    private $status;
    private $data;
    private $error;
    private $responseType;

    public function status() {
        return $this->status;
    }

    public function data($key) {
        if($this->data == null) {
            return null;
        }
        if(!isset($this->data[$key])) return null;
        return $this->data[$key];
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }

    public function setError($error) {
        $this->status = 0;
        $this->error = $error;
    }

    public function setData($key, $value) {
        $this->status = 1;
        if($this->data == null) {
            $this->data = array();
        }
        $this->data[$key] = $value;
    }

    public function setResponseType($value) {
        $this->responseType = $value;
    }


    public function response() {
        switch($this->responseType) {
            case ResponseType::JSON:
                if($this->status == 0) {
                    return json_encode(array(
                        'status' => 0,
                        'error' => $this->error
                    ));
                }
                if($this->data != null) {
                    return json_encode(array(
                        'status' => 1,
                        'data' => $this->data
                    ));
                }
                return json_encode(array(
                    'status' => 1
                ));
                break;
        }
    }
}

class AbstractValidationCommand {
    protected $is_valid;

    private $params;

    public function init($request) {
        $params = array();
        
        $required_fields = $this->required();
        $this->is_valid = true;
 
        foreach($required_fields as $fieldName) {
            if(isset($request[$fieldName])) {
                $this->params[$fieldName] = $request[$fieldName];
            } else {
                $this->params[$fieldName] = null;
                $this->is_valid = false;
            }
        }

        $optional_fields = $this->optional();
        foreach($optional_fields as $fieldName) {
            if(isset($request[$fieldName])) {
                $this->$params[$fieldName] = $request[$fieldName];
            } else {
                $this->params[$fieldName] = null;
            }
        }
    }

    public function execute() {
        if($this->is_valid) {
            return $this->doExecute();
        }
        $comandoResult = new ComandoResult();
        $comandoResult->setError('Missing parameters in request.');
        return $comandoResult;
    }

    protected function doExecute() {
        throw new Exception("Abstract method 'doExecute()' must be overriden.");
    }

    protected function required() {
        throw new Exception("Abstract method 'required()' must be overriden.");
    }

    protected function optional() {
        return array();
    }

    protected function getParam($paramName) {
        return $this->params[$paramName];
    }
}

class LogRequest {

    public $command;

    public function init($request) {
        $this->request = $request;
    }

    public function execute() {
/*        $comando = ORM::for_table('comando')->create();
        $comando->command = $this->command;
        $comando->request = http_build_query($this->request);
        $comando->timestamp = date('Y-m-d H:i:s');
        $comando->save();*/
        return null;
    }
}


class ResponseType {
    const JSON = "json";
    const ACTO = "acto";
    const XML = "xml";
}


class RequestType {
    const GET = "get";
    const POST = "post";
    const REQUEST = "request";
    const SCRIPT = "script";
}

$comando = new Comando();
$comando->init();
?>