<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Common\CustomController\CustomController;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Common\CustomMailer;
use Common\CustomMailer\EmailManager;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use Common\ActionForms\ExplorerForm;
use Common\API\RESTManager;
use Zend\Db\Sql\Select;
use Common\Utilities\Constants;
use Zend\View\Model\JsonModel;
use Common\API\RESTJSONManager;
use Zend\Json\Json;
use Common\Utilities\Convertors;
use Zend\Db\ResultSet\ResultSet;

class PlanController extends AbstractActionController {
	protected $planTable;
	
	public function getPlanTable() {
		if (! $this->planTable) {
			$sm = $this->getServiceLocator ();
			$this->planTable = $sm->get ( 'PlanService' );
		}
		return $this->planTable;
	}
	
	
    
	function indexAction() {
	$this->layout ( "layout/user" );
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		
		
		$apiManager = new RESTJSONManager ();
		$url = Constants::PLAN_GET_STARS;
		$url = str_replace("<UID>", $uid, $url);
		$result = $apiManager->PlanJSONManager ( "GET", $url, null, $uid );
		
		$view = new ViewModel(array("result"=>json_decode($result->getBody(),true)));
		return $view;
		
	}
	function majorAction() {
		$this->layout ( "layout/user" );
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		
		$planRow = $this->getPlanTable ()->getPlanByUID ( $uid );
		$plangpaflag = "";
		foreach ( $planRow as $plan ) {
			
			$plangpaflag = $plan->major;
		}
		$result = array ();
		
		$r = new RESTJSONManager ();
		$url = Constants::PLAN_GET_ALL_MAJORS;
		// $url=str_replace("<UID>", $uid, $url);
		// echo $url;
		$result = $r->PlanJSONManager ( 'GET', $url, null, $uid );
		
		if ($plangpaflag == 1) {
			
			// Get Major by UID
			$majorurl = Constants::PLAN_GET_MAJOR;
			$majorurl = str_replace ( "<UID>", $uid, $majorurl );
			$response = $r->PlanJSONManager ( 'GET', $majorurl, null, $uid );
			$majorresult = json_decode ( $response->getBody (), true );
			$userMajor = $majorresult ['major'];
			
			$view = new ViewModel ( array (
					"result" => json_decode ( $result->getBody (), true ),
					"planflag" => $plangpaflag,
					'uid' => $uid,
					'major' => $userMajor,
					'note' => $majorresult['notes'][0]
			) );
		} else {
			
			$view = new ViewModel ( array (
					"result" => json_decode ( $result->getBody (), true ),
					"planflag" => $plangpaflag,
					'uid' => $uid,
					'major' => '' 
			) );
		}
		return $view;
	}
	function ucAction() {
		$this->layout ( "layout/user" );
		$jsonRequest = array ();
		$jsonRequest ['error'] = null;
		$jsonRequest ['errorCode'] = 0;
		$jsonRequest ['errorLevel'] = 0;
		
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		
		$mode = $this->params ()->fromPost ( 'mode' );
		$mode = "save";
		$uid = $this->params ()->fromPost ( 'uid' );
		$uid = 500;
		$jsonRequest ['notes'] = array (
				$this->params ()->fromPost ( 'note' ) 
		);
		$jsonRequest ['uid'] = $this->params ()->fromPost ( 'uid' );
		// echo "uid" . $this->params()->fromPost('uid');
		$jsonRequest ['dreamSchool'] = $this->params ()->fromPost ( 'dreamschool' );
		
		$planjson = new RESTJSONManager ();
		
		if ($mode == "update") {
			
			$url = Constants::PLAN_MYUCEAZY_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			
			$this->LogMessage ( json_encode ( $jsonRequest ) );
			$jsonResponse = $planjson->PlanJSONManager ( 'PUT', $url, json_encode ( $jsonRequest ), $uid );
			$this->LogMessage ( $jsonResponse );
		} elseif ($mode == "save") {
			
			$this->getPlanTable ()->updateMyUCEAZY ( $uid );
			
			$url = Constants::PLAN_MYUCEAZY_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			$jsonResponse = $planjson->PlanJSONManager ( 'POST', $url, json_encode ( $jsonRequest ), $uid );
		}
		
		$error = "";
		$ecode = "";
		$response = "";
		if ($jsonResponse->getStatusCode () == '200' || $jsonResponse->getStatusCode () == '201') {
			
			$response = json_decode ( $jsonResponse->getBody (), true );
			$ecode = $response ['errorCode'];
			$error = $response ['error'];
		} else {
			$ecode = "999";
			$error = "Backend API Error";
		}
		
		/*
		 * $resultret = new JsonModel(array(
		 *
		 * 'response' =>json_decode($jsonResponse->getBody(),true) ,'error'=>$error,'ecode'=>$ecode,'request'=>json_encode($jsonRequest)
		 * ));
		 */
		return new JsonModel ( array () );
		
	}
	
