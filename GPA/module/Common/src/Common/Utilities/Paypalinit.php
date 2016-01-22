<?php

namespace Common\Utilities;
require_once  getcwd().'/vendor/autoload.php';

require_once __DIR__ . '/paypal.php';


use PayPal\Rest;
use PayPal\Auth\OAuthTokenCredential;


class Paypalinit{

	function __construct(){
		return $this->getApiContext();
	}
 
// SDK Configuration
function getApiContext() {


    // Define the location of the sdk_config.ini file
    if (!defined("PP_CONFIG_PATH")) {
        define("PP_CONFIG_PATH", dirname(__DIR__));
    }

	$apiContext = new \PayPal\Rest\ApiContext( new \PayPal\Auth\OAuthTokenCredential(
		//'EBWKjlELKMYqRNQ6sYvFo64FtaRLRR5BdHEESmha49TM',
		//'EO422dn3gQLgDbuwqTjzrFgFtaRLRR5BdHEESmha49TM'
		'AXC-ruytoWCZuQkziPi--GNQyPRDWnmpfreOdG03cDdLv_PtefyX8Le3VGAVnkV87dJZ0eCvpjfBzZ3I',
		'EA554cq5zJhgfbraN3X7jnaa_3j7bwO2IL9kRQ9yw2_1Smf62BpFh3mBgZ4JioL0iUGoCJia2_lGYStz'
		
	));

	
	// Alternatively pass in the configuration via a hashmap.
	// The hashmap can contain any key that is allowed in
	// sdk_config.ini	
	
	$apiContext->setConfig(array(
		'http.ConnectionTimeOut' => 30,
		'http.Retry' => 1,
		'mode' => 'sandbox',
		'log.LogEnabled' => FALSE,
		'log.FileName' => '../PayPal.log',
		'log.LogLevel' => 'INFO'		
	));
	
	return $apiContext;
}
}
