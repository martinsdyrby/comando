<?php session_start();
date_default_timezone_set('CET');

class Comando {

	static public $instance = null;
	const ENDPOINTS = "endpoints";
	const RESOLVE_EXPRESSION = "resolveExpression";

    private $services = array();
    private $_request_params;

    public function setService($serviceName, $commandName, $responseType, $requestType, $restriction, $restricted, $params) {
        $this->services[$serviceName] = array(
            'type' => $commandName,
            'response' => $responseType,
            'request' => $requestType,
            'restriction' => $restriction,
            'restricted' => $restricted,
            'params' => $params,
        );
    }

    public function init($request) {

        self::$instance = $this;

        $this->_request_params = $request;

        $this->services = array();

        $config = include("config.comando.php");


		// SETUP SERVICES
		$tmpcommands = $config['commands'];
        foreach($tmpcommands as $command_name => $command_data) {

			$command_type = $command_data['type'];

            $command_response = isset($command_data['response']) ? $command_data['response'] : 'json';

            $command_request = isset($command_data['request']) ? $command_data['request'] : 'request';

            $command_restriction = isset($command_data['restriction']) ? $command_data['restriction'] : '';

            $command_restricted = isset($command_data['restricted']) ? $command_data['restricted'] : '';

            $command_params = isset($command_data['params']) ? $command_data['params'] : array();
        
			$this->setService($command_name, $command_type, $command_response, $command_request, $command_restriction, $command_restricted, $command_params);
        }

		$this->setService(self::ENDPOINTS, 'CreateAltEndpointsDocument', 'raw', 'request', '', '', array());
		$this->setService(self::RESOLVE_EXPRESSION, 'ResolveExpression', 'json', 'script', '', '', array());

		/* SETUP EXPRESSIONS */
		$this->expressions = $config['expression'];

		/* SETUP LOGGING */
		if(isset($config['logging'])){
			$this->logging = $config['logging'];
        } else {
            $this->logging = '';
        }


		/* EXECUTE INITS */
		$tmpinits = $config['init'];
        foreach($tmpinits as $init_command_name) {
            $this->doExecute($init_command_name, 'script', $this->_request_params, true);
        }
    }

    public function execute($commandName, $initRequest = null) {
        return $this->doExecute($commandName, RequestType::SCRIPT, $initRequest, true);
    }

    public function display($service) {
        try {
            $method = isset($_POST['service']) ? RequestType::POST : RequestType::GET ;
            return $this->doExecute($service, $method, $_REQUEST);
        } catch (Exception $e) {
            $log_error = $e->getMessage()."\nFound in ".$e->getFile()." on line ".$e->getLine()."\n\nTrace:\n".$e->getTraceAsString()."\n\n".http_build_query($_REQUEST);
            $result = new ComandoResult();
            $result->setError($log_error);
            return $result;
        }
    }

    private function doExecute($commandName, $method, $initRequest = null, $doLog = true) {
		if(!isset($this->services[$commandName])) {
            return null;
        }


		$service = $this->services[$commandName];
		$command_class_name = $service['type'];
		$response_type = strtolower($service['response']);
        $request_type = strtolower($service['request']);
        $restriction = strtolower($service['restriction']);
        $restricted = $service['restricted'];
        $params = $service['params'];

        $method = strtolower($method);

        $request = ($initRequest != null) ? $initRequest : $this->_request_params;

		if ($commandName == self::ENDPOINTS){
            $request['services'] = $this->services;
        }

		if ($restriction != ''){
			// resolve restriction
			$restrictionResult = $this->runCommand("ResolveExpression", array(ResolveExpression::EXPRESSION => $this->expressions[$restriction]));
			$proceed = $restrictionResult->status() == 1;

			if ($proceed == false && $restricted != ''){
				return $this->doExecute($restricted, "script", $request, $doLog);
            }
        } else {
			$proceed = true;
        }



        if ($proceed == false){
            header("HTTP/1.0 403 Forbidden");
            exit;
/*			$result = new ComandoResult();
			$result->setResponseType($response_type);
			$result->setError("Service '".$commandName."' is restricted");
			return $result;*/
        }

        if($method != RequestType::SCRIPT) {

            if($request_type != RequestType::REQUEST) {
                if($request_type != $method) {
                    $result = new ComandoResult();
                    $result->setResponseType($response_type);
                    $result->setError("Service '".$commandName."' is not accessible through request type '".$method."'");
                    return $result;
                }
            }
        }

        
        foreach ($params as $key => $value){
            $request[$key] = $value;
        }

		$result = $this->runCommand($command_class_name, $request);
		$result->setResponseType($response_type);
		if ($doLog == true && $this->logging != '') {
            $this->runCommand($this->logging, array('command'=> $commandName . " (".$command_class_name.")", 'request'=> $request));
        }

		return $result;
    }

