<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 14, 2012
 * Time: 3:26:59 PM
 * To change this template use File | Settings | File Templates.
 */
date_default_timezone_set('CET');

class Comando {

    static public $instance;

    static public $services = array();

    static public function setService($serviceName, $commandName) {
        self::$services[$serviceName] = $commandName;
    }

    static public function display($service) {
        $comandoResult = self::execute(self::$services[$service]);
        return $comandoResult->response();
    }

    public function init() {

        self::$instance = $this;

        require_once("_init.php");
    }

    public function instance_execute($commandname, $initRequest, $log = true) {
        require_once("commands/".$commandname.".php");

        $comando = null;

        eval('$comando = new '.$commandname.'();');
        $comando->init($initRequest);
        $result = $comando->execute();
        
        if($log) {
            $log = new LogRequest();
            $log->command = $commandname;
            $log->init($initRequest);
            $log->execute();
        }
        return $result;
    }

    static public function execute($commandname, $initRequest = null, $log = true) {
        
        if($initRequest != null) return self::$instance->instance_execute($commandname, $initRequest, $log);
        return self::$instance->instance_execute($commandname, $_REQUEST, $log);
    }
}


class ComandoResult {

    private $status;
    private $data;
    private $error;

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


    public function response() {
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

    }
}

class AbstractValidationCommand {
    protected $is_valid;

    public function init($request) {
        $required_fields = $this->required();
        $this->is_valid = true;
 
        foreach($required_fields as $fieldName) {
            if(isset($request[$fieldName])) {
                $this->$fieldName = $request[$fieldName];
            } else {
                $this->$fieldName = null;
                $this->is_valid = false;
            }
        }

        $optional_fields = $this->optional();
        foreach($optional_fields as $fieldName) {
            if(isset($request[$fieldName])) {
                $this->$fieldName = $request[$fieldName];
            } else {
                $this->$fieldName = null;
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
}

class LogRequest {

    public $command;

    public function init($request) {
        $this->request = $request;
    }

    public function execute() {
        $comando = ORM::for_table('comando')->create();
        $comando->command = $this->command;
        $comando->request = http_build_query($this->request);
        $comando->timestamp = date('Y-m-d H:i:s');
        $comando->save();
        return null;
    }
}

$comando = new Comando();
$comando->init();
?>