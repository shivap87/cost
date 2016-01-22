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
	
	function GPAReviewJSONAction(){
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		$uid = $this->params ()->fromPost ( 'uid' );
		
		
		$jsonRequest ['error'] = null;
		$jsonRequest ['errorCode'] = 0;
		$jsonRequest ['errorLevel'] = 0;
		$uid = $this->params ()->fromPost ( 'uid' );
		//$uid=1013;
		$url = Constants::PLAN_UCEAZY_ELIGIBLE_COURSE;
		$url = str_replace ( "<UID>", $uid, $url );
		//$this->LogMessage ( json_encode ( $jsonRequest ) );
		
		$planjson = new RESTJSONManager ();
		$jsona2g = "";
		$jsona2g = $planjson->PlanJSONManager ( 'GET', $url, json_encode ( $jsonRequest ), $uid );
		
		$error = "";
		$ecode = "";
		$responsea2g = "";
		if ($jsona2g->getStatusCode () == '200' || $jsona2g->getStatusCode () == '201') {
				
			$responsea2g = json_decode ( $jsona2g->getBody (), true );
			$ecode = 0;
			$error = "";
		} else {
			$ecode = "999";
			$error = "Backend API Error";
		}
		
		
		$url = Constants::PLAN_UCEAZY_COMPETITIVE_COURSES;
		$url = str_replace ( "<UID>", $uid, $url );
		//$this->LogMessage ( json_encode ( $jsonRequest ) );
		
		$planjson = new RESTJSONManager ();
		$jsonnona2g = "";
		$jsonnona2g = $planjson->PlanJSONManager ( 'GET', $url, json_encode ( $jsonRequest ), $uid );
		
		$error = "";
		$ecode = "";
		$responsenona2g = "";
		if ($jsonnona2g->getStatusCode () == '200' || $jsonnona2g->getStatusCode () == '201') {
		
			$responsenona2g = json_decode ( $jsonnona2g->getBody (), true );
			$ecode = 0;
			$error = "";
		} else {
			$ecode = "999";
			$error = "Backend API Error";
		}
		
		
		
		$resultret = new JsonModel ( array (
		
				'responsea2g' => json_decode ( $jsona2g->getBody (), true ),
				'responsenona2g'=>json_decode ( $jsonnona2g->getBody (), true ),
				'error' => 0,
				'ecode' => 0,
				//'request' => json_encode ( $jsonRequest )
		) );
		return $resultret;
	}
	function SATtoACTAction() {
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		$sattoactyear = $this->params ()->fromPost ( 'sattoactyear' );
		$uid = $this->params ()->fromPost ( 'uid' );
		$satdatetakenArray = $this->params ()->fromPost ( 'satdatetaken' );
		$satcrArray = $this->params ()->fromPost ( 'satcr' );
		$satmathArray = $this->params ()->fromPost ( 'satmath' );
		$satwritingArray = $this->params ()->fromPost ( 'satwriting' );
		$i = 0;
		foreach ( $satdatetakenArray as $date ) {
			if ($sattoactyear == $date) {
				break;
			}
			$i ++;
		}
		
		$satdatetaken = $satdatetakenArray [$i];
		$satcr = $satcrArray [$i];
		$satmath = $satmathArray [$i];
		$satwriting = $satwritingArray [$i];
		
		$requestJSON ['year'] = $satdatetaken;
		$requestJSON ['criticalReadingAndMath'] = ($satcr + $satmath);
		$requestJSON ['writing'] = (0 + $satwriting);
		$r = new RESTJSONManager ();
		$url = Constants::PLAN_TESTING_SATTOACT;
		$url = str_replace ( "<UID>", $uid, $url );
		$jsonResponse = $r->PlanJSONManager ( 'POST', $url, json_encode ( $requestJSON ), $uid );
		
		$error = "";
		$ecode = "";
		$response = "";
		if ($jsonResponse->getStatusCode () == '200' || $jsonResponse->getStatusCode () == '201') {
			$response = json_decode ( $jsonResponse->getBody (), true );
			$ecode = '0';
			$error = '';
		} else {
			$ecode = "999";
			$error = "Backend API Error";
		}
		
		$resultret = new JsonModel ( array (
				
				'response' => json_decode ( $jsonResponse->getBody (), true ),
				'error' => $error,
				'ecode' => $ecode,
				'request' => json_encode ( $requestJSON ),
				'date' => $sattoactyear 
		) );
		return $resultret;
	}
	function ACTtoSATAction() {
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		$requestJSON = array ();
		$acttosatyear = $this->params ()->fromPost ( 'acttosatyear' );
		$uid = $this->params ()->fromPost ( 'uid' );
		$actdatetakenArray = $this->params ()->fromPost ( 'actdatetaken' );
		$actcompositeArray = $this->params ()->fromPost ( 'actcomposite' );
		$actenglishArray = $this->params ()->fromPost ( 'actenglish' );
		$actmathArray = $this->params ()->fromPost ( 'actmath' );
		$actreadingArray = $this->params ()->fromPost ( 'actreading' );
		$actscienceArray = $this->params ()->fromPost ( 'actscience' );
		$actwritingArray = $this->params ()->fromPost ( 'actwriting' );
		
		$i = 0;
		foreach ( $actdatetakenArray as $date ) {
			if ($date == $acttosatyear) {
				break;
			}
			$i ++;
		}
		
		$actdatetaken = $actdatetakenArray [$i];
		$actcomposite = $actcompositeArray [$i];
		$actenglish = $actenglishArray [$i];
		$actmath = $actmathArray [$i];
		$actreading = $actreadingArray [$i];
		$actscience = $actscienceArray [$i];
		$actwriting = $actwritingArray [$i];
		
		$tmp =  $actenglish . ":" . $actmath . ":" .$actscience.":" . $actreading  . ":" . $actwriting;
		
		
		if(( $actenglish <11||$actmath <11||$actscience <11||$actreading <11||$actwriting<11)){
				
			$resultret = new JsonModel ( array (
		
					'response' => null,
					'error' => "Unfortunately we are not able to provide an accurate conversion for ACT scores lower than 11.",
					'ecode' => "100".$tmp,
					'request' => null
			) );
			return $resultret;
		
		}
		/*
		 *
		 * {
		 * "year": "2014",
		 * “composite” : “32” - (English+math + Science + Reading)/4 - Round up to nearest whole number
		 * "writing": 23
		 * }
		 */
		$requestJSON ['year'] = $actdatetaken;
		$requestJSON ['composite'] = floor(($actenglish + $actmath + $actreading + $actscience) / 4);
		$requestJSON ['writing'] = 0 + $actwriting;
		
		$r = new RESTJSONManager ();
		$url = Constants::PLAN_TESTING_ACTTOSAT;
		$url = str_replace ( "<UID>", $uid, $url );
		
		$jsonResponse = $r->PlanJSONManager ( 'POST', $url, json_encode ( $requestJSON ), $uid );
		
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
		
		$resultret = new JsonModel ( array (
				
				'response' => json_decode ( $jsonResponse->getBody (), true ),
				'error' => $error,
				'ecode' => $ecode,
				'request' => json_encode ( $requestJSON ),
				'date' => $acttosatyear 
		) );
		return $resultret;
	}
	function gpaAction() {
		$this->layout ( "layout/user" );
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		
		$schoolnames  = file_get_contents(Constants::ALL_SCHOOLS);
		
		$planRow = $this->getPlanTable ()->getPlanByUID ( $uid );
		$plangpaflag = "";
		foreach ( $planRow as $plan ) {
			
			$plangpaflag = $plan->gpa;
		}
		$result = array ();
		if ($plangpaflag == 1) {
			$r = new RESTJSONManager ();
			$url = Constants::PLAN_GPA_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			// echo $url;
			$result = $r->PlanJSONManager ( 'GET', $url, null, $uid );
			
			$view = new ViewModel ( array (
					"result" => json_decode ( $result->getBody (), true ),
					"planflag" => $plangpaflag,
					'uid' => $uid ,
					'schoolnames'=>$schoolnames,
					'allschool'=>$schoolnames
			) );
		} else {
			
			$view = new ViewModel ( array (
					"result" => $result,
					"planflag" => $plangpaflag,
					'uid' => $uid,
					'schoolnames'=>$schoolnames,'allschool'=>$schoolnames
			) );
		}
		return $view;
	}
	function getBestTestScoreAction(){
		/*if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}*/
		$this->layout ( "layout/user" );
		$jsonRequest = array ();
		$jsonRequest ['error'] = null;
		$jsonRequest ['errorCode'] = 0;
		$jsonRequest ['errorLevel'] = 0;				
		$uid = $this->params ()->fromPost ( 'uid' );
		//$uid=1013;
		$url = Constants::PLAN_UCEAZY_BEST_STD_TESTS;
		$url = str_replace ( "<UID>", $uid, $url );		
		//$this->LogMessage ( json_encode ( $jsonRequest ) );
		
		$planjson = new RESTJSONManager ();
		$jsonResponse = "";
		$jsonResponse = $planjson->PlanJSONManager ( 'GET', $url, json_encode ( $jsonRequest ), $uid );
		//$this->LogMessage ( $jsonResponse );
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
				'request' => json_encode ( $jsonRequest ) 
		) );
		return $resultret;
		
	}
	function gpaJSONAction() {
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
		$jsonRequest ['uid'] = $this->params ()->fromPost ( 'uid' );
		if ($this->params ()->fromPost ( 'schoolname' ) == "") {
			$jsonRequest ['schoolName'] = null;
		} else {
			$jsonRequest ['schoolName'] = $this->params ()->fromPost ( 'schoolname' );
		}
		$jsonRequest ['previousSchoolNames'] = $this->params ()->fromPost ( 'previousSchoolNames' );
		
		if ($this->params ()->fromPost ( 'county' ) == "-1") {
			$jsonRequest ['county'] = null;
		} else {
			$jsonRequest ['county'] = $this->params ()->fromPost ( 'county' );
		}
		
		$jsonRequest ['endDate'] = $this->params ()->fromPost ( 'year' );
		$jsonRequest ['stateName'] = $this->params ()->fromPost ( 'state' );
		$jsonRequest ['highSchoolCourses'] = array ();
		$alla2g = $this->params ()->fromPost ( 'a2g' );
		$totalSchoolInfo = count ( $this->params ()->fromPost ( 'a2g' ) );
		$i = 0;
		
		for($index = 0; $index < $totalSchoolInfo; $index ++) {
			$a2g = $alla2g [$index];
			
			$dynamicparameter9 = $alla2g [$index] . '_9_grade';
			$dynamicparameter10 = $alla2g [$index] . '_10_grade';
			$dynamicparameter11 = $alla2g [$index] . '_11_grade';
			$dynamicparameter12 = $alla2g [$index] . '_12_grade';
			
			// Loop for 9th grade
			for($tmp = 0; $tmp < count ( $this->params ()->fromPost ( $dynamicparameter9 ) ); $tmp ++) {
				
				$dynamiccname = $a2g . '_9_cname';
				$dynamicgradesem1 = $a2g . '_9_gradesem1';
				$dynamicgradesem2 = $a2g . '_9_gradesem2';
				$dynamicgradesem3 = $a2g . '_9_gradesem3';
				$dynamiclevel = $a2g . '_9_level';
				$dynamiccredit = $a2g . '_9_credit';
				
				$cname = $this->params ()->fromPost ( $dynamiccname );
				$gradesem1 = $this->params ()->fromPost ( $dynamicgradesem1 );
				$gradesem2 = $this->params ()->fromPost ( $dynamicgradesem2 );
				$gradesem3 = $this->params ()->fromPost ( $dynamicgradesem3 );
				$level = $this->params ()->fromPost ( $dynamiclevel );
				$credit = $this->params ()->fromPost ( $dynamiccredit );
				
				if ($cname [$tmp] != '' || $level [$tmp] != "" || $gradesem1 [$tmp] != 'Select' || $gradesem2 [$tmp] != 'Select' || $gradesem3 [$tmp] != 'Select' || $credit [$tmp] != '') {
					
					$jsonRequest ['highSchoolCourses'] [$i] = array (
							'year' => '',
							'subject' => $cname [$tmp],
							'a2g' => $a2g,
							'grade' => '9',
							'subjectCode' => '',
							'level' => $level [$tmp],
							'sem1Grade' => $gradesem1 [$tmp],
							'sem2Grade' => $gradesem2 [$tmp],
							'sem3Grade' => $gradesem3 [$tmp],
							'credit' => $credit [$tmp] 
					);
					$i ++;
				}
			}
			// Loop for 10 grade
			for($tmp = 0; $tmp < count ( $this->params ()->fromPost ( $dynamicparameter10 ) ); $tmp ++) {
				
				$dynamiccname = $a2g . '_10_cname';
				$dynamicgradesem1 = $a2g . '_10_gradesem1';
				$dynamicgradesem2 = $a2g . '_10_gradesem2';
				$dynamicgradesem3 = $a2g . '_10_gradesem3';
				$dynamiclevel = $a2g . '_10_level';
				$dynamiccredit = $a2g . '_10_credit';
				
				$cname = $this->params ()->fromPost ( $dynamiccname );
				$gradesem1 = $this->params ()->fromPost ( $dynamicgradesem1 );
				$gradesem2 = $this->params ()->fromPost ( $dynamicgradesem2 );
				$gradesem3 = $this->params ()->fromPost ( $dynamicgradesem3 );
				$level = $this->params ()->fromPost ( $dynamiclevel );
				$credit = $this->params ()->fromPost ( $dynamiccredit );
				
				if ($cname [$tmp] != '' || $level [$tmp] != "" || $gradesem1 [$tmp] != 'Select' || $gradesem2 [$tmp] != 'Select' || $gradesem2 [$tmp] != 'Select' || $credit [$tmp] != '') {
					
					$jsonRequest ['highSchoolCourses'] [$i] = array (
							'year' => '',
							'subject' => $cname [$tmp],
							'a2g' => $a2g,
							'grade' => '10',
							'subjectCode' => '',
							'level' => $level [$tmp],
							'sem1Grade' => $gradesem1 [$tmp],
							'sem2Grade' => $gradesem2 [$tmp],
							'sem3Grade' => $gradesem3 [$tmp],
							'credit' => $credit [$tmp] 
					);
					$i ++;
				}
			}
			
			// Loop for 11 grade
			for($tmp = 0; $tmp < count ( $this->params ()->fromPost ( $dynamicparameter11 ) ); $tmp ++) {
				
				$dynamiccname = $a2g . '_11_cname';
				$dynamicgradesem1 = $a2g . '_11_gradesem1';
				$dynamicgradesem2 = $a2g . '_11_gradesem2';
				$dynamicgradesem3 = $a2g . '_11_gradesem3';
				$dynamiclevel = $a2g . '_11_level';
				$dynamiccredit = $a2g . '_11_credit';
				
				$cname = $this->params ()->fromPost ( $dynamiccname );
				$gradesem1 = $this->params ()->fromPost ( $dynamicgradesem1 );
				$gradesem2 = $this->params ()->fromPost ( $dynamicgradesem2 );
				$gradesem3 = $this->params ()->fromPost ( $dynamicgradesem3 );
				$level = $this->params ()->fromPost ( $dynamiclevel );
				$credit = $this->params ()->fromPost ( $dynamiccredit );
				if ($cname [$tmp] != '' || $level [$tmp] != "" || $gradesem1 [$tmp] != 'Select' || $gradesem2 [$tmp] != 'Select' || $gradesem2 [$tmp] != 'Select' || $credit [$tmp] != '') {
					
					$jsonRequest ['highSchoolCourses'] [$i] = array (
							'year' => '',
							'subject' => $cname [$tmp],
							'a2g' => $a2g,
							'grade' => '11',
							'subjectCode' => '',
							'level' => $level [$tmp],
							'sem1Grade' => $gradesem1 [$tmp],
							'sem2Grade' => $gradesem2 [$tmp],
							'sem3Grade' => $gradesem3 [$tmp],
							'credit' => $credit [$tmp] 
					);
					$i ++;
				}
			}
			// Loop for 12 grade
			for($tmp = 0; $tmp < count ( $this->params ()->fromPost ( $dynamicparameter12 ) ); $tmp ++) {
				
				$dynamiccname = $a2g . '_12_cname';
				$dynamicgradesem1 = $a2g . '_12_gradesem1';
				$dynamicgradesem2 = $a2g . '_12_gradesem2';
				$dynamicgradesem3 = $a2g . '_12_gradesem3';
				$dynamiclevel = $a2g . '_12_level';
				$dynamiccredit = $a2g . '_12_credit';
				
				$cname = $this->params ()->fromPost ( $dynamiccname );
				$gradesem1 = $this->params ()->fromPost ( $dynamicgradesem1 );
				$gradesem2 = $this->params ()->fromPost ( $dynamicgradesem2 );
				$gradesem3 = $this->params ()->fromPost ( $dynamicgradesem3 );
				$level = $this->params ()->fromPost ( $dynamiclevel );
				$credit = $this->params ()->fromPost ( $dynamiccredit );
				
				if ($cname [$tmp] != '' || $level [$tmp] != "" || $gradesem1 [$tmp] != 'Select' || $gradesem2 [$tmp] != 'Select' || $gradesem2 [$tmp] != 'Select' || $credit [$tmp] != '') {
					
					$jsonRequest ['highSchoolCourses'] [$i] = array (
							'year' => '',
							'subject' => $cname [$tmp],
							'a2g' => $a2g,
							'grade' => '12',
							'subjectCode' => '',
							'level' => $level [$tmp],
							'sem1Grade' => $gradesem1 [$tmp],
							'sem2Grade' => $gradesem2 [$tmp],
							'sem3Grade' => $gradesem3 [$tmp],
							'credit' => $credit [$tmp] 
					);
					$i ++;
				}
			}
		}
		
		$jsonRequest ['commCollegeCourses'] = array ();
		
		$commCollCount = count ( $this->params ()->fromPost ( "ccollegename" ) );
		
		$cunits = $this->params ()->fromPost ( 'cunits' );
		$ccollegename = $this->params ()->fromPost ( 'ccollegename' );
		$cstate = $this->params ()->fromPost ( 'cstate' );
		$ccoursename = $this->params ()->fromPost ( 'ccoursename' );
		
		$cgrade = $this->params ()->fromPost ( 'cgrade' );
		$ctrems = $this->params ()->fromPost ( 'ctrems' );
		$cyear = $this->params ()->fromPost ( 'cyear' );
		// echo "--->".$commCollCount;
		
		// $cgrade[$tmp];
		
		for($tmp = 0; $tmp < $commCollCount; $tmp ++) {
			
			$sem1 = "";
			$sem2 = "";
			$sem3 = "";
			if ($ctrems [$tmp] == 'Fall') {
				$sem1 = $cgrade [$tmp];
			} else if ($ctrems [$tmp] == 'Winter') {
				$sem2 = $cgrade [$tmp];
			} else if ($ctrems [$tmp] == 'Summer') {
				$sem3 = $cgrade [$tmp];
			} else if ($ctrems [$tmp] == 'Select') {
				$sem1 = $cgrade [$tmp];
			}else{
				$sem1 = $cgrade [$tmp];
			}
			
			if ($cyear [$tmp] != 'Select' || $ccoursename [$tmp] != '' || $cgrade [$tmp] != 'Select' || $ccollegename [$tmp] != '' || $cunits [$tmp] != '' || $ctrems [$tmp] != 'Select') {
				$jsonRequest ['commCollegeCourses'] [$tmp] = array (
						"state" => $cstate [$tmp],
						"year" => $cyear [$tmp],
						"subject" => $ccoursename [$tmp],
						"a2g" => "",
						"grade" => 0,
						"subjectCode" => "",
						"sem1Grade" => $sem1,
						"sem2Grade" => $sem2,
						"sem3Grade" => $sem3,
						"level" => "",
						"collegeName" => $ccollegename [$tmp],
						"units" => $cunits [$tmp],
						"term" => 0 
				);
			}
		}
		
		//
		//
		
		$planjson = new RESTJSONManager ();
		$jsonResponse = "";
		
		if ($mode == "update") {
			
			$url = Constants::PLAN_GPA_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			
			$this->LogMessage ( json_encode ( $jsonRequest ) );
			$jsonResponse = $planjson->PlanJSONManager ( 'PUT', $url, json_encode ( $jsonRequest ), $uid );
			$this->LogMessage ( $jsonResponse );
		} elseif ($mode == "save") {
			
			$this->LogMessage ( json_encode ( $jsonRequest ) );
			$this->getPlanTable ()->updateGPA ( $uid );
			
			$url = Constants::PLAN_GPA_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			$this->LogMessage ( json_encode ( $jsonRequest ) );
			$jsonResponse = $planjson->PlanJSONManager ( 'POST', $url, json_encode ( $jsonRequest ), $uid );
			$this->LogMessage ( $jsonResponse );
		} elseif ($mode == "gpacalc") {
			
			$url = Constants::PLAN_GPA_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			$jsonResponse = $planjson->PlanJSONManager ( 'POST', $url, json_encode ( $jsonRequest ), $uid );
			
			$url = Constants::PLAN_GPA_CALC_GENERAL;
			$url = str_replace ( "<UID>", $uid, $url );
			$jsonResponse = $planjson->PlanJSONManager ( 'GET', $url, null, $uid );
		} elseif ($mode == "gpaweight_csu") {
			
			$url = Constants::PLAN_GPA_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			$jsonResponse = $planjson->PlanJSONManager ( 'POST', $url, json_encode ( $jsonRequest ), $uid );
			
			$url = Constants::PLAN_GPA_CALC_CSU;
			$url = str_replace ( "<UID>", $uid, $url );
			$jsonResponse = $planjson->PlanJSONManager ( 'GET', $url, json_encode ( $jsonRequest ), $uid );
		}
		elseif ($mode == "gpaweight_uc") {
				
			$url = Constants::PLAN_GPA_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			$jsonResponse = $planjson->PlanJSONManager ( 'POST', $url, json_encode ( $jsonRequest ), $uid );
				
			$url = Constants::PLAN_GPA_CALC_UC;
			$url = str_replace ( "<UID>", $uid, $url );
			$jsonResponse = $planjson->PlanJSONManager ( 'GET', $url, json_encode ( $jsonRequest ), $uid );
		}
		
		$error = "";
		$ecode = "";
		$response = "";
		
		if ($jsonResponse != null) {
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
					'url' => $url,
					'error' => $error,
					'ecode' => $ecode,
					'request' => json_encode ( $jsonRequest ) 
			) );
		} else {
			$resultret = new JsonModel ( array (
					
					'response' => "",
					'error' => "Access error",
					'ecode' => $ecode,
					'request' => json_encode ( $jsonRequest ) 
			) );
		}
		
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