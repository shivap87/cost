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
	protected $organizerTable;
	protected $essayTable;
	
	public function getPlanTable() {
		if (! $this->planTable) {
			$sm = $this->getServiceLocator ();
			$this->planTable = $sm->get ( 'PlanService' );
		}
		return $this->planTable;
	}
	
	public function getOrganizerTable()
	{
		if (!$this->organizerTable) {
			$sm = $this->getServiceLocator();
			$this->organizerTable = $sm->get('OrganizerService');
		}
		return $this->organizerTable;
	}
	// for essay table service
	public function getEssayTable()
    {
        if (!$this->essayTable) {
            $sm = $this->getServiceLocator();
            $this->essayTable = $sm->get('EssayService');
        }
        return $this->essayTable;
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
	function personalHomeAction() {
		
		
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
			$plangpaflag = $plan->personal;
		}
		
		$fixedQuestions = "";
		
		if($plangpaflag==null || $plangpaflag == ''){
			
			$level1Array=array(1,2,3);
			$level2Array=array(4,5,6);
			$level3Array=array(7,8);
				
			$i=0;
			$val='';
			foreach ( $level1Array as $level ) {
				$val .= $level . '#' . rand(1,3) .",";
			}
			
			$val .=":";
			foreach ( $level2Array as $level ) {
				$val .= $level . '#' . rand(1,3) .",";
			}
				
			$val .=":";
			foreach ( $level3Array as $level ) {
				$val .= $level . '#' . rand(1,3) .",";
			}
			
			
			$planRow = $this->getPlanTable ()->insertPersonal ( $uid ,$val);
			$fixedQuestions = $val;
			
		}else{
			$fixedQuestions = $plangpaflag;
		}
		
		$apiManager = new RESTJSONManager ();
		$url = Constants::PLAN_PERSONAL_GET_ALL_QUESTIONS;
		$result = $apiManager->PlanJSONManager ( "GET", $url, null, $uid );
		// Level 1 = 3, level 2 = 3 and level 3 = 2;
		if ($result == "" || $result == null) {
			$view = new ViewModel ( array (
					"result" => array (),
					"planflag" => $plangpaflag,
					'uid' => $uid 
			) );
		} else if ($result->getStatusCode () == '200') {
			$questions = json_decode ( $result->getBody (), true );
			$contextList = array ();
			$contextList [1] = $questions ['contextList'] [0] ['title'];
			$contextList [2] = $questions ['contextList'] [1] ['title'];
			$contextList [3] = $questions ['contextList'] [2] ['title'];
			$contextList [4] = $questions ['contextList'] [3] ['title'];
			$contextList [5] = $questions ['contextList'] [4] ['title'];
			$contextList [6] = $questions ['contextList'] [5] ['title'];
			$contextList [7] = $questions ['contextList'] [6] ['title'];
			$contextList [8] = $questions ['contextList'] [7] ['title'];
			$questionList = array ();
			$tmpArray = $questions ['contextList'];
			foreach ( $tmpArray as $row ) {
				foreach ( $row ['excerptList'] as $tmp ) {
					$questionList [$tmp ['contextId']] [$tmp ['id']] = $tmp;
				}
			}
			$session->questionList = $questionList;
			$session->contextList = $contextList;
			
			
			$question1=array();
			$question2=array();
			$question3=array();
			
			$questionArray = explode(":", $fixedQuestions);
			
			$q1 = explode(",",$questionArray[0]);
			$q2 = explode(",",$questionArray[1]);
			$q3 = explode(",",$questionArray[2]);
		
			
			foreach ($q1 as $q){
			
				if($q!=''){
					$question1[$q]=$q;
				}
				
			}
			
			foreach ($q2 as $q){
					
				if($q!=''){
					$question2[$q]=$q;
				}
			
			}
			
			foreach ($q3 as $q){
					
				if($q!=''){
					$question3[$q]=$q;
				}
			
			}
			
			$session->question1=$question1;
			$session->question2=$question2;
			$session->question3=$question3;
			
			$url = Constants::PLAN_GET_ANSWERED_QUESTIONS;
			$url = str_replace ( '<UID>', $uid, $url );
			$res = $apiManager->PlanJSONManager ( 'GET', $url, null, $uid );
			$totalRows = json_decode ( $res->getBody (), true );
			$showsummery = false;
			if (count ( $totalRows ['userChoices'] ) == 8) {
				$showsummery = true;
			}
			$view = new ViewModel ( array (
					"result" => json_decode ( $result->getBody (), true ),
					"planflag" => $plangpaflag,
					'uid' => $uid,
					'summary' => $showsummery 
			) );
		} else {
			$view = new ViewModel ( array (
					"result" => array (),
					"planflag" => $plangpaflag,
					'uid' => $uid 
			) );
		}
		return $view;
	}
	function sendsummaryAction() {
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		$questionList = $session->questionList;
		// get All answer list
		$apimanager = new RESTJSONManager ();
		$url = Constants::PLAN_GET_ANSWERED_QUESTIONS;
		$url = str_replace ( '<UID>', $uid, $url );
		$result = $apimanager->PlanJSONManager ( 'GET', $url, null, $uid );
		$allAnswer = json_decode ( $result->getBody (), true );
		$bodyMsg = "<table border=1>";
		foreach ( $allAnswer ['userChoices'] as $row ) {
			$bodyMsg .= "<tr>";
			$bodyMsg .= "<td>Question</td>";
			$context = $row ['contextId'];
			$question = $row ['excerptId'];
			$userAns = $row ['userAnswer'];
			$bodyMsg .= "<td>" . $questionList [$context] [$question] ['excerpt'] . "</td>";
			$bodyMsg .= "</tr>";
			$bodyMsg .= "<tr><td>User Answer </td>";
			$bodyMsg .= "<td>" . $userAns . "</td></tr>";
			$bodyMsg .= "<tr><td>Correct Answer</td>";
			$bodyMsg .= "<td>" . $questionList [$context] [$question] ['correctAnswer'] . "</td></tr>";
			$bodyMsg .= "<tr><td>Correct Answer Description </td>";
			$desc=str_replace("The answer you chose is CORRECT!",'',$questionList [$context] [$question] ['correctAnswerDescription']);
			$bodyMsg .= "<td>" .$desc . "</td></tr>";
		}
		$bodyMsg .= "</table>";
		//echo $bodyMsg;
		
		$mail = new EmailManager ();
		$mail->sendPersonalStatement ( $username, $username, $bodyMsg );
		
		$view = new JsonModel ( array (
				'flag' => true 
		) );
		return $view;
	}
	function checkAnswerAction() {
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		$flag = false;
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		
		
		$questionList = $session->questionList;
		$contextID = trim ( $this->params ()->fromPost ( 'contextID' ) );
		$questionID = trim ( $this->params ()->fromPost ( 'questionID' ) );
		$userAnswer = trim ( $this->params ()->fromPost ( 'answer' ) );
		$correctanswer = $questionList [$contextID] [$questionID] ['correctAnswer'];
		$api = new RESTJSONManager ();
		$url = Constants::PLAN_GET_ANSWERED_QUESTIONS;
		$url = str_replace ( "<UID>", $uid, $url );
		$result = $api->PlanJSONManager ( "GET", $url, null, $uid );
		$resultArray = json_decode ( $result->getBody (), true );
		$count = count ( $resultArray ['userChoices'] );
		if ($count == 0) {
			$jsonRequest = array ();
			$jsonRequest ['error'] = null;
			$jsonRequest ['errorCode'] = 0;
			$jsonRequest ['errorLevel'] = 0;
			$jsonRequest ['uid'] = $uid;
			$jsonRequest ['notes'] = array (
					null 
			);
			$jsonRequest ['userChoices'] [$count] = array (
					'contextId' => $contextID,
					'excerptId' => $questionID,
					'userAnswer' => $userAnswer 
			);
			$storeResponse = $api->PlanJSONManager ( 'POST', $url, json_encode ( $jsonRequest ), $uid );
		} else {
			$resultArray ['userChoices'] [$count] = array (
					'contextId' => $contextID,
					'excerptId' => $questionID,
					'userAnswer' => $userAnswer 
			);
			$storeResponse = $api->PlanJSONManager ( 'PUT', $url, json_encode ( $resultArray ), $uid );
		}
		if ($userAnswer == $correctanswer) {
			$flag = true;
		} else {
			$flag = false;
		}
		
		$view = new JsonModel ( array (
				"answer" => $flag 
		) );
		return $view;
	}
	function level1Action() {
		$this->layout ( "layout/user" );
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		$questionList = $session->questionList;
		$contextList = $session->contextList;
		$api = new RESTJSONManager ();
		$url = Constants::PLAN_GET_ANSWERED_QUESTIONS;
		$url = str_replace ( '<UID>', $uid, $url );
		$result = $api->PlanJSONManager ( 'GET', $url, null, $uid );
		$answedList = json_decode ( $result->getBody (), true );
	/* 	$level1Array = array (
				1,
				2,
				3 
		);
	 */	
		/* $questionArray = array (
				1,
				2,
				3 
		); */
		$questionFlag = false;
		//$levelNumber = 1;
		//$questionNumber = 1;
		//$questionTMP = array ();
		//$answerTMP = array ();
		$i = 0;
		/* foreach ( $level1Array as $level ) {

			$questionTMP [$i ++] = $level . '#' . rand(1,3);;
			
		}
 */		
		$i = 0;

		$questionTMP=$session->question1;
		$answerTMP = array ();
		foreach ( $answedList ['userChoices'] as $tmp ) {
			$answerTMP [$i ++] = $tmp ['contextId'] . '#' . $tmp ['excerptId'];
		}
		
		$diff = array_diff ( $questionTMP, $answerTMP );
		
	//	var_dump($diff);
		
		
		if (count ( $diff ) > 0) {
			foreach ( $diff as $d ) {
				$questionFlag = true;
				$val = explode ( "#", $d );
				$levelNumber = $val [0];
				$questionNumber = $val [1];
				break;
			}
		} else {
			$questionFlag = false;
		}
		
		if($questionFlag==false){
			$view = new ViewModel ( array (
					
					'flag' => $questionFlag,
					'btn_array'=>$this->getProfile_home_btns()			
			) );
			return $view;
		}else{
		$view = new ViewModel ( array (
				'question' => $questionList [$levelNumber] [$questionNumber],
				'title' => $contextList [$levelNumber],
				'flag' => $questionFlag 
		) );
		return $view;
		}
	}
	function level2Action() {
	$this->layout ( "layout/user" );
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		$questionList = $session->questionList;
		$contextList = $session->contextList;
		$api = new RESTJSONManager ();
		$url = Constants::PLAN_GET_ANSWERED_QUESTIONS;
		$url = str_replace ( '<UID>', $uid, $url );
		$result = $api->PlanJSONManager ( 'GET', $url, null, $uid );
		$answedList = json_decode ( $result->getBody (), true );
	/* 	$level1Array = array (
				1,
				2,
				3 
		);
	 */	
		/* $questionArray = array (
				1,
				2,
				3 
		); */
		$questionFlag = false;
		//$levelNumber = 1;
		//$questionNumber = 1;
		//$questionTMP = array ();
		//$answerTMP = array ();
		$i = 0;
		/* foreach ( $level1Array as $level ) {

			$questionTMP [$i ++] = $level . '#' . rand(1,3);;
			
		}
 */		
		$i = 0;

		$questionTMP=$session->question2;
		$answerTMP = array ();
		foreach ( $answedList ['userChoices'] as $tmp ) {
			$answerTMP [$i ++] = $tmp ['contextId'] . '#' . $tmp ['excerptId'];
		}
		
		$diff = array_diff ( $questionTMP, $answerTMP );
		
		if (count ( $diff ) > 0) {
			foreach ( $diff as $d ) {
				$questionFlag = true;
				$val = explode ( "#", $d );
				$levelNumber = $val [0];
				$questionNumber = $val [1];
				break;
			}
		} else {
			$questionFlag = false;
		}
		
		if($questionFlag==false){
			$view = new ViewModel ( array (
					
					'flag' => $questionFlag,
					'btn_array'=>$this->getProfile_home_btns()
			) );
			return $view;
		}else{
		$view = new ViewModel ( array (
				'question' => $questionList [$levelNumber] [$questionNumber],
				'title' => $contextList [$levelNumber],
				'flag' => $questionFlag 
		) );
		return $view;
		}
	}
	function level3Action() {
	$this->layout ( "layout/user" );
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		$questionList = $session->questionList;
		$contextList = $session->contextList;
		$api = new RESTJSONManager ();
		$url = Constants::PLAN_GET_ANSWERED_QUESTIONS;
		$url = str_replace ( '<UID>', $uid, $url );
		$result = $api->PlanJSONManager ( 'GET', $url, null, $uid );
		$answedList = json_decode ( $result->getBody (), true );
	/* 	$level1Array = array (
				1,
				2,
				3 
		);
	 */	
		/* $questionArray = array (
				1,
				2,
				3 
		); */
		$questionFlag = false;
		//$levelNumber = 1;
		//$questionNumber = 1;
		//$questionTMP = array ();
		//$answerTMP = array ();
		$i = 0;
		/* foreach ( $level1Array as $level ) {

			$questionTMP [$i ++] = $level . '#' . rand(1,3);;
			
		}
 */		
		$i = 0;

		$questionTMP=$session->question3;
		$answerTMP = array ();
		foreach ( $answedList ['userChoices'] as $tmp ) {
			$answerTMP [$i ++] = $tmp ['contextId'] . '#' . $tmp ['excerptId'];
		}
		
		$diff = array_diff ( $questionTMP, $answerTMP );
		
		if (count ( $diff ) > 0) {
			foreach ( $diff as $d ) {
				$questionFlag = true;
				$val = explode ( "#", $d );
				$levelNumber = $val [0];
				$questionNumber = $val [1];
				break;
			}
		} else {
			$questionFlag = false;
		}
		
		if($questionFlag==false){
			$view = new ViewModel ( array (
					
					'flag' => $questionFlag,
					'btn_array'=>$this->getProfile_home_btns()
			) );
			return $view;
		}else{
		$view = new ViewModel ( array (
				'question' => $questionList [$levelNumber] [$questionNumber],
				'title' => $contextList [$levelNumber],
				'flag' => $questionFlag 
		) );
		return $view;
		}
	}
	function getProfile_home_btns(){
		$session = new Container ( 'user' );	
		$uid = $session->userid;
		
		$api = new RESTJSONManager ();
		$url = Constants::PLAN_GET_ANSWERED_QUESTIONS;
		$url = str_replace ( '<UID>', $uid, $url );
		$result = $api->PlanJSONManager ( 'GET', $url, null, $uid );
		$answedList = json_decode ( $result->getBody (), true );	
		
		$answerTMP = array ();
		$i=1;
		foreach ( $answedList ['userChoices'] as $tmp ) {
			$answerTMP [$i ++] = $tmp ['contextId'] . '#' . $tmp ['excerptId'];
		}
		$questions_list=array($session->question1,$session->question2,$session->question3);
		$j=1;
		$level1Status=$level2Status=$level3Status=0;	
		foreach ($questions_list as $set) {
			foreach ($set as $question){
				if(!in_array($question, $answerTMP)){
					${'level'."$j".'Status'}+=1;			 
				}
			}$j+=1;
		}
		$return=array();
		$tot=count($session->question1)+count($session->question2)+count($session->question3);
		$btn1=$btn2=$btn3=$email_btn="no";
		if($tot==count($answerTMP)){
			$email_btn='yes';
		}else{

			if ($level1Status>0){
				$btn1="yes";
			}
			if ($level2Status>0){
				$btn2="yes";
			}
			if ($level3Status>0){
				$btn3="yes";
			}

		}
		$return=array(
			"btn1"=>$btn1,
			"btn2"=>$btn2,
			"btn3"=>$btn3,
			"email_btn"=>$email_btn
		);
		return $return;				
	}
	
	
	function replayGameAction(){
	
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		$this->layout ( "layout/user" );		
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		$api = new RESTJSONManager ();
		$url = Constants::PLAN_GET_ANSWERED_QUESTIONS;
		$url = str_replace ( '<UID>', $uid, $url );
		$result = $api->PlanJSONManager ( 'DELETE', $url, null, $uid );
		$status = json_decode ( $result->getBody (), true );
			
		$level1Array=array(1,2,3);
		$level2Array=array(4,5,6);
		$level3Array=array(7,8);

		$i=0;
		$val='';
		foreach ( $level1Array as $level ) {
			$val .= $level . '#' . rand(1,3) .",";
		}
			
		$val .=":";
		foreach ( $level2Array as $level ) {
			$val .= $level . '#' . rand(1,3) .",";
		}

		$val .=":";
		foreach ( $level3Array as $level ) {
			$val .= $level . '#' . rand(1,3) .",";
		}
		$planRow = $this->getPlanTable ()->insertPersonal ( $uid ,$val);
			
		$view = new JsonModel ( array (
				"flag" => true,
				"status" =>$status,
				"planRow"=>$planRow
		) );

		return $view;
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
	function testingAction() {
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
			$plangpaflag = $plan->testing;
		}
		$result = array ();
		
		if ($plangpaflag == 1) {
			$r = new RESTJSONManager ();
			$url = Constants::PLAN_TESTING_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			// echo $url;
			$result = $r->PlanJSONManager ( 'GET', $url, null, $uid );
			if ($result == "" || $result == null) {
				$view = new ViewModel ( array (
						"result" => array (),
						"planflag" => $plangpaflag,
						'uid' => $uid 
				) );
			} else if ($result->getStatusCode () == '200') {
				$view = new ViewModel ( array (
						"result" => json_decode ( $result->getBody (), true ),
						"planflag" => $plangpaflag,
						'uid' => $uid 
				) );
			} else {
				$view = new ViewModel ( array (
						"result" => array (),
						"planflag" => $plangpaflag,
						'uid' => $uid 
				) );
			}
		} else {
			$view = new ViewModel ( array (
					"result" => array (),
					"planflag" => $plangpaflag,
					'uid' => $uid 
			) );
		}
		
		return $view;
	}
	function personalAction() {
		$this->layout ( "layout/user" );		
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		/*check user's Service purchase history*/
		$eligible='no';
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "SELECT * FROM tbl_buyservice WHERE tbl_buyservice.user_id=$uid  and tbl_buyservice.id NOT IN (SELECT tbl_essay.buy_id FROM tbl_essay )";        	
        $stmt = $adapter->createStatement($sql);        	
        $stmt->prepare();
        $result = $stmt->execute();
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        $un_used_service=$resultSet->toArray();
               
        $open_essays=$this->getEssayTable()->get_openEssays($uid);
        if($open_essays->count()<=0 && count($un_used_service)<=0){
        	$eligible='yes';
        }        
		/*Service code ends here */   
		
		
		$view = new ViewModel ( array (
					'service_eligible'=>$eligible
		) );
			
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
	function activitiesAction() {
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
	function testingJSONAction() {
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
		
		// SAT
		
		$starcount = 0;
		//$jsonRequest ['starCount'] =$starcount;
		//if($count > 0) {
			//$starcount++;
		//}
		
		$satarray = $this->params ()->fromPost ( 'satdatetaken' );
		$count = count ( $satarray );
		
		if($count > 0) {
			$starcount++;
		}
		
		$satdatetaken = $this->params ()->fromPost ( 'satdatetaken' );
		$satcr = $this->params ()->fromPost ( 'satcr' );
		$satmath = $this->params ()->fromPost ( 'satmath' );
		$satwriting = $this->params ()->fromPost ( 'satwriting' );
		
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['sats'] [$i] = array (
					"year" => $satdatetaken [$i],
					"criticalReading" => $satcr [$i],
					"math" => $satmath [$i],
					"writing" => $satwriting [$i],
					"criticalReadingAndMath" => ($satcr [$i] + $satmath [$i]) 
			);
		}
		
		// ACT
		
		$actarray = $this->params ()->fromPost ( 'actdatetaken' );
		$count = count ( $actarray );
		
		if($count > 0) {
			$starcount++;
		}
		
		
		$buttonAction = $this->params ()->fromPost ( 'buttonaction' );
		
		
		
		$actcomposite = $this->params ()->fromPost ( 'actcomposite' );
		$actenglish = $this->params ()->fromPost ( 'actenglish' );
		$actmath = $this->params ()->fromPost ( 'actmath' );
		$actreading = $this->params ()->fromPost ( 'actreading' );
		$actscience = $this->params ()->fromPost ( 'actscience' );
		$actwriting = $this->params ()->fromPost ( 'actwriting' );
		
		for($i = 0; $i < $count; $i ++) {
			
	if(($actenglish [$i]<11||$actmath [$i]<11||$actscience [$i]<11||$actreading [$i]<11||$actwriting [$i]<11)&& $buttonAction =="bestscore"){
			
				$resultret = new JsonModel ( array (
				
						'response' => null,
						'error' => "Unfortunately we are not able to provide an accurate conversion for ACT scores lower than 11.",
						'ecode' => "100".$buttonAction,
						'request' => null
				) );
				return $resultret;  
				
			}else{
				$jsonRequest ['acts'] [$i] = array (
						"year" => $actarray [$i],
						"composite" => $actcomposite [$i],
						"english" => $actenglish [$i],
						"math" => $actmath [$i],
						"science" => $actscience [$i],
						'reading' => $actreading [$i],
						'writing' => $actwriting [$i]
				);
			}
			
		}
		
		$psatarray = $this->params ()->fromPost ( 'psatgrade' );
		$count = count ( $psatarray );
		
		if($count > 0) {
			$starcount++;
		}
		
		$psatgrade = $this->params ()->fromPost ( 'psatgrade' );
		$psatcr = $this->params ()->fromPost ( 'psatcr' );
		$psatmath = $this->params ()->fromPost ( 'psatmath' );
		$psatwriting = $this->params ()->fromPost ( 'psatwriting' );
		
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['psats'] [$i] = array (
					"year" => $psatarray [$i],
					"gradeTaken" => $psatgrade [$i],
					"criticalReading" => $psatcr [$i],
					"math" => $psatmath [$i],
					"writing" => $psatwriting [$i] 
			);
		}
		
		// SAT Subj
		$satsubsarray = $this->params ()->fromPost ( 'satsubtest' );
		$count = count ( $satsubsarray );
		
		
		
		$satsubtestdate = $this->params ()->fromPost ( 'satsubtestdate' );
		$satsubscore = $this->params ()->fromPost ( 'satsubscore' );
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['subjectSats'] [$i] = array (
					"year" => $satsubtestdate [$i],
					"subject" => $satsubsarray [$i],
					"score" => $satsubscore [$i] 
			);
		}
		
		$apsarray = $this->params ()->fromPost ( 'apexamscore' );
		$count = count ( $apsarray );
		$apexam = $this->params ()->fromPost ( 'apexam' );
		$apexamdate = $this->params ()->fromPost ( 'apexamdate' );
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['subjectAPs'] [$i] = array (
					"year" => $apexamdate [$i],
					"subject" => $apexam [$i],
					"score" => $apsarray [$i] 
			);
		}
		
		$ibsarray = $this->params ()->fromPost ( 'ibexam' );
		$count = count ( $ibsarray );
		$ibexam = $this->params ()->fromPost ( 'ibexam' );
		$iblevel = $this->params ()->fromPost ( 'iblevel' );
		$ibdate = $this->params ()->fromPost ( 'ibdate' );
		$ibscore = $this->params ()->fromPost ( 'ibscore' );
		$isibdiplomo = $this->params ()->fromPost ( 'isibdiplomo' );
		
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['intBacs'] [$i] = array (
					"year" => $ibdate [$i],
					"level" => $iblevel [$i],
					"score" => $ibscore [$i],
					"subject" => $ibsarray [$i],
					"diplomaCandidate" => Convertors::nullSub ( $isibdiplomo [$i] ) 
			);
		}
		
		if($count > 0) {
			$starcount++;
		}
		$jsonRequest ['starCount'] =$starcount;
		$mode = $this->params ()->fromPost ( 'mode' );
		$uid = $this->params ()->fromPost ( 'uid' );
		
		$planjson = new RESTJSONManager ();
		$jsonResponse = "";
		
		if ($mode == "update") {
			
			$url = Constants::PLAN_TESTING_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			
			$this->LogMessage ( json_encode ( $jsonRequest ) );
			$jsonResponse = $planjson->PlanJSONManager ( 'PUT', $url, json_encode ( $jsonRequest ), $uid );
			$this->LogMessage ( $jsonResponse );
		} elseif ($mode == "save") {
			
			$this->getPlanTable ()->updateTesting ( $uid );
			
			$url = Constants::PLAN_TESTING_BASE_URL;
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
				'request' => json_encode ( $jsonRequest ) 
		) );
		return $resultret;
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
	function organisationAction() {
		$this->layout ( "layout/user" );
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
			
			
		}
		
		$this->layout("layout/user");
		$result_set=$this->getOrganizerTable()->getAllEvents();
		foreach ($result_set as $row){
			$result[]=$row;
		}
		$view = new ViewModel(array("result"=>$result));
		
		return $view;
	
	}
	function costsJSONAction() {
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

		$costcount = 0;
		
		$jsonRequest ['notes'] = array (
				$this->params ()->fromPost ( 'note' ) 
		);
		
		
		$jsonRequest ['uid'] = $this->params ()->fromPost ( 'uid' );
		
		$jsonRequest ['freeLunch'] = $this->params ()->fromPost ( 'freelunch' );
		$jsonRequest ['feeWaivers'] = $this->params ()->fromPost ( 'freewaivers' );
		$jsonRequest ['residency'] = $this->params ()->fromPost ( 'livewith' );
		
		
		if($jsonRequest ['freeLunch']!='' && $jsonRequest ['residency']!='' ){
			$costcount++;
		}
		
		
		$jsonRequest ['numOfUC'] = $this->params ()->fromPost ( 'ucappcount' );
		$jsonRequest ['numOfCSU'] = $this->params ()->fromPost ( 'csuappcount' );
		
		
		if($jsonRequest ['numOfUC']!='0' && $jsonRequest ['numOfCSU']!='0'){
			$costcount++;
		}
		
		
		$jsonRequest ['numOfSAT'] = $this->params ()->fromPost ( 'satcount' );
		$jsonRequest ['numOfACT'] = $this->params ()->fromPost ( 'actcount' );
		$jsonRequest ['numOfSubjectSAT'] = $this->params ()->fromPost ( 'satsubcount' );
		$jsonRequest ['sendingStdTestNum'] = $this->params ()->fromPost ( 'testscorecount' );
		
		if($jsonRequest ['numOfSAT']!='0' && $jsonRequest ['numOfACT']!='0'&& $jsonRequest ['numOfSubjectSAT']!='0'&& $jsonRequest ['sendingStdTestNum']!='0'){
			$costcount++;
		}
		
		$jsonRequest ['costOfUCAttendance'] = $this->params ()->fromPost ( 'coauc' );
		$jsonRequest ['costOfCSUAttendance'] = $this->params ()->fromPost ( 'coacsu' );
		
		if($jsonRequest ['costOfUCAttendance']!='' || $jsonRequest ['costOfCSUAttendance']!=''){
			$costcount++;
		}
		
		
		$jsonRequest ['costOfSchoolTranscript'] = $this->params ()->fromPost ( 'schooltransfee' );
		
		$commcollValue = $this->params ()->fromPost ( 'commcoll' );
		
		if ($commcollValue == 'trueuc') {
			$jsonRequest ['twoYearsCommToUC'] = true;
		} else if ($commcollValue == 'falseuc') {
			$jsonRequest ['twoYearsCommToUC'] = false;
		} else if ($commcollValue == 'truecsu') {
			$jsonRequest ['twoYearsCommToCSU'] = true;
		} else if ($commcollValue == 'falsecsu') {
			$jsonRequest ['twoYearsCommToCSU'] = false;
		}
		

		if($jsonRequest ['costOfSchoolTranscript']!='' &&  $commcollValue!=''){
			$costcount++;
		}
		
		$jsonRequest ['starCount'] =$costcount;
		
		$planjson = new RESTJSONManager ();
		
		if ($mode == "update") {
			
			$url = Constants::PLAN_COSTS_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			
			$this->LogMessage ( json_encode ( $jsonRequest ) );
			$jsonResponse = $planjson->PlanJSONManager ( 'PUT', $url, json_encode ( $jsonRequest ), $uid );
			$this->LogMessage ( $jsonResponse );
		} elseif ($mode == "save") {
			
			$this->getPlanTable ()->updateCost ( $uid );
			
			$url = Constants::PLAN_COSTS_BASE_URL;
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
				'request' => json_encode ( $jsonRequest ) 
		) );
		return $resultret;
	}
	function costsAction() {
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
			
			$plangpaflag = $plan->costs;
		}
		$result = array ();
		if ($plangpaflag == 1) {
			$r = new RESTJSONManager ();
			$url = Constants::PLAN_COSTS_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			// echo $url;
			$result = $r->PlanJSONManager ( 'GET', $url, null, $uid );
			
			$view = new ViewModel ( array (
					"result" => json_decode ( $result->getBody (), true ),
					"planflag" => $plangpaflag,
					'uid' => $uid 
			) );
		} else {
			
			$view = new ViewModel ( array (
					"result" => $result,
					"planflag" => $plangpaflag,
					'uid' => $uid 
			) );
		}
		return $view;
	}
	
	function resumehomeAction(){
		$this->layout ( "layout/user" );
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
	}
	function resumeAction() {
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
			
			$plangpaflag = $plan->activities;
		}
		$result = array ();
		if ($plangpaflag == 1) {
			$r = new RESTJSONManager ();
			$url = Constants::PLAN_RESUME_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			// echo $url;
			$result = $r->PlanJSONManager ( 'GET', $url, null, $uid );
			
			$view = new ViewModel ( array (
					"result" => json_decode ( $result->getBody (), true ),
					"planflag" => $plangpaflag,
					'uid' => $uid 
			) );
		} else {
			
			$view = new ViewModel ( array (
					"result" => $result,
					"planflag" => $plangpaflag,
					'uid' => $uid 
			) );
		}
		return $view;
	}
	function resumeJSONAction() {
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
		
		$starcount = 0;
		//$jsonRequest ['starCount'] =$starcount;
		//if($count > 0) {
		//$starcount++;
		//}
		
		
		// Education
		
		$eduhrs = $this->params ()->fromPost ( 'eduhrs' );
		$eduweeks = $this->params ()->fromPost ( 'eduweeks' );
		$eduyears = $this->params ()->fromPost ( 'eduyears' );
		$edurole = $this->params ()->fromPost ( 'edurole' );
		$eduactivity = $this->params ()->fromPost ( 'eduactivity' );
		$eduprogram = $this->params ()->fromPost ( 'eduprogram' );
		
		$count = count ( $eduprogram );
		
		if($count > 0) {
			$starcount++;
		}
		
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['educationPreProgm'] [$i] = array (
					"programName" => $eduprogram [$i],
					"descriptionOfActivity" => $eduactivity [$i],
					"titleOrRole" => $edurole [$i],
					"yearsInvloved" => $eduyears [$i],
					"timeSpent" => $eduhrs [$i] . "/" . $eduweeks [$i] 
			);
		}
		
		// Service
		
		$servweeks = $this->params ()->fromPost ( 'servweeks' );
		$servhrs = $this->params ()->fromPost ( 'servhrs' );
		$servyear = $this->params ()->fromPost ( 'servyear' );
		$servrole = $this->params ()->fromPost ( 'servrole' );
		$servdesc = $this->params ()->fromPost ( 'servdesc' );
		$servname = $this->params ()->fromPost ( 'servname' );
		
		$count = count ( $servname );
		
		if($count > 0) {
			$starcount++;
		}
		
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['volunteer'] [$i] = array (
					"organizatioName" => $servname [$i],
					"description" => $servdesc [$i],
					"titleOrRole" => $servrole [$i],
					"yearsInvolved" => $servyear [$i],
					"timeSpent" => $servhrs [$i] . "/" . $servweeks [$i] 
			);
		}
		
		// Work
		
		$workname = $this->params ()->fromPost ( 'workname' );
		$workdesc = $this->params ()->fromPost ( 'workdesc' );
		$workrole = $this->params ()->fromPost ( 'workrole' );
		$workyears = $this->params ()->fromPost ( 'workyears' );
		$worksummer = $this->params ()->fromPost ( 'worksummer' );
		$workschoolyear = $this->params ()->fromPost ( 'workschoolyear' );
		$workstartmonth = $this->params ()->fromPost ( 'workstartmonth' );
		$workstartyear = $this->params ()->fromPost ( 'workstartyear' );
		$workendmonth = $this->params ()->fromPost ( 'workendmonth' );
		$workendyear = $this->params ()->fromPost ( 'workendyear' );
		
		$count = count ( $workname );
		if($count > 0) {
			$starcount++;
		}
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['workExperience'] [$i] = array (
					"employerName" => $workname [$i],
					"description" => $workdesc [$i],
					"titleOrRole" => $workrole [$i],
					"yearsInvolved" => $workyears [$i],
					"timeSpentInSummer" => $worksummer [$i],
					"timeSpentDuringSchool" => $workschoolyear [$i],
					"startDate" => $workstartmonth [$i] . '/' . $workstartyear [$i],
					"endDate" => $workendmonth [$i] . '/' . $workendyear [$i] 
			);
		}
		
		// Extra
		
		$extraweeks = $this->params ()->fromPost ( 'extraweeks' );
		$extrahrs = $this->params ()->fromPost ( 'extrahrs' );
		$extrayear = $this->params ()->fromPost ( 'extrayear' );
		$extrarole = $this->params ()->fromPost ( 'extrarole' );
		$extradesc = $this->params ()->fromPost ( 'extradesc' );
		$extraname = $this->params ()->fromPost ( 'extraname' );
		
		$count = count ( $extraname );
		if($count > 0) {
			$starcount++;
		}
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['extracurricular'] [$i] = array (
					"activityName" => $extraname [$i],
					"description" => $extradesc [$i],
					"titleOrRole" => $extrarole [$i],
					"yearsInvloved" => $extrayear [$i],
					"timeSpent" => $extrahrs [$i] . "/" . $extraweeks [$i] 
			);
		}
		
		// Award
		$awardname = $this->params ()->fromPost ( 'awardname' );
		$awardlevel = $this->params ()->fromPost ( 'awarddesc' );
		$awarddesc = $this->params ()->fromPost ( 'awarddesc' );
		$awardtype = $this->params ()->fromPost ( 'awardtype' );
		$awarddate = $this->params ()->fromPost ( 'awarddate' );
		
		$count = count ( $awardname );
		if($count > 0) {
			$starcount++;
		}
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['awardsAndHonors'] [$i] = array (
					"awardName" => $awardname [$i],
					"level" => $awardlevel [$i],
					"description" => $awarddesc [$i],
					"type" => $awardtype [$i],
					"yearsInvloved" => $awarddate [$i],
					"timeSpent" => "0" . "/" . "0" 
			);
		}
		
		$courseweeks = $this->params ()->fromPost ( 'courseweeks' );
		$coursehrs = $this->params ()->fromPost ( 'coursehrs' );
		$courseyear = $this->params ()->fromPost ( 'courseyear' );
		$coursedesc = $this->params ()->fromPost ( 'coursedesc' );
		$coursename = $this->params ()->fromPost ( 'coursename' );
		
		$count = count ( $coursename );
		for($i = 0; $i < $count; $i ++) {
			$jsonRequest ['nonA2GCourses'] [$i] = array (
					"courseName" => $coursename [$i],
					"description" => $coursedesc [$i],
					"yearsInvloved" => $courseyear [$i],
					"timeSpent" => $coursehrs [$i] . "/" . $courseweeks [$i] 
			);
		}
		
		$mode = $this->params ()->fromPost ( 'mode' );
		$uid = $this->params ()->fromPost ( 'uid' );
		$jsonRequest ['starCount'] =$starcount;
		$planjson = new RESTJSONManager ();
		$jsonResponse = "";
		if ($mode == "update") {
			
			$url = Constants::PLAN_RESUME_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			$this->LogMessage ( json_encode ( $jsonRequest ) );
			$jsonResponse = $planjson->PlanJSONManager ( 'PUT', $url, json_encode ( $jsonRequest ), $uid );
			$this->LogMessage ( $jsonResponse );
		} elseif ($mode == "save") {
			$this->getPlanTable ()->updateactivities ( $uid );
			$url = Constants::PLAN_RESUME_BASE_URL;
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
				'request' => json_encode ( $jsonRequest ) 
		) );
		return $resultret;
	}
	function myprofileAction() {
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
			
			$plangpaflag = $plan->myprofile;
		}
		$result = array ();
		if ($plangpaflag == 1) {
			$r = new RESTJSONManager ();
			$url = Constants::PLAN_PROFILE_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			// echo $url;
			$result = $r->PlanJSONManager ( 'GET', $url, null, $uid );
			$view = new ViewModel ( array (
					"result" => json_decode ( $result->getBody (), true ),
					"planflag" => $plangpaflag,
					'uid' => $uid 
			) );
		} else {
			$view = new ViewModel ( array (
					"result" => $result,
					"planflag" => $plangpaflag,
					'uid' => $uid 
			) );
		}
		return $view;
	}
	function myprofileJSONAction() {
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		$this->layout ( "layout/user" );
		
		$jsonRequest = array ();
		$jsonRequest ['error'] = null;
		$jsonRequest ['errorCode'] = 0;
		$jsonRequest ['errorLevel'] = 0;
		$mode = $this->params ()->fromPost ( 'mode' );
		
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		$uid = $this->params ()->fromPost ( 'uid' );
		
		
		$jsonRequest ['notes'] = array (
				$this->params ()->fromPost ( 'note' ) 
		);
		$jsonRequest ['uid'] = $this->params ()->fromPost ( 'uid' );
		
		$jsonRequest ['parentGuardian'] [0] = array (
				"PGFirstName" => $this->params ()->fromPost ( 'pgfname1' ),
				"PGMiddleName" => $this->params ()->fromPost ( 'pgmname1' ),
				"PGLastName" => $this->params ()->fromPost ( 'pglname1' ),
				"PGStreetAddress" => $this->params ()->fromPost ( 'pgaddress1' ),
				"PGCity" => $this->params ()->fromPost ( 'pgcity1' ),
				"PGState" => $this->params ()->fromPost ( 'pgstate1' ),
				"PGZipcode" => $this->params ()->fromPost ( 'pgzip1' ),
				"PGCounty" => $this->params ()->fromPost ( 'pgcounty1' ),
				"PGPhone" => $this->params ()->fromPost ( 'pgtelephone1' ),
				"PGMobilePhone" => $this->params ()->fromPost ( 'pgcell1' ),
				"PGEmail" => $this->params ()->fromPost ( 'pgemail1' ),
				"PGEmailUcEazy" => $this->params ()->fromPost ( 'isemailuceazy1' ),
				"PGEmailUCCSU" => $this->params ()->fromPost ( 'isemailucsu1' ) 
		)
		;
		
		$jsonRequest ['parentGuardian'] [1] = array (
				"PGFirstName" => $this->params ()->fromPost ( 'pgfname2' ),
				"PGMiddleName" => $this->params ()->fromPost ( 'pgmname2' ),
				"PGLastName" => $this->params ()->fromPost ( 'pglname2' ),
				"PGStreetAddress" => $this->params ()->fromPost ( 'pgaddress2' ),
				"PGCity" => $this->params ()->fromPost ( 'pgcity2' ),
				"PGState" => $this->params ()->fromPost ( 'pgstate2' ),
				"PGZipcode" => $this->params ()->fromPost ( 'pgzip2' ),
				"PGCounty" => $this->params ()->fromPost ( 'pgcounty2' ),
				"PGPhone" => $this->params ()->fromPost ( 'pgtelephone2' ),
				"PGMobilePhone" => $this->params ()->fromPost ( 'pgcell2' ),
				"PGEmail" => $this->params ()->fromPost ( 'pgemail2' ),
				"PGEmailUcEazy" => $this->params ()->fromPost ( 'isemailuceazy2' ),
				"PGEmailUCCSU" => $this->params ()->fromPost ( 'isemailucsu2' ) 
		)
		;
		
		$jsonRequest ['state'] = $this->params ()->fromPost ( 'state' );
		$jsonRequest ['suffix'] = $this->params ()->fromPost ( 'suffix' );
		$jsonRequest ['email'] = $this->params ()->fromPost ( 'email' );
		$jsonRequest ['city'] = $this->params ()->fromPost ( 'city' );
		$jsonRequest ['firstName'] = $this->params ()->fromPost ( 'fname' );
		$jsonRequest ['middleName'] = $this->params ()->fromPost ( 'mname' );
		$jsonRequest ['lastName'] = $this->params ()->fromPost ( 'lname' );
		$jsonRequest ['streetAddress'] = $this->params ()->fromPost ( 'street' );
		$jsonRequest ['zipcode'] = $this->params ()->fromPost ( 'zipcode' );
		$jsonRequest ['county'] = $this->params ()->fromPost ( 'county' );
		$jsonRequest ['phone'] = $this->params ()->fromPost ( 'telephone' );
		$jsonRequest ['mobilePhone'] = $this->params ()->fromPost ( 'cell' );
		$jsonRequest ['birthday'] = $this->params ()->fromPost ( 'birthdate' );
		$jsonRequest ['sex'] = $this->params ()->fromPost ( 'gendar' );
		
		$jsonRequest ['ethnicity'] = $this->params ()->fromPost ( 'ethnicity' );
		
		if ($this->params ()->fromPost ( 'ethnicityname' ) == "999") {
			$jsonRequest ['ethnicityOthers'] = $this->params ()->fromPost ( 'ethnicitynameothers' );
		} else {
			$jsonRequest ['ethnicityOthers'] = Convertors::getEthnicityVal ( $this->params ()->fromPost ( 'ethnicityname' ) );
		}
		
		$starCount=0;
		
		
		if($this->params ()->fromPost ( 'email' )!='' && 
		
		$this->params ()->fromPost ( 'fname' )!='' && 
		$this->params ()->fromPost ( 'mname' )!='' && 
		$this->params ()->fromPost ( 'lname' )!='' && 
		
		$this->params ()->fromPost ( 'cell' )!='' 
		){
			$starCount++;
		}
		
		
		
		 if($this->params ()->fromPost ( 'pgfname1' ) !='' && 
			 $this->params ()->fromPost ( 'pgmname1' ) !='' && 
			 $this->params ()->fromPost ( 'pglname1' ) !='' && 
			$this->params ()->fromPost ( 'pgcell1' )!='' && 
		 $this->params ()->fromPost ( 'pgemail1' )!='' 
		){
		 	$starCount++;
		 }
		 
	
		 $jsonRequest ['starCount']=$starCount;
		
		$jsonRequest ['race'] = $this->params ()->fromPost ( 'race' );
		$jsonRequest ['raceCategory'] = $this->params ()->fromPost ( 'racecategory' );
		
		
		$planjson = new RESTJSONManager ();
		$url="";
		$jsonResponse = "";
		if ($mode == "update") {
			
			$url = Constants::PLAN_PROFILE_BASE_URL;
			$url = str_replace ( "<UID>", $uid, $url );
			$this->LogMessage ( json_encode ( $jsonRequest ) );
			$jsonResponse = $planjson->PlanJSONManager ( 'PUT', $url, json_encode ( $jsonRequest ), $uid );
			$this->LogMessage ( $jsonResponse );
		} elseif ($mode == "save") {
			$this->getPlanTable ()->updateMyProfile ( $uid );
			$url = Constants::PLAN_PROFILE_BASE_URL;
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
				'request' => json_encode ( $jsonRequest ) 
		) );
		return $resultret;
	}
	function myuceazyJSONAction() {
		if (! isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			return $this->redirect ()->toUrl ( '/' );
		}
		
		$this->layout ( "layout/user" );
		
		$mode = $this->params ()->fromPost ( 'mode' );
		$uid = $this->params ()->fromPost ( 'uid' );
		$jsonRequest = array ();
		$jsonRequest ['notes'] = array (
				$this->params ()->fromPost ( 'note' ) 
		);
		$jsonRequest ['uid'] = $this->params ()->fromPost ( 'uid' );
		$jsonRequest ['dreamSchool'] = $this->params ()->fromPost ( 'dreamschool' );
		
		$planjson = new RESTJSONManager ();
		
		$jsonResponse = "";
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
		
		$resultret = new JsonModel ( array (
				'response' => json_decode ( $jsonResponse->getBody (), true ),
				'error' => $error,
				'ecode' => $ecode,
				'request' => json_encode ( $jsonRequest ) 
		) );
		return $resultret;
	}
	function myuceazyAction() {
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
			
			$plangpaflag = $plan->mycounselor;
		}
		$result = array ();
		
		$notes = "";
		$dreamschool = $this->params ()->fromQuery ( 'dreamschool' );
		$r = new RESTJSONManager ();
		
		$tmpSchoolName = str_replace ( " ", "%20", $dreamschool );
		// $dreamschool=$tmpSchoolName;
		if ($dreamschool != null) {
		} else {
			
			if ($plangpaflag == 1) {
				
				$url = Constants::PLAN_MYUCEAZY_BASE_URL;
				$url = str_replace ( "<UID>", $uid, $url );
				$dreamresult = $r->PlanJSONManager ( 'GET', $url, null, $uid );
				$decodeResponse = json_decode ( $dreamresult->getBody (), true );
				$dreamschool = $decodeResponse ['dreamSchool'];
				$notes=$decodeResponse ['notes'];;
				
				$tmpSchoolName = str_replace ( " ", "%20", $dreamschool );
			}
		}
		$decodeResponse = "";
		// Get School information
		
		$url = Constants::PLAN_SCHOOL_INFO_URL;
		$url = str_replace ( "<SCHOOL>", $tmpSchoolName, $url );
		
		// echo $url;
		$dreamresult = $r->PlanJSONManager ( 'GET', $url, null, $uid );
		$schoolInfo = json_decode ( $dreamresult->getBody (), true );
		
		// var_dump($schoolInfo);
		
		$decodeResponse = "";
		$url = Constants::PLAN_MYUCEAZY_STUINFO;
		$url = str_replace ( "<UID>", $uid, $url );
		$result = $r->PlanJSONManager ( 'GET', $url, null, $uid );
		$yourGPA = $result->getBody ();
		
		$url = Constants::PLAN_GET_MAJOR;
		$url = str_replace ( "<UID>", $uid, $url );
		$result = $r->PlanJSONManager ( 'GET', $url, null, $uid );
		$decodeResponse = json_decode ( $result->getBody (), true );
		$major = $decodeResponse ['major'];
		
		$url = Constants::PLAN_MYUCEAZY_ELIGIBILITY;
		$url = str_replace ( "<UID>", $uid, $url );
		$jsonResponse = $r->PlanJSONManager ( 'GET', $url, null, '' );
		$cols = json_decode ( $jsonResponse->getBody (), true );
		
		$eligibleCourse = "";
		$url = Constants::PLAN_UCEAZY_ELIGIBLE_COURSE;
		$url = str_replace ( "<UID>", $uid, $url );
		$jsonResponse = $r->PlanJSONManager ( 'GET', $url, null, '' );
		$eligibleCourse = json_decode ( $jsonResponse->getBody (), true );
		
		$competitiveCourse = "";
		$url = Constants::PLAN_UCEAZY_COMPETITIVE_COURSES;
		$url = str_replace ( "<UID>", $uid, $url );
		
		$jsonResponse = $r->PlanJSONManager ( 'GET', $url, null, '' );
		
		$competitiveCourse = json_decode ( $jsonResponse->getBody (), true );
		
		$url = Constants::PLAN_UCEAZY_BEST_STD_TESTS;
		$url = str_replace ( "<UID>", $uid, $url );
		$jsonResponse = $r->PlanJSONManager ( 'GET', $url, null, '' );
		$bestScores = json_decode ( $jsonResponse->getBody (), true );
		$notification_data=$this->getNotifyData("all",$uid);
		$view = new ViewModel ( array (
				"competitiveCourse" => $competitiveCourse,
				"bestScores" => $bestScores,
				"dreamschool" => $dreamschool,
				'schoolinfo' => $schoolInfo,
				'eligibleCourse' => $eligibleCourse,
				'yourgpa' => $yourGPA,
				'major' => $major,
				'cols' => $cols,
				"planflag" => $plangpaflag,
				'uid' => $uid,
				'note'=>isset($notes[0])?$notes[0]:"",
				'notification_data'=>$notification_data				
		) );
		
		return $view;
	}
	
	
	public function getNotifyData($flag,$uid){
		
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$flag=trim ($flag);
		$uid=trim ($uid);		
		if($flag=='cnt'){
			$stmt = $adapter->createStatement("SELECT count(user_notification_history.user_id) as cnt
             FROM notification_master LEFT JOIN user_notification_history ON notification_master.notify_id=user_notification_history.notify_id where notification_master.end_date >= CURDATE()
             and user_notification_history.is_read='no' and user_notification_history.user_id=$uid");
		}if($flag=="all"){

			$stmt = $adapter->createStatement("SELECT notification_master.*, user_notification_history.*
                 FROM notification_master LEFT JOIN user_notification_history ON notification_master.notify_id=user_notification_history.notify_id where notification_master.end_date >= CURDATE()
                 and user_notification_history.user_id=$uid order by user_notification_history.is_read");

		}
		$stmt->prepare();
		$result = $stmt->execute();
		$resultSet = new ResultSet();
		$resultSet->initialize($result);
		$data=$resultSet->toArray();
		if($flag=="all"){
		foreach($data as $key => $value)
		{
		  $data[$key]['start_date'] = $this->Timesince($value['start_date']);
		}
		}
		return $data;
	}
	
	
function Timesince($original)
{
  $ret_txt  = '';
  $dateobj1 = new \DateTime();
  $now      = $dateobj1->format('Y-m-d H:i:s');
  
  $dateobj2 = new \DateTime($original);
  $past     = $dateobj2->format('Y-m-d H:i:s');
  $date1    = new \DateTime($past);
  $date2    = new \DateTime($now);
  $interval = $date1->diff($date2);
  if ($interval->y > 0) {    
	 $ret_txt = '<small class="label label-default"><i class="fa fa-clock-o"></i>'. $interval->y . '&nbsp;year</small>';
  } else if ($interval->m > 0) {
    if ($interval->m == 1)      
	 $ret_txt = '<small class="label label-default"><i class="fa fa-clock-o"></i>'. $interval->m . '&nbsp;month</small>';
    if ($interval->m > 1)      
	 $ret_txt = '<small class="label label-default"><i class="fa fa-clock-o"></i>'. $interval->m . '&nbsp;months</small>';	
  } else if ($interval->d > 0) {
    $day = $interval->d;
    if ($day > 0 && $day <= 6) {     
	  $ret_txt = '<small class="label label-warning"><i class="fa fa-clock-o"></i>'. $day . '&nbsp;day</small>';
    }
    if ($day >= 7) {
      $week = round($day / 7);
      if ($week == 0)
        $ret_txt = ' 1&nbsp;Week ';
		 $ret_txt = '<small class="label label-primary"><i class="fa fa-clock-o"></i> 1&nbsp;week</small>';
      if ($week >= 1)      
		 $ret_txt = '<small class="label label-primary"><i class="fa fa-clock-o"></i>'. $week . '&nbsp;weeks</small>';
    }

  } else if ($interval->d == 0 && $interval->h > 0) {    
	$ret_txt = '<small class="label label-info"><i class="fa fa-clock-o"></i>'. $interval->h . '&nbsp;hours</small>';
  } else if ($interval->i >= 0) {
    $ret_txt = '<small class="label label-primary"><i class="fa fa-clock-o"></i>'. $interval->i . '&nbsp;mins</small>';
	
  }
  return $ret_txt;
}
	
}