	private function runCommand($command_class_name, $request){

        $modulepart = explode('.', $command_class_name);
        $command_class_name = array_pop($modulepart);
        if(count($modulepart)>0) {
            require_once('../'.implode('/',$modulepart).".php");
        }

        $command = null;
        eval('$command = new '.$command_class_name.'();');
        $command->init($request);

        return $command->execute();
    }

	private function runAuthCommand($command_class_name, $request) {
        return $this->runCommand($command_class_name, $request);
    }
}


class ComandoResult {

    private $_status = 0;
    private $_data = null;
    private $_error = null;
    private $_responseType = 'json';
    private $_output = '';
    private $_location = '';

    public function status() {
        return $this->_status;
    }

    public function data($key) {
        if($key == 'status') return $this->_status;
        if($this->_data == null) {
            return null;
        }
        if(!isset($this->_data[$key])) return null;
        return $this->_data[$key];
    }
    
    public function setStatus($status) {
        $this->_status = $status;
    }

    public function setError($error) {
        $this->_status = 0;
        $this->_error = $error;
    }

    public function setData($key, $value) {
        $this->_status = 1;
        if($this->_data == null) {
            $this->_data = array();
        }
        $this->_data[$key] = $value;
    }

    public function setOutput($value) {
        $this->_output = $value;
    }

    public function setResponseType($value) {
        $this->_responseType = $value;
    }

    public function setLocation($value) {
        $this->_location = $value;
    }

    public function hasLocation() {
        return $this->_location != '' && $this->_location != null;
    }

    private function array_utf8_encode_recursive($dat) {
        if (is_string($dat)) {
            return utf8_encode($dat);
        }
        if (is_object($dat)) {
            $ovs= get_object_vars($dat);
            $new=$dat;
            foreach ($ovs as $k =>$v)    {
                $new->$k=$this->array_utf8_encode_recursive($new->$k);
            }
            return $new;
        }

        if (!is_array($dat)) return $dat;
        $ret = array();
        foreach($dat as $i=>$d) $ret[$i] = $this->array_utf8_encode_recursive($d);
        return $ret;
    }

    public function response() {
        switch($this->_responseType) {
            case ResponseType::JSON:
                header("Content-Type: application/json; charset=UTF-8");
/*                if($this->_status == 0) {
                    return json_encode(array(
                        'status' => false,
                        'error' => $this->array_utf8_encode_recursive($this->_error)
                    ));
                }*/
                if($this->_data != null) {
                    $this->_data['status'] = ($this->_status == 1);
                    if($this->_error != null) {
                        $this->_data['error'] = $this->array_utf8_encode_recursive($this->_error);
                    }
                    return json_encode($this->array_utf8_encode_recursive($this->_data));
                }
                if($this->_error != null) {
                    return json_encode(array(
                        'status' => ($this->_status == 1),
                        'error' => $this->array_utf8_encode_recursive($this->_error)
                    ));
                }

                return json_encode(array(
                    'status' => ($this->_status == 1)
                ));
                break;
            case ResponseType::XML:
                return '<comando><status>'.$this->_status.'</status></comando>';
                break;
            case ResponseType::RAW:
                if ($this->_output == ''){
                    if ($this->_status == 0){
                        if ($this->_error != null) {
                            return 'Error: '.$this->_error;
                        }else{
                            return 'Error';
                        }
                    }else{
                        return 'success';
                    }

                }else{
                    return $this->_output;
                }
                break;
        }
    }
}

class AbstractValidationCommand {
    protected $is_valid;
    protected $request;
    private $params;

