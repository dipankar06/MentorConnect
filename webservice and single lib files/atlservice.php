<?php
/*
Copyright (C) 2019  IBM Corporation 
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details at 
http://www.gnu.org/licenses/gpl-3.0.html
*/

/* @package: core_webservice
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 29-Aug-2018
 * @Description: Web services utility functions and classes for atlinnonet
*/


define('NO_DEBUG_DISPLAY', true);
define('WS_SERVER', true);

require_once('../config.php');

require_once("$CFG->dirroot/webservice/atlservicelib.php");


class webservice_rest_newserver extends webservice_base_server {

    /** @var string return method ('xml' or 'json') */
    protected $restformat;
	protected $returnresponse; //added by ATL Dev

    /**
     * Contructor
     *
     * @param string $authmethod authentication method of the web service (WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN, ...)
     * @param string $restformat Format of the return values: 'xml' or 'json'
     */
    public function __construct($authmethod) {
        parent::__construct($authmethod);
        $this->wsname = 'rest';
		$this->restformat = 'json';
    }

    /**
     * This method parses the $_POST and $_GET superglobals and looks for
     * the following information:
     *  1/ user authentication - username+password or token (wsusername, wspassword and wstoken parameters)
     *  2/ function name (wsfunction parameter)
     *  3/ function parameters (all other parameters except those above)
     *  4/ text format parameters
     *  5/ return rest format xml/json
     */
    protected function parse_request() {
        // Retrieve and clean the POST/GET parameters from the parameters specific to the server.
        parent::set_web_service_call_settings();

        // Get GET and POST parameters.
        $methodvariables = array_merge($_GET, $_POST);
	        
        unset($methodvariables['moodlewsrestformat']);

        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {
            $this->username = isset($methodvariables['wsusername']) ? $methodvariables['wsusername'] : null;
            unset($methodvariables['wsusername']);
            $this->password = isset($methodvariables['wspassword']) ? $methodvariables['wspassword'] : null;
            unset($methodvariables['wspassword']);
            $this->functionname = isset($methodvariables['wsfunction']) ? $methodvariables['wsfunction'] : null;
            unset($methodvariables['wsfunction']);
            $this->parameters = $methodvariables;
        } else {
			//By ATL Dev, Request recieve from client as Json ...ChangesOn: 30-Nov-2018
			$methodvariables = (array) json_decode($methodvariables['wsdata']);
			$this->token = isset($methodvariables['wstoken']) ? $methodvariables['wstoken'] : null;
			unset($methodvariables['wstoken']);
			$this->functionname = isset($methodvariables['wsfunction']) ? $methodvariables['wsfunction'] : null;
			unset($methodvariables['wsfunction']);
			$parameters_array = isset($methodvariables['wsparams']) ? (array) $methodvariables['wsparams'] : null;
			$parameters_array['usertoken'] = trim($this->token);
			$this->parameters = $parameters_array;
        }
    }

    /**
     * Send the result of function call to the WS client
     * formatted as XML document.
     */
    protected function send_response() {
        //Check that the returned values are valid
		$validatedvalues = $this->returnresponse;
        if ($this->restformat == 'json') {
            $response = json_encode($validatedvalues);
        }
        $this->send_headers();
        echo $response;
    }

	/**
	* Send the error information to the WS client
	* formatted as XML document.
	* Note: the exception is never passed as null,
	*       it only matches the abstract function declaration.
	* @param exception $ex the exception that we are sending
	*/
    protected function send_error($ex=null) {
        $this->send_headers();
        echo $this->generate_error($ex);
    }

	/**
	* Build the error information matching the REST returned value format (JSON or XML)
	* @param exception $ex the exception we are converting in the server rest format
	* @return string the error in the requested REST format
	*/
    protected function generate_error($ex) {
        if ($this->restformat == 'json') {
            $errorobject = new stdClass;
            $errorobject->exception = get_class($ex);
            $errorobject->errorcode = $ex->errorcode;
            $errorobject->message = $ex->getMessage();
            if (debugging() and isset($ex->debuginfo)) {
                $errorobject->debuginfo = $ex->debuginfo;
            }
            $error = json_encode($errorobject);
        } else {
            $error = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
            $error .= '<EXCEPTION class="'.get_class($ex).'">'."\n";
            $error .= '<ERRORCODE>' . htmlspecialchars($ex->errorcode, ENT_COMPAT, 'UTF-8')
                    . '</ERRORCODE>' . "\n";
            $error .= '<MESSAGE>'.htmlspecialchars($ex->getMessage(), ENT_COMPAT, 'UTF-8').'</MESSAGE>'."\n";
            if (debugging() and isset($ex->debuginfo)) {
                $error .= '<DEBUGINFO>'.htmlspecialchars($ex->debuginfo, ENT_COMPAT, 'UTF-8').'</DEBUGINFO>'."\n";
            }
            $error .= '</EXCEPTION>'."\n";
        }
        return $error;
    }

    /**
     * Internal implementation - sending of page headers.
     */
    protected function send_headers() {
        if ($this->restformat == 'json') {
            header('Content-type: application/json');
        } else {
            header('Content-Type: application/xml; charset=utf-8');
            header('Content-Disposition: inline; filename="response.xml"');
        }
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
        // Allow cross-origin requests only for Web Services.
        // This allow to receive requests done by Web Workers or webapps in different domains.
        header('Access-Control-Allow-Origin: *');
    }

	/**
	* Internal implementation - recursive function producing XML markup.
	*
	* @param mixed $returns the returned values
	* @param external_description $desc
	* @return string
	*/
    protected static function xmlize_result($returns, $desc) {
        return null;
    }
	
	//Child Class function
    protected function execute() {
		if (function_exists($this->functionname)) {
			$this->returnresponse = call_user_func($this->functionname,$this->parameters);
		} else{
			$this->returnresponse = "Error ! function not exists, Please check wsfunction name";
		}
		//$this->$returns = $this->returnresponse;     	 // throwing error in local
    }
	
	//Copy of parent function run()
	public function runserver() {
        // we will probably need a lot of memory in some functions
        raise_memory_limit(MEMORY_EXTRA);
        // set some longer timeout, this script is not sending any output,
        // this means we need to manually extend the timeout operations
        // that need longer time to finish
        external_api::set_timeout();
        // set up exception handler first, we want to sent them back in correct format that
        // the other system understands
        // we do not need to call the original default handler because this ws handler does everything
        set_exception_handler(array($this, 'exception_handler'));
        
		// init all properties from the request data
        $this->parse_request();
        // authenticate user, this has to be done after the request parsing
        // this also sets up $USER and $SESSION
        
		// finally, execute the function - any errors are catched by the default exception handler
        $this->execute();

        // send the results back in correct format
        $this->send_response();

        // session cleanup
        $this->session_cleanup();        
    }
}


//** REST web service entry point. The authentication is done via tokens.

/*if (!webservice_protocol_is_enabled('rest')) {
    header("HTTP/1.0 403 Forbidden");
    debugging('The server died because the web services or the REST protocol are not enable',
        DEBUG_DEVELOPER);
    die;
}
*/

$server = new webservice_rest_newserver(WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN);
//$server->run(); //Parent Function
$server->runserver(); //Child Function
die;

?>

<?php
// Public URL To Request data "http://your.site.com/webservice/atlservice.php?wstoken=<optional-userlogin token>&wsfunction=<mandatory>&wsparams=<json Data>"
// Method POST

// Public URL To Submit data "http://your.site.com/webservice/atlservice.php?wstoken=<optional-userlogin token>&wsfunction=<mandatory>&wsparams=<json Data-Mandatory>" 
// Method POST
?>