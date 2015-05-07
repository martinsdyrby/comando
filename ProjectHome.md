A lightweight php framework based on a front controller and a command pattern.

# Quick start #

## Installation ##
Download the latest release and unzip in your project folder. Rename unzipped directory to _comando_.

## Configuration ##
Open _comando/config.comando.php_.

### Configure commands ###
Commands are added to the **$config['commands']** array. The syntax is:
```
$config['commands']['COMMAND-ID'] = array(
   'type' => 'PATH-TO-CLASS.CLASS-NAME',
    'request' => 'REQUEST-TYPE',
   'response' => 'RESPONSE-TYPE',
   'params' => array(
      'key1' => 'value1',
      'key2' => 'value2'
   ),
   'restriction' => 'EXPRESSION-NAME',
   'restricted' => 'COMMAND-NAME'
);
```
  * COMMAND-ID will be the name used to execute the command. If you placed the comando directory in the root of your server the command will be available on the path **www.your-domain.com/comando/?service=COMMAND-ID**
  * _type_: PATH-TO-CLASS is the a dot separated path from the parent folder of comando to the file the holds the command class. CLASS\_NAME is the name of the class in the file.
  * _request_: REQUEST-TYPE determines how the command can be accessed. Valid values are GET, POST, REQUEST and SCRIPT. REQUEST allows access from both GET and POST. SCRIPT allows only execution from other commands.
  * _response_: RESPONSE-TYPE determines how the output should be formatted. At the moment only JSON is supported.
  * _params_ is a multidimensional array which will be proxied to the command.
  * _restriction_: EXPRESSION-NAME (optional) is the id of an expression defined in the configuration file the determines restrictions on access tothe command. For example an expression can require a valid login before the command is executed.
  * _restricted_: COMMAND-NAME is an option to specify a command to execute in case execution is restricted.

### Execute commands on init ###
If you want a command. for example a command that sets up the database connection, to run every time a command is requested. Add its COMMAND-ID to the **$config['init']** array.

### Configure expressions ###
Expressions are used to validate access to a command. Expressions are added to the **$config['expression']** array. The syntax is:
```
$config['expression']['EXPRESSION-NAME'] = 'EXPRESSION';
```
  * EXPRESSION-NAME is the unique id of the expression.
  * EXPRESSION is a statement that can be resolved to either boolean true or false. The expression can reference properties on other commands on the form COMMAND-ID.PROPERTY-NAME. The property must be returned in the commands data output. See **Writing commands**.

### Configure logging ###
To enable logging assign a COMMAND-ID to **$config['logging']**.
```
$config['logging'] = 'COMMAND-ID';
```
The command will be executed every time another commands is executed. it will receive a request array with two keys:
```
array(
   'command' => COMMAND-ID,
   'request' => REQUEST
);
```
  * COMMAND-ID is the command executed
  * REQUEST is the request array the command received

## Writing commands ##
A command is a class that implements atleast two specific methods
```
class MyCommand {
   public function init($request) {

   }

   public function execute() {
      return new ComandoResult();
   }
}
```
  * _init_ is called before _execute_. A request array is passed in. If the command is executed via a GET or POST request the value of the array will be the php `$_REQUEST` array. If the command is executed via a request from another command the value of the array will be whatever the calling command passes in the call. See **Executing commands from commands**.
  * _execute_ executes the command. The method must return an instance of _ComandoResult_.

### Command example ###
```
class MyCommand {
   protected $request;

   public function init($request) {
      $this->request = $request;
   }

   public function execute() {
      $result = new ComandoResult();

      if(isset($this->request['action'])) {
         $result->setStatus(1);
         $result->setData('action', $this->request['action']);
      } else {
         $result->setStatus(0);
         $result->setError("No action requested.");
      }

      return $result;
   }
}
```

## Relevant comando classes ##

### ComandoResult ###
**Method Summary**

_bool_ **status** ()
<pre>
Returns the boolean status of the result.<br>
</pre>
_void_ **setStatus** ( $status )
<pre>
Sets the boolean status of the result.<br>
</pre>
_mixed_ **data** ( $key )
<pre>
Returns the value stored for the given $key.<br>
Returns null if the $key is not found.<br>
</pre>
_void_ **setData** ( $key, $value )
<pre>
Stores $value for the given $key.<br>
The data will be outputted in the response in case of a succesful status.<br>
Automatically sets the status of the result to true.<br>
</pre>
_string_ **error** (  )
<pre>
Returns the error message of the result. The error will be outputted in the response in case of an error status.<br>
Returns null if no error has been set.<br>
</pre>
_void_ **setError** ( $error )
<pre>
Sets the error message of the result. Automatically set status to false.<br>
</pre>
_void_ **setOutput** ( $output )
<pre>
Sets the raw output response of the result. Useful if the output needed is different from json.<br>
</pre>
_void_ **setResponseType** ( $type )
<pre>
Sets what format to output the response as. Valid values are JSON.<br>
Defaults to the format specified in the configuration of the command.<br>
</pre>
_void_ **setLocation** ( $location )
<pre>
Sets a location to redirect to in case of a success status.<br>
</pre>
_bool_ **hasLocation** ()
<pre>
Returns wether a redirect location has been set.<br>
</pre>
_string_ **response** ()
<pre>
Outputs the response on the given format.<br>
Outputs the status and in case of status is true outputs the data. In case status is false outputs the error<br>
If a raw output has been specified using setOutput then only the specified will be outputted.<br>
</pre>
### AbstractValidationCommand ###

**Description**

Useful as superclass for commands.
Validates the existence of parameters in given request and returns a standard result error in case of missing parameters. To use override _required()_, _optional()_ and _doExecute()_.
In _doExecute()_ use _getParam()_ to retrieve values from the request array.

**Example**

Simple command example. The Login command requires _username_ and _password_ to be passed in the request otherwise the command will return a missing parameters error.
If _username_ and  _password_ are set and not null the command returns a succes response otherwise it returns an error response.
```
class Login extends AbstractValidationCommand {
    const USERNAME = "username";
    const PASSWORD = "password";

    public function required() {
        return array(self::USERNAME, self::PASSWORD);
    }
    
    public function optional() {
        return array();
    }
    
    public function doExecute() {
        $result = new ComandoResult();
        $username = $this->getParam(self::USERNAME);
        $password = $this->getParam(self::PASSWORD);

        if($username != null && $password != null) {
           $result->setStatus(1);
        } else {
            $result->setStatus(0);
        }
        return $result;
    }
}
```

**Method Summary**

_ComandoResult_ **doExecute** ()
<pre>
Must be overriden.<br>
Executes the command if all required paramters are available in the request array.<br>
</pre>

_array_ **required** ()
<pre>
Must be overriden.<br>
Returns what parameters the command should consider required.<br>
The command will exit with status false if one or more of the required parameters are<br>
not passed.<br>
</pre>

_array_ **optional** ()
<pre>
Must be overriden.<br>
Returns what parameters the command should consider optional.<br>
</pre>

_mixed_ **getParam** ( $paramName )
<pre>
Returns the formatted version of the parameter given by $paramName.<br>
The parameter name must be specified in either the required array or the optional array.<br>
The formatting is done with the htmlentities and strip_tags methods.<br>
</pre>

_mixed_ **getRawParam** ( $paramName )

<pre>
Returns the raw version of the parameter given by $paramName.<br>
The parameter name must be specified in either the required array or the optional array.<br>
</pre>