		function majorsaveupdateJSONAction() {
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		$this->layout ( "layout/user" );
		$jsonRequest = array ();
		$jsonRequest ['error'] = null;
		$jsonRequest ['errorCode'] = 0;
		$jsonRequest ['errorLevel'] = 0;
		
		$mode = $this->params ()->fromPost ( 'mode' );
		$uid = $this->params ()->fromPost ( 'uid' );
		$jsonRequest ['notes'] = array (
				$this->params ()->fromPost ( 'note' ) 
		);
		
		$starcount = 0;
		
	$note = $this->params ()->fromPost ( 'note' ) ;
		
		$jsonRequest ['uid'] = $this->params ()->fromPost ( 'uid' );
		// echo "uid" . $this->params()->fromPost('uid');
		$tmpMajor =  $this->params ()->fromPost ( 'majorselect' );
		$finalMajor = $tmpMajor;
		$finalMajor = $tmpMajor;
		
		$jsonRequest ['major'] = $finalMajor;
		
		if($this->params ()->fromPost ( 'majorselect' )!=''){
			$starcount++;
		}
		$jsonRequest ['starCount'] =$starcount;
		
		
		$planjson = new RESTJSONManager ();
		
		if ($mode == "update") {
			
			$url = Constants::PLAN_GET_MAJOR;
			$url = str_replace ( "<UID>", $uid, $url );
			$this->LogMessage ( json_encode ( $jsonRequest ) );
			$jsonResponse = $planjson->PlanJSONManager ( 'PUT', $url, json_encode ( $jsonRequest ), $uid );
			$this->LogMessage ( $jsonResponse );
		} elseif ($mode == "save") {
			
			$this->getPlanTable ()->updateMajor ( $uid );
			$url = Constants::PLAN_GET_MAJOR;
			$url = str_replace ( "<UID>", $uid, $url );
			$jsonResponse = $planjson->PlanJSONManager ( 'POST', $url, json_encode ( $jsonRequest ), $uid );
		}
		
		$error = "";
		$ecode = "";
		$response = "";
		if ($jsonResponse->getStatusCode () == '200' || $jsonResponse->getStatusCode () == '201') {
			
			$response = json_decode ( $jsonResponse->getBody (), true );
			$ecode = $response ['errorCode'];
			$error = $response ['error'];
		} else {
			$ecode = "999";
			$error = "Backend API Error";
		}
		
		$resultret = new JsonModel ( array (
				
				'response' => json_decode ( $jsonResponse->getBody (), true ),
				'error' => $error,
				'ecode' => $ecode,
				'request' => json_encode ( $jsonRequest ) ,
				'note'=>$note
		) );
		return $resultret;
	}
	function majorJSONAction() {
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		$this->layout ( "layout/user" );
		
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		$major = $this->params ()->fromPost ( 'major' );
		
		
		$finalMajor = $major;
		$finalMajor = $major;
		
		
		
		$starcount = 0;
		
		if($major!=''){
			$starcount++;
		}
		
		$mode = $this->params ()->fromPost ( 'mode' );
		$r = new RESTJSONManager ();
		if ($mode == 'save') {
			
			$this->getPlanTable ()->updateMajor ( $uid );
			
			$jsonRequest = array ();
			$jsonRequest ['error'] = null;
			$jsonRequest ['errorCode'] = 0;
			$jsonRequest ['errorLevel'] = 0;
			$jsonRequest ['major'] = $finalMajor;
			$jsonRequest ['starCount'] =$starcount;
			$jsonRequest ['notes'] = array (
					$this->params ()->fromPost ( 'note' ) 
			);
			$jsonRequest ['uid'] = $this->params ()->fromPost ( 'uid' );
			
			$majorurl = Constants::PLAN_GET_MAJOR;
			$majorurl = str_replace ( "<UID>", $uid, $majorurl );
			$response = $r->PlanJSONManager ( 'POST', $majorurl, $jsonRequest, $uid );
			$error = "";
			$ecode = "";
			$response = "";
			if ($jsonResponse->getStatusCode () == '200' || $jsonResponse->getStatusCode () == '201') {
				
				$response = json_decode ( $jsonResponse->getBody (), true );
				$ecode = isset ( $response ['errorCode'] ) ? $response ['errorCode'] : 0;
				$error = isset ( $response ['error'] ) ? $response ['error'] : "";
			} else {
				$ecode = "999";
				$error = "Backend API Error";
			}
		} elseif ($mode == 'update') {
			
			$jsonRequest = array ();
			$jsonRequest ['error'] = null;
			$jsonRequest ['errorCode'] = 0;
			$jsonRequest ['errorLevel'] = 0;
			$jsonRequest ['major'] = $finalMajor;
			$jsonRequest ['starCount'] =$starcount;
			$jsonRequest ['notes'] = array (
					$this->params ()->fromPost ( 'note' ) 
			);
			$jsonRequest ['uid'] = $this->params ()->fromPost ( 'uid' );
			$majorurl = Constants::PLAN_GET_MAJOR;
			$majorurl = str_replace ( "<UID>", $uid, $majorurl );
			$response = $r->PlanJSONManager ( 'PUT', $majorurl, $jsonRequest, $uid );
			$error = "";
			$ecode = "";
			$response = "";
			if ($jsonResponse->getStatusCode () == '200' || $jsonResponse->getStatusCode () == '201') {
				
				$response = json_decode ( $jsonResponse->getBody (), true );
				$ecode = isset ( $response ['errorCode'] ) ? $response ['errorCode'] : 0;
				$error = isset ( $response ['error'] ) ? $response ['error'] : "";
			} else {
				$ecode = "999";
				$error = "Backend API Error";
			}
		} else {
			$major = str_replace ( " ", "%20", $major );
			$major = str_replace ( "/", '%252F', $major );
			$url = Constants::PLAN_GET_COLLEGE_BY_MAJOR;
			$url = str_replace ( "<MAJOR>", $major, $url );
			$jsonResponse = $r->PlanJSONManager ( 'GET', $url, null, '' );
			
			$error = "";
			$ecode = "";
			$response = "";
			if ($jsonResponse->getStatusCode () == '200' || $jsonResponse->getStatusCode () == '201') {
				
				$response = json_decode ( $jsonResponse->getBody (), true );
				$ecode = isset ( $response ['errorCode'] ) ? $response ['errorCode'] : 0;
				$error = isset ( $response ['error'] ) ? $response ['error'] : "";
			} else {
				$ecode = "999";
				$error = "Backend API Error";
			}
		}
		
		$resultret = new JsonModel ( array (
				
				'response' => json_decode ( $jsonResponse->getBody (), true ),
				'error' => $error,
				'ecode' => $ecode,
				'request' => $url 
		) );
		/*
		 * $resultret = new JsonModel(array(
		 *
		 * 'response' =>$jsonResponse->__toString() ,'request'=>$url));
		 */
		return $resultret;
	}
	function LogMessage($message) {
		if (Constants::IS_LOG) {
			$logger = new \Zend\Log\Logger ();
			$writer = new \Zend\Log\Writer\Stream ( Constants::LOG_FILE );
			$logger->addWriter ( $writer );
			$logger->info ( $message );
		}
	}
	
	
}