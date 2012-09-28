<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martindyrby
 * Date: 9/26/12
 * Time: 11:34 AM
 * To change this template use File | Settings | File Templates.
 */

class ServiceUtil {

    static public function toCamelCase($value) {
        $parts = explode("_", $value);
        $output = $parts[0];
        for($i = 1; $i < count($parts); $i++) {
            $output .= ucfirst($parts[$i]);
        }

        return $output;
    }
    static public function toHuman($value) {
        return ucfirst(str_replace("_", " ", $value));
    }
}

class AjaxTestService extends AbstractValidationCommand {

    const SERVICES = "services";
    const ZUMO = "zumo";
    const ZUMOCOMMAND = "zumocommand";

    protected function required() {
        return array(self::SERVICES);
    }

    protected function optional() {
        return array(self::ZUMO, self::ZUMOCOMMAND);
    }

    protected function doExecute() {
        $services = $this->getParam(self::SERVICES);
        $zumo = $this->getParam(self::ZUMO);
        $zumoCommand = $this->getParam(self::ZUMOCOMMAND);

        $result = new ComandoHtmlResult();

        if($zumo == 1) {
            header("Content-Type: application/xml");
            echo '<?xml version="1.0" encoding="UTF-8" ?>
';
            echo '<zumo>
';
            echo '    <views>
';

            for($i = 0; $i < count($services); $i++) {
                $service = $services[$i];

                $serviceName = $service['service'];
                $camelCase = ServiceUtil::toCamelCase($serviceName);
                $human = ServiceUtil::toHuman($serviceName);
                $addHandler = ($i == 0) ? '<handler type="startup" />' : '';
                $page = <<<EOT
        <page id="{$serviceName}" mediator="Comando.Views.AjaxMediator" title="{$human}" type="domclone" target="#pages #{$serviceName}" container="#page" manager="cascade">
            <handler type="{$camelCase}" />
            {$addHandler}
        </page>

EOT;
                echo $page;
            }

            echo '  </views>
';

            echo '  <commands>
';

            echo '      <command id="hideResult" type="function" target="Comando.Commands.hideResult">
';
            echo '          <handler type="formSubmitted" />
';
            echo '          <handler type="page_shift" />
';
            echo '      </command>
';
            echo '      <command id="showResult" type="function" target="Comando.Commands.showResult">
';
            echo '          <handler type="formResultReturned" />
';
            echo '      </command>
';

            for($i = 0; $i < count($services); $i++) {
                $service = $services[$i];

                $serviceName = $service['service'];
                $camelCase = ServiceUtil::toCamelCase($serviceName);
                $human = ServiceUtil::toHuman($serviceName);

            echo '      <command id="'.$camelCase.'" type="function" target="Comando.Commands.ajax">';
            echo '          <prop name="_args">';
            echo '              <item>';
            echo '                  <prop name="url" value="?service='.$serviceName.'" />';
            echo '                  <prop name="dataWrapper" value="result" />';
            echo '                  <prop name="successEvent" value="formResultReturned" />';
            echo '                  <prop name="errorEvent" value="formResultReturned" />';
            echo '              </item>';
            echo '          </prop>';
            echo '          <handler type="'.$camelCase.'" />';
            echo '      </command>';

            }

            echo '  </commands>';

            echo '</zumo>';
            exit;
        }

        $filepath = $this->resolveFilepath('/');


//        $result->addToHeader('<script type="text/javascript" src="http://libs.molamil.com/zumo-0.1.min.js"></script>');
        $result->addToHeader('<script type="text/javascript" src="'.$filepath.'zumo-0.1.js"></script>');
        $result->addToHeader('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>');
        $result->addToHeader('<script type="text/javascript" src="'.$filepath.'comando.js.php?service='.$_REQUEST['service'].'"></script>');
        $result->addToHeader('<link href="http://fonts.googleapis.com/css?family=Andada" rel="stylesheet" type="text/css" />');
        $result->addToHeader('<link href="'.$filepath.'style.css" rel="stylesheet" type="text/css" />');


        $site = <<<EOT
        <div id="site">

            <div id="header">
                <img id="logo" src="{$filepath}molalogo.png" alt="MOLAMIL" />
                <ul id="menu">
                </ul>
            </div>

            <div id="page">

            </div>

            <div id="block">
                <div class="resultblock">
                    <div>
                        <div><h1>Result</h1></div>
                        <div id="result"></div>
                    </div>
                </div>
            </div>

        </div>
EOT;

        $result->addToBody($site);

        $result->addToBody('<div id="pages">');

        for($i = 0; $i < count($services); $i++) {
            $service = $services[$i];

            $serviceName = $service['service'];
            $request = $service['request'];
            $fields = $service['fields'];
            $human = ServiceUtil::toHuman($serviceName);
            $page = '<div id="'.$serviceName.'">';
            $page .= '    <div><h1>'.$human.'</h1></div>';
            $page .= '    <div>';
            $page .= '          <form action="'.ServiceUtil::toCamelCase($serviceName).'" method="'.$request.'" id="'.$serviceName.'">';
            foreach($fields as $field) {
            $page .= '              <div>'.$field.'</div>';
            $page .= '              <input type="text" name="'.$field.'" />';
            }


            $page .= '              <br /><input type="submit" />';
            $page .= '          </form>';
            $page .= '      </div>';
            $page .= '</div>';

            $result->addToBody($page);
        }

        $result->addToBody('</div>');

        return $result;

    }


    private function resolveFilepath($separator) {
        $php_self = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')+1);
        $file = substr(__FILE__, strpos(__FILE__,$php_self)+strlen($php_self));
        $lastSlash = strrpos($file, '/');
        if($lastSlash !== false) {
            return substr($file, 0, $lastSlash).$separator;
        }

        return $file;
    }

}
?>