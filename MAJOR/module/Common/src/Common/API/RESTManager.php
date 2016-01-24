<?php

namespace Common\API;
use Zend\Http\Request;

use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use Common\Utilities\Constants;
class RESTManager{




function getACTResult($gpa,$act,$actew,$county,$state){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	$url="";
	try{
		$request->setUri(Constants::URI_ACT);
		$request->setMethod('GET');
		
		if($county==null){
			$county="";
		}
		$request->setQuery(new Parameters(array('gpa' => $gpa,'actcomp'=>$act,'actrdwr'=>$actew,'county'=>$county,'state'=>$state)));
		$url= $request->getQuery()->toString();
		$client = new Client();
		$response = $client->dispatch($request);
		$data = json_decode($response->getBody(), true);
	}catch (\Exception $e){
		$this->LogMessage($url);
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}

function getSATResult($gpa,$satcrm,$satwrt,$county,$state){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	$request->setUri(Constants::URI_SAT);
	$request->setMethod('GET');
	$url="";
	if($county==null){
		$county="";
	}
	$request->setQuery(new Parameters(array('gpa' => $gpa,'satcrm'=>$satcrm,'satwrt'=>$satwrt,'county'=>$county,'state'=>$state)));
	$url=$request->getQuery()->toString();
	try{
	$client = new Client();
	$response = $client->dispatch($request);
	$data = json_decode($response->getBody(), true);
	}catch (\Exception $e){
		$this->LogMessage($url);
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}

function getPSATResult($gpa,$psatcrm,$psatwrt,$county,$state){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	$request->setUri(Constants::URI_PSAT);
	$request->setMethod('GET');
	if($county==null){
		$county="";
	}
	$url="";
	$request->setQuery(new Parameters(array('gpa' => $gpa,'psatcrm'=>$psatcrm,'psatwrt'=>$psatwrt,'county'=>$county,'state'=>$state)));
	$url= $request->getQuery()->toString();
	try{
	$client = new Client();
	$response = $client->dispatch($request);
	$data = json_decode($response->getBody(), true);
	}catch (\Exception $e){
		$this->LogMessage($url);
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}


function getCSUACTResult($gpa,$act,$actew,$county,$state,$freelunch,$us,$major,$csize,$cpref,$allflag,$schoolname){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	if($county==null){
		$county="";
	}
	if($major=="-1"){
		$major=null;
	}
	$url="";
	try{
		$request->setUri(Constants::CSU_URI_ACT);
		$request->setMethod('GET');
		if($allflag==1){
		$request->setQuery(new Parameters(array('gpa' => $gpa,'actcomp'=>$act,'actrdwr'=>$actew,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'allcolleges'=>'true','school'=>$schoolname)));
		}else{
			$request->setQuery(new Parameters(array('gpa' => $gpa,'actcomp'=>$act,'actrdwr'=>$actew,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'school'=>$schoolname)));
			
		}
		$url=Constants::CSU_URI_ACT . $request->getQuery()->toString();
		$client = new Client();
		$response = $client->dispatch($request);
		$data = json_decode($response->getBody(), true);
	}catch (\Exception $e){
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}

function getCSUSATResult($gpa,$satcrm,$satwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$allflag,$schoolname){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	if($county==null){
		$county="";
	}
	if($major=="-1"){
		$major=null;
	}
	$url="";
	try{
	$request->setUri(Constants::CSU_URI_SAT);
	$request->setMethod('GET');
	
	if($allflag==1){
	$request->setQuery(new Parameters(array('gpa' => $gpa,'satcrm'=>$satcrm,'satwrt'=>$satwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,"allcolleges"=>'true','school'=>$schoolname)));
	}else{
	
$request->setQuery(new Parameters(array('gpa' => $gpa,'satcrm'=>$satcrm,'satwrt'=>$satwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'school'=>$schoolname)));
	}

$url=Constants::CSU_URI_SAT.'?'.$request->getQuery()->toString();
	$client = new Client();
	$response = $client->dispatch($request);
	$data = json_decode($response->getBody(), true);
		$data = json_decode($response->getBody(), true);
		}catch (\Exception $e){
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;

}
function getCSUPSATResult($gpa,$psatcrm,$psatwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$allflag,$schoolname){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	if($county==null){
		$county="";
	}
	if($major=="-1"){
		$major=null;
	}
	$url="";
	try{
	$request->setUri(Constants::CSU_URI_PSAT);
	$request->setMethod('GET');
	
	if($allflag==1){
	$request->setQuery(new Parameters(array('gpa' => $gpa,'psatcrm'=>$psatcrm,'psatwrt'=>$psatwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'allcolleges'=>'true','school'=>$schoolname)));
	}else{
		$request->setQuery(new Parameters(array('gpa' => $gpa,'psatcrm'=>$psatcrm,'psatwrt'=>$psatwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'school'=>$schoolname)));
	}
	$url=Constants::CSU_URI_PSAT.'?'.$request->getQuery()->toString();
	$client = new Client();
	$response = $client->dispatch($request);
	$data = json_decode($response->getBody(), true);
	$data = json_decode($response->getBody(), true);
		}catch (\Exception $e){
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}

function getUCACTResult($gpa,$act,$actew,$county,$state,$freelunch,$us,$major,$csize,$cpref,$allflag,$schoolname){
	
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	
	if($county==null){
		$county="";
	}
	if($major=="-1"){
		$major=null;
	}
	$url="";
	try{
	$request->setUri(Constants::UC_URI_ACT);
	$request->setMethod('GET');
	
	if($allflag==1){
	$request->setQuery(new Parameters(array('gpa' => $gpa,'actcomp'=>$act,'actrdwr'=>$actew,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'allcolleges'=>'true','school'=>$schoolname)));
	}else{
		$request->setQuery(new Parameters(array('gpa' => $gpa,'actcomp'=>$act,'actrdwr'=>$actew,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'school'=>$schoolname)));
	}
	$url=Constants::UC_URI_ACT.'?'.$request->getQuery()->toString();
	$client = new Client();
	$response = $client->dispatch($request);
	$data = json_decode($response->getBody(), true);
	}catch (\Exception $e){
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}	
	

function getUCPSATResult($gpa,$psatcrm,$psatwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$allflag,$schoolname){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	if($county==null){
		$county="";
	}
	if($major=="-1"){
		$major=null;
	}
	$url="";
	try{
	$request->setUri(Constants::UC_URI_PSAT);
	$request->setMethod('GET');
	if($allflag==1){
	$request->setQuery(new Parameters(array('gpa' => $gpa,'psatcrm'=>$psatcrm,'psatwrt'=>$psatwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'allcolleges'=>'true','school'=>$schoolname)));
	}else{
		$request->setQuery(new Parameters(array('gpa' => $gpa,'psatcrm'=>$psatcrm,'psatwrt'=>$psatwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'school'=>$schoolname)));
		
	}
	
	$url=$request->getQuery()->toString();
	$client = new Client();
	$response = $client->dispatch($request);
	$data = json_decode($response->getBody(), true);
		}catch (\Exception $e){
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}


function getUCSATResult($gpa,$satcrm,$satwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$allflag,$schoolname){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	
	if($county==null){
		$county="";
	}
	if($major=="-1"){
		$major=null;
	}
	$url="";
	try{
	$request->setUri(Constants::UC_URI_SAT);
	$request->setMethod('GET');
	
	if($allflag=="1"){
	$request->setQuery(new Parameters(array('gpa' => $gpa,'satcrm'=>$satcrm,'satwrt'=>$satwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'allcolleges'=>'true','school'=>$schoolname)));
	}else{
		$request->setQuery(new Parameters(array('gpa' => $gpa,'satcrm'=>$satcrm,'satwrt'=>$satwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'school'=>$schoolname)));
	}
	$url=$request->getQuery()->toString();
	$client = new Client();
	$response = $client->dispatch($request);
	$data = json_decode($response->getBody(), true);
	}catch (\Exception $e){
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}
	
	
function getBOTHACTResult($gpa,$act,$actew,$county,$state,$freelunch,$us,$major,$csize,$cpref,$allflag,$schoolname){

	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));

	if($county==null){
		$county="";
	}
	if($major=="-1"){
		$major=null;
	}
$url="";
	try{
		$request->setUri(Constants::BOTH_URI_ACT);
		$request->setMethod('GET');
		if($allflag==1){
		$request->setQuery(new Parameters(array('gpa' => $gpa,'actcomp'=>$act,'actrdwr'=>$actew,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'allcolleges'=>'true','school'=>$schoolname)));
		}else{
		$request->setQuery(new Parameters(array('gpa' => $gpa,'actcomp'=>$act,'actrdwr'=>$actew,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'school'=>$schoolname)));
		}
		$url=Constants::BOTH_URI_ACT."/".$request->getQuery()->toString();
		$client = new Client();
		$response = $client->dispatch($request);
		$data = json_decode($response->getBody(), true);
	}catch (\Exception $e){
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}


function getBOTHPSATResult($gpa,$psatcrm,$psatwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$allflag,$schoolname){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));
	
	if($county==null){
		$county="";
	}
	if($major=="-1"){
		$major=null;
	}
	$url="";
	try{
	$request->setUri(Constants::BOTH_URI_PSAT);
	$request->setMethod('GET');
	if($allflag=="1"){
	$request->setQuery(new Parameters(array('gpa' => $gpa,'psatcrm'=>$psatcrm,'psatwrt'=>$psatwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'allcolleges'=>'true','school'=>$schoolname)));
	}else{
		$request->setQuery(new Parameters(array('gpa' => $gpa,'psatcrm'=>$psatcrm,'psatwrt'=>$psatwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'school'=>$schoolname)));
	}

	$url=Constants::BOTH_URI_PSAT.'?'.$request->getQuery()->toString();
	$client = new Client();
	$response = $client->dispatch($request);
	$data = json_decode($response->getBody(), true);
	
	}catch (\Exception $e){
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
	

}


function getBOTHSATResult($gpa,$satcrm,$satwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$allflag,$schoolname){
	$request = new Request();
	$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	));

	if($county==null){
		$county="";
	}
	if($major=="-1"){
		$major=null;
	}
	$url="";
	try{
	$request->setUri(Constants::BOTH_URI_SAT);
	$request->setMethod('GET');
	
	if($allflag=="1"){
		$request->setQuery(new Parameters(array('gpa' => $gpa,'satcrm'=>$satcrm,'satwrt'=>$satwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,"allcolleges"=>'true','school'=>$schoolname)));
		}else{
	$request->setQuery(new Parameters(array('gpa' => $gpa,'satcrm'=>$satcrm,'satwrt'=>$satwrt,'county'=>$county,'state'=>$state,'size'=>$csize,'style'=>$cpref,'lunch'=>$freelunch,'majors'=>$major,'school'=>$schoolname)));
	
	}
	$url=Constants::BOTH_URI_SAT.'?'.$request->getQuery()->toString();
	$client = new Client();
	$response = $client->dispatch($request);
	$data = json_decode($response->getBody(), true);
	//$data['url']=$url;
	}catch (\Exception $e){
		
		$data['exception']="URL not found";
		$data['url']=$url;
	}
	return $data;
}
}