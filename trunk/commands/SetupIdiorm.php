<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 14, 2012
 * Time: 3:54:28 PM
 * To change this template use File | Settings | File Templates.
 */
 
class SetupIdiorm {

    private $host;
    private $username;
    private $password;

    public $no_log=true;

    public function init($request) {
        require_once('idiorm.php');

        $this->host = $request['host'];
        $this->username = $request['username'];
        $this->password = $request['password'];

    }

    public function execute() {

        ORM::configure($this->host);
        ORM::configure('username', $this->username);
        ORM::configure('password', $this->password);

    }

}
