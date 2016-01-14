<?php

namespace Common\API;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use Common\Utilities\Constants;
use Zend\Http\ClientStatic;
class RESTJSONManager{
	


	
	function PlanJSONManager($action,$url,$requestjson,$uid){
	
	
			$request = new Request();
		$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/json; charset=UTF-8'
	));
		//$url="";
		
		
		try{
			$request->setUri($url);
			$request->setMethod($action);
			$client = new Client();
			
			if($action=='PUT' || $action=='POST'){
				$client->setUri($url);
				$client->setMethod($action);
				$client->setRawBody($requestjson);
				$client->setEncType('application/json');
				$response=$client->send();
				return $response;
			}else{
			
			$response = $client->dispatch($request);
	
			//var_dump(json_decode($response->getBody(),true));

			return $response;
			}
		}catch (\Exception $e){
			$e->getTrace();
		}
		
		return null;
	
	}
	
	
}