    public function init($request) {
        $this->request = $request;
        $this->params = array();
        
        $required_fields = $this->required();
        $this->is_valid = true;
 
        foreach($required_fields as $fieldName) {
            if(isset($request[$fieldName])) {
                if(is_string($request[$fieldName])) {
                    $this->params[$fieldName] = htmlentities(strip_tags($request[$fieldName]), ENT_QUOTES);
                } else {
                    $this->params[$fieldName] = $request[$fieldName];
                }

            } else {
                $this->params[$fieldName] = null;
                $this->is_valid = false;
            }
        }

        $optional_fields = $this->optional();
        foreach($optional_fields as $fieldName) {
            if(isset($request[$fieldName])) {
                if(is_string($request[$fieldName])) {
                    $this->params[$fieldName] = htmlentities(strip_tags($request[$fieldName]), ENT_QUOTES);
                } else {
                    $this->params[$fieldName] = $request[$fieldName];
                }
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
        return (isset($this->params[$paramName])) ? $this->params[$paramName] : null;
    }
    protected function getRawParam($paramName) {
        return (isset($this->params[$paramName])) ? $_REQUEST[$paramName] : null;
    }
}


class AbstractHttpAuthenticationCommand {

    protected $username;
    protected $password;

	public function execute() {
		if (!isset($_SERVER['PHP_AUTH_USER'])){
            header('WWW-Authenticate: Basic realm=""');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authorization required';
        }else{
			$this->username = $_SERVER['PHP_AUTH_USER'];
			$this->password = $_SERVER['PHP_AUTH_PW'];
			return $this->doExecute();
        }
		return new ComandoResult();
    }
}



class CreateAltEndpointsDocument extends AbstractValidationCommand {
	protected function required(){
		return array('services');
    }
	protected function optional(){
		return array();
    }
	protected function doExecute(){
        $comando = Comando::$instance;
        $result = new ComandoResult();

        $formatted_services = array();
  /*
        $p_endpoint = '^/***\s*Endpoint$';
        $p_description = '^\s*Description$';
        $p_request = '^\s*Request$';
        $p_response_data = '^\s*Response data$';
        $p_response_errors = '^\s*Response errors$';
  */
        $services = $this->getParam('services');

          foreach($services as $serviceName => $service){
            if ($serviceName == Comando::ENDPOINTS || $serviceName == Comando::RESOLVE_EXPRESSION){
                continue;
              }

            /* for each service read file */
            $command_class_name = $service['type'];
            $command_class_name_part = explode('.', $command_class_name);
            $command_name = array_pop($command_class_name_part);
            $command_path = implode("/", $command_class_name_part);

            $formatted_services[$serviceName] = array(
                  'class_contents' => '',
                  'description' => '',
                  'request' => array(),
                  'data' => array(),
                  'errors' => array()
              );

            $contents = file_get_contents('../'.$command_path.'.php');

            $class_index = strpos($contents, 'class '.$command_name);
            if($class_index == -1) {
                continue;
              }

            $class_contents = substr($contents, 0, $class_index);
            $prev_class_index = strrpos($class_contents,'class');
            $description_index = strrpos($class_contents,'Description');
            $request_index = strrpos($class_contents,'Request');
            $response_data_index = strrpos($class_contents,'Response data');
            $response_errors_index = strrpos($class_contents,'Response errors');

            if($description_index > -1 && $description_index > $prev_class_index){
                $description_contents = substr($class_contents, $description_index+11, $request_index - ($description_index+11));
                $request_contents = substr($class_contents,$request_index+7, $response_data_index - ($request_index+7));
                $response_data_contents = substr($class_contents, $response_data_index+13, $response_errors_index - ($response_data_index+13));
                $response_errors_contents = substr($class_contents, $response_errors_index+15);
              } else {
                $description_contents = '';
                $request_contents = '';
                $response_data_contents = '';
                $response_errors_contents = '';
              }

            $formatted_services[$serviceName]['description'] = $description_contents;
            $requests = explode("\n", $request_contents);

            $formatted_services[$serviceName]['request'] = $requests;
            $response_data = explode("\n", $response_data_contents);
            $formatted_services[$serviceName]['data'] = $response_data_contents;
            $response_errors = explode("\n", $response_errors_contents);
            $formatted_services[$serviceName]['errors'] = $response_errors;
            $formatted_services[$serviceName]['class_contents'] = $request_contents;

          }
        /* add services to output */
          $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
          if ($_SERVER["SERVER_PORT"] != "80")
          {
              $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
          }
          else
          {
              $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
          }
        $endpoints_index = strpos($pageURL, Comando::ENDPOINTS);
        $baseurl = substr($pageURL, 0, $endpoints_index);

           $output = <<<EOD
  <html>
    <head>
        <title>Endpoints</title>
        <style type="text/css">
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #D0C7AB;
            }
            .content {}
            h1 {
                font-size: 36;
                color: #C56212
            }
            h2 {
                font-size: 18;
                color: #22384D
            }
            h3 {
                font-size: 14;
                color: #22384D
            }
            p {
                font-size: 12;
                color: #000
            }
            ul {
                list-style-type: square;
                font-size: 12;
                color: #000
            }
            .header {
                width: 100%;
                height: 49;
                padding: 10px;
                background-color: #D0C7AB;
            }
            .separator { margin-top: 15px; width: 100%; height: 5px; background-color: #C56212; }
            .subheader { width: 100%; height: 5px; background-color: #C56212; }
            .subheader2 { width: 100%; height: 6px; background-color: #768A4F; }
            .subheader3 { width: 100%; height: 8px; background-color: #22384D; }
            .description { float: left; padding-right: 20px; width: 300px;}
            .clear { clear: both; }
            .service { padding: 10px; }
        </style>
    </head>
    <body>
        <div class="header"><h1>Endpoints</h1></div>
        <div class="subheader"></div>
        <div class="subheader2"></div>
        <div class="subheader3"></div>
        <div class="content">
EOD;

        foreach($formatted_services as $serviceName => $twain){
            $output .= '<div class="service">';
            $output .= '<h2>'.$baseurl.$serviceName.'</h2>';
            $output .= '<div class="description">';
            $output .= '<h3>Description</h3>';
            $output .= '<p>'.$formatted_services[$serviceName]['description'].'</p>';
            $output .= '</div>';

            $output .= '<div class="description">';
            $output .= '<h3>Request parameters</h3>';
            $output .= '<ul>';

            foreach($formatted_services[$serviceName]['request'] as $twains=>$param){
                if (trim($param) != ''){
                    $output .= '<li>'.$param.'</li>';
                  }
              }

            $output .= '</ul>';
            $output .= '</div>';

            $output .= '<div class="description">';
            $output .= '<h3>Response data</h3>';
            $output .= '<pre>';
              $output .= str_replace("\t", " ", $formatted_services[$serviceName]['data']);
  /*            foreach($formatted_services[$serviceName]['data'] as $twains=>$param){
                  if(trim($param) != ''){
                      $output .= $param.'<br />';
                  }
              }*/
            $output .= '</pre>';
            $output .= '</div>';

            $output .= '<div class="description">';
            $output .= '<h3>Response errors</h3>';
            $output .= '<ul>';
              foreach($formatted_services[$serviceName]['errors'] as $twains=>$param){
                if(trim($param) != '' and trim($param) != '***/'){
                      $output .= '<li>'.$param.'</li>';
                  }
              }
            $output .= '</ul>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="clear"></div>';
            $output .= '<div class="separator"></div>';
          }
        $output .= "</div></body></html>";

        $result->setOutput($output);

        return $result;
    }
}

class ResolveExpression {
	const EXPRESSION = 'expression';
	public function init($request){
		$this->orgexpression = $request[ResolveExpression::EXPRESSION];
    }

	public function execute(){
		$result = new ComandoResult();
		$value = $this->doExecute($this->orgexpression);
		if ($value == true){
			$result->setStatus(1);
        } else {
			$result->setStatus(0);
        }
		return $result;
    }

	public function doExecute($expression){
		$endIndex=0;
		while ($endIndex > -1){
			$endIndex=strpos($expression,")");
			if ($endIndex > -1){
                $beginIndex = strrpos(substr($expression,0,$endIndex), '(');
				if ($beginIndex > -1){
                    $subexpression = substr($expression,$beginIndex+1,$endIndex-($beginIndex+1));
					$subresult = $this->doExecute($subexpression);
                    $newexpression = substr($expression, 0, $beginIndex) . $subresult . substr($expression, $endIndex+1);
					$expression = $newexpression;
                } else {
					return false;
                }
            }
        }
		$expression = $this->resolveVars($expression);

        $value = false;
		eval('$value = '.$expression.';');

		return $value;
    }

	public function resolveVars($expression){

		$comando = Comando::$instance;
		$varname = "";
		$index=-1;

        for($i = 0; $i < strlen($expression); $i++) {
            $c = $expression[$i];
			$resolveVar = false;
			$index+=1;
			if(is_numeric($c)){
				$resolveVar = true;
            } else {
				if ($c == " " || $c == "=" || $c == "*" || $c == "/" || $c == "+" || $c == "-" || $c == ">" || $c == "<" || ($varname == "" && $c == ".")){
					$resolveVar = true;
                } else {
					$varname .= $c;
                }
            }
			if ($resolveVar){
				if ($varname == "" || $varname == "true" || $varname == "false" || $varname == "||" || $varname == "&&"){
					$varname = "";
                } else {
					$parts = explode('.',$varname);
					$commandname = $parts[0];
					$commandparam = $parts[1];
					$varresult = $comando->execute($commandname,null);
					$varvalue = $varresult->data($commandparam);
                    $expression = substr($expression, 0, $index - strlen($parts[0]) - 1 - strlen($parts[1])) . $varvalue . substr($expression, $index);

                    return $this->resolveVars($expression);
                }
            }
        }

		return $expression;
    }
}


class ResponseType {
    const JSON = "json";
    const ACTO = "acto";
    const XML = "xml";
    const RAW = "raw";
}


class RequestType {
    const GET = "get";
    const POST = "post";
    const REQUEST = "request";
    const SCRIPT = "script";
}
?>