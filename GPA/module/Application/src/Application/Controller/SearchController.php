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
use Common\Utilities\Convertors;
use Zend\View\Model\JsonModel;
use Common\API\RESTJSONManager;
class SearchController extends AbstractActionController
{
	protected $userTable;
	protected $seachcriteriaTable;
	protected $savedsearchresultTable;
	protected $shareresultTable;
	public function getUserTable()
	{
		if (!$this->userTable) {
			$sm = $this->getServiceLocator();
			$this->userTable = $sm->get('UserService');
		}
		return $this->userTable;
	}
	
	public function getShareResultTable()
	{
		if (!$this->userTable) {
			$sm = $this->getServiceLocator();
			$this->userTable = $sm->get('ShareresultService');
		}
		return $this->userTable;
	}
	public function getSeachcriteriaTable()
	{
		if (!$this->seachcriteriaTable) {
			$sm = $this->getServiceLocator();
			$this->seachcriteriaTable = $sm->get('SearchCriteriaService');
		}
		return $this->seachcriteriaTable;
	}
	
	public function getSavedSearchResultTable()
	{
		if (!$this->savedsearchresultTable) {
			$sm = $this->getServiceLocator();
			$this->savedsearchresultTable = $sm->get('SavedSearchResultService');
		}
		return $this->savedsearchresultTable;
	}
	
	
	
	
	
	
	
	function shareresultallAction(){
		$request = $this->getRequest();
		if ($request->isPost()) {
			$tmpsession = new Container('tmp');
			
			$list = $this->params()->fromPost('collegelist');
		
			$explorerForm=$tmpsession->exploreForm;
			$em = new EmailManager();
			$email = $this->params()->fromPost('shareemail');
			$this->getShareResultTable()->insertRow($email,$explorerForm->getsearchType());
			$em->sendResultsALL($explorerForm,$email,$list);
				
			$resultret = new JsonModel(array(
					'success' => "Your search result has been sent to given email address",
			));
			return $resultret;
		}
	}
	
	function shareresultAction(){
		$request = $this->getRequest();
		if ($request->isPost()) {
			$tmpsession = new Container('tmp');
			$button= $this->params()->fromPost('saveresult');
			$sendemail = $this->params()->fromPost('sendmail');
			$explorerForm=$tmpsession->exploreForm;
			$em = new EmailManager();
			$email = $this->params()->fromPost('shareemail');
			$this->getShareResultTable()->insertRow($email,$explorerForm->getsearchType());
			$em->sendResults($explorerForm,$email);
			
			$resultret = new JsonModel(array(
						'success' => "Your search result has been sent to given email address",
				));
			return $resultret;
		}
	}
	
	
	function saveadvanceAction(){
			$session = new Container('user');
			$tmpsession = new Container('tmp');
			$username= $session->username;
			$userid=$session->userid;
			if($username != ""){
				$explorerForm=$tmpsession->exploreForm;
				$id=$this->getSeachcriteriaTable()->insertAdvanceRow($userid,$explorerForm->getExam(),
						$explorerForm->getGpa(),$explorerForm->getAct(),$explorerForm->getActew(),$explorerForm->getsatread(),$explorerForm->getsatmath(),$explorerForm->getsatmath(),
						$explorerForm->getpsatread(),$explorerForm->getpsatmath(),$explorerForm->getpsatwrite(),$explorerForm->getCounty(),$explorerForm->getsearchType(),
						$explorerForm->getus(),$explorerForm->getfreelunch(),$explorerForm->getNoca(),$explorerForm->getMajor(),$explorerForm->getcsize(),$explorerForm->getcpref()
				);
				//safety
				$safety = $explorerForm->getSafety();
				foreach ($safety as $row){
					$this->getSavedSearchResultTable()->insertAdvanceRow($id,Constants::SEARCH_RESULT_SAFETY,$row);
				}
				//Reach
				$reach = $explorerForm->getReach();
				foreach ($reach as $row){
					$this->getSavedSearchResultTable()->insertAdvanceRow($id,Constants::SEARCH_RESULT_REACH,$row);
				}
				//Target
				$taget = $explorerForm->getTarget();
				foreach ($taget as $row){
					$this->getSavedSearchResultTable()->insertAdvanceRow($id,Constants::SEARCH_RESULT_TARGET,$row);
				}
				$resultret = new JsonModel(array(
						'success' => "Your search result has been saved.",
				));
				return $resultret;
			}
			
	}
	
	// Currently this function disabled.
	function savesimpleAction(){
			$session = new Container('user');
			$tmpsession = new Container('tmp');
			$username= $session->username;
			$userid=$session->userid;
			if($username != ""){
				$location="";
				$explorerForm=$tmpsession->exploreForm;
				$id=$this->getSeachcriteriaTable()->insertRow($userid,$explorerForm->getExam(),
						$explorerForm->getGpa(),$explorerForm->getAct(),$explorerForm->getActew(),$explorerForm->getsatread(),$explorerForm->getsatmath(),$explorerForm->getsatmath(),
						$explorerForm->getpsatread(),$explorerForm->getpsatmath(),$explorerForm->getpsatwrite(),$location);
				//safety
				$safety = $explorerForm->getSafety();
				foreach ($safety as $row){
					$this->getSavedSearchResultTable()->insertRow($id,Constants::SEARCH_RESULT_SAFETY,$row);
				}
				//Reach
				$reach = $explorerForm->getReach();
				foreach ($reach as $row){
					$this->getSavedSearchResultTable()->insertRow($id,Constants::SEARCH_RESULT_REACH,$row);
				}
				//Target
				$taget = $explorerForm->getTarget();
				foreach ($taget as $row){
					$this->getSavedSearchResultTable()->insertRow($id,Constants::SEARCH_RESULT_TARGET,$row);
				}
				$tmpsession->exploreForm=null;
				$resultret = new JsonModel(array(
						'success' => "Your search result has been saved.",
				));
				return $resultret;
			}else{
				$resultret = new JsonModel(array(
						'error' => "You are not loggin.Please login and save the result",
				));
				return $resultret;
			}
		
	}
	
	function eazyAction(){
		
		$this->layout('layout/empty');
		$apiManager = new RESTJSONManager ();
		$url = Constants::ALL_SCHOOLS;
		$result = $apiManager->PlanJSONManager ( "GET", $url, null, null );
		$view = new ViewModel(array("result"=>json_decode($result->getBody(),true),"allschool"=>$result->getBody()));
		return $view; 
		
	}
	function simpleAction(){
		$actew="";
		$act="";
		$gpa="";
		$exam="";
		$satcrm="";
		$satwrt="";
		$satread="";
		$satmath="";
		$satwrite="";
		$psatread="";
		$psatmath="";
		$psatwrite="";
		
		$tmpsession = new Container('tmp');
		$session = new Container('user');
		$email="";
		$explorerForm = new ExplorerForm();
		$explorerForm->setExam("act");
		$explorerForm->setGpa("3.46");
		$explorerForm->setState("cal");
		$explorerForm->setEmail($session->username);
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$county=$this->params()->fromPost('county');
			$state=$this->params()->fromPost('state');
			$explorerForm->setCounty($county);
			$explorerForm->setState($state);
			
			
			if($state==""){
				$result = new JsonModel(array(
						'error' => "Please select the state of Residence.",
				));
				return $result;
			}
			
			if($state=="CA" and $county=="-1"){
				$result = new JsonModel(array(
						'error' => "Please select the California County",
				));
				return $result;
			}
			
			$school=$this->params()->fromPost('school');
			if($school==""||$school==null){
				$result = new JsonModel(array(
						'error' => "Please enter valid school name",
				));
				return $result;
			}
			
			$act= $this->params()->fromPost('act');
			$actew = $this->params()->fromPost('actew');
			$gpa= $this->params()->fromPost('gpa');
			$exam=$this->params()->fromPost('exam');
			if($exam==null){
				$result = new JsonModel(array(
						'error' => "Please select an exam.",
				));
				return $result;
			}
			$gpa=substr($gpa, 0,4);
			$result=array();
			$rm = new RESTManager();

			if($exam == 'act'){
				$result =$rm->getACTResult($gpa,$act,$actew,$county,$state);
			}elseif($exam == 'sat' ){
				$satread=$this->params()->fromPost('satread');
				$satmath=$this->params()->fromPost('satmath');
				$satwrite=$this->params()->fromPost('satwrite');
				$satcrm = $satread + $satmath;
				$satwrt = $satwrite;
				$result =$rm->getSATResult($gpa,$satcrm,$satwrt,$county,$state);
			}elseif($exam== 'psat'){
				$psatread=$this->params()->fromPost('psatread');
				$psatmath=$this->params()->fromPost('psatmath');
				$psatwrite=$this->params()->fromPost('psatwrite');
				$psatcrm = $psatread + $psatmath;
				$psatwrt = $psatwrite;
				$result =$rm->getPSATResult($gpa,$psatcrm,$psatwrt,$county,$state);
			}
				
			// Setting tthe value
			$explorerForm->setAct($act);
			$explorerForm->setActew($actew);
			$explorerForm->setExam($exam);
			$explorerForm->setGpa($gpa);
				
			$explorerForm->setsatread($satread);
			$explorerForm->setsatmath($satmath);
			$explorerForm->setsatwrite($satwrite);
				
			$explorerForm->setpsatread($psatread);
			$explorerForm->setpsatmath($psatmath);
			$explorerForm->setpsatwrite($psatwrite);
		
			if(isset($result['exception'])){
				$explorerForm->setisResult(false);
				$this->LogMessage($result['url']);
				$resultret = new JsonModel(array(
						'error' => "API Error",
				));
				return $resultret;
			}elseif(count($result['reach'])==0 && count($result['target'])==0 && count($result['safety'])==0){
				$explorerForm->setisResult(false);
				$resultret = new JsonModel(array(
						'error' => "Based on some of your search criteria, UCEazy isn't able to match you with UC or CSU colleges at this time.  If you are interested, enrolling into a community college and transferring to a UC/CSU might be a great alternative.",
				));
				return $resultret;
			}else{
				if($result['reach']==null&&$result['target']==null&&$result['safety']==null){
					$explorerForm->setisResult(false);
					$resultret = new JsonModel(array(
							'error' => "Based on some of your search criteria, UCEazy isn't able to match you with UC or CSU colleges at this time.  If you are interested, enrolling into a community college and transferring to a UC/CSU might be a great alternative.",
					));
					return $resultret;
			}
			$explorerForm->setisResult(true);
			$explorerForm->setReach($result['reach']);
			$explorerForm->setTarget($result['target']);
			$explorerForm->setSafety($result['safety']);
			unset($result['error']);
			$tmpsession->exploreForm=$explorerForm;
			if($result['reach']==null){
				$result['reach']=array("No College Found");
			}
			if($result['target']==null){
				$result['target']=array("No College Found");
			}
			if($result['safety']==null){
				$result['safety']=array("No College Found");
			}
				
			$tmp = array("Reach"=>$result['reach'],"Target"=>$result['target'],"Safety"=>$result['safety']);
			
			$json = json_encode($tmp);
			$explorerForm->setJson($json);
				
			$result = new JsonModel(array(
					'success' => "Here you go. System found some matches for your search",
					'data'=>$json
			));
				return $result;
			}
		}
		
		$result = new JsonModel(array(
				'success' => "Here you go. System found some matches for your search",
		));
		return $result;
	}
	
	function LogMessage($message){
		if(Constants::IS_LOG){
			$logger = new \Zend\Log\Logger();
			$writer = new \Zend\Log\Writer\Stream(Constants::LOG_FILE);
			$logger->addWriter($writer);
			$logger->info($message);
		}
	}
	
	function advexplorerAction(){
		$this->layout ( "layout/user" );
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}

		$apiManager = new RESTJSONManager ();
		$url = Constants::ALL_SCHOOLS;
		$result = $apiManager->PlanJSONManager ( "GET", $url, null, $uid );
		$view = new ViewModel(array("result"=>json_decode($result->getBody(),true),"allschool"=>$result->getBody()));
		//$view = new ViewModel(array("result"=>json_decode(array())));
		return $view;
		
	}
	
	function advanceAction(){
		$session = new Container('user');
		$username= $session->username;
		$userid=$session->userid;
		if($username == "" or $username==null ){
		}
		
		$actew="";
		$act="";
		$gpa="";
		$exam="";
		$satcrm="";
		$satwrt="";
		$satread="";
		$satmath="";
		$satwrite="";
		$psatread="";
		$psatmath="";
		$psatwrite="";
		$noca="";
		$major="";
		$freelunch="";
		$csize="";
		$cpref="";
		$us="";

		$tmpsession = new Container('tmp');
		$explorerForm = new ExplorerForm();
		$explorerForm->setsearchType("A");
		$explorerForm->setExam("act");
		$explorerForm->setGpa("2.50");
		$explorerForm->setState("cal");
		$explorerForm->setNoca("2");
		$explorerForm->setEmail($session->username);
		$request = $this->getRequest();

		if ($request->isPost()) {
		
		$button= $this->params()->fromPost('saveresult');
		$sendemail = $this->params()->fromPost('sendmail');
		
		$schoolname= $this->params()->fromPost('schoolname');
		
		
		
		$county=$this->params()->fromPost('county');
		$state=$this->params()->fromPost('state');
		$explorerForm->setCounty($county);
		$explorerForm->setState($state);
		$act= $this->params()->fromPost('act');
		$actew = $this->params()->fromPost('actew');
		$gpa= $this->params()->fromPost('gpa');
		$gpa=substr($gpa, 0,4);
		$exam=$this->params()->fromPost('exam');
		$noca=$this->params()->fromPost('noca');
		$nocaval=$this->params()->fromPost('nocaval');
		$major=$this->params()->fromPost('major');
		$freelunch=$this->params()->fromPost('freelunch');
		$csize=$this->params()->fromPost('csize');
		$cpref=$this->params()->fromPost('cpref');
		$us=$this->params()->fromPost('us');
		$satread=$this->params()->fromPost('satread');
		$satmath=$this->params()->fromPost('satmath');
		$satwrite=$this->params()->fromPost('satwrite');
		$psatread=$this->params()->fromPost('psatread');
		$psatmath=$this->params()->fromPost('psatmath');
		$psatwrite=$this->params()->fromPost('psatwrite');
		$explorerForm->setNoca($noca);
		$explorerForm->setMajor($major);
		$explorerForm->setfreelunch($freelunch);
		$explorerForm->setcsize($csize);
		$explorerForm->setcpref($cpref);
		$explorerForm->setus($us);
		if($nocaval==null or $nocaval==""){
			$explorerForm->setNocaval(0);
		}else{
		$explorerForm->setNocaval($nocaval);
		}
		// Setting tthe value
		$explorerForm->setAct($act);
		$explorerForm->setActew($actew);
		$explorerForm->setGpa($gpa);
		$explorerForm->setsatread($satread);
		$explorerForm->setsatmath($satmath);
		$explorerForm->setsatwrite($satwrite);
		$explorerForm->setpsatread($psatread);
		$explorerForm->setpsatmath($psatmath);
		$explorerForm->setpsatwrite($psatwrite);
		if($cpref=="A"){
			$cpref=null;
		}
		if($csize=="A"){
			$csize=null;
		}
		$explorerForm->setExam($exam);
		
		$mode=$this->params()->fromPost('mode');
		$pref="";
		// Show = 1, Exp =0
		if($mode=='show'){
			$pref="1";
		}elseif ($mode=="exp"){
			$pref="0";
		}
		
		if($state==null){
			$resultret = new JsonModel(array('error' => "Please select a current state",));
			return $resultret;
		}
		
		if($state=='CA' && $county=='Select'){
			$resultret = new JsonModel(array('error' => "Please select a county",));
			return $resultret;
		}
		
		if($schoolname==""){
			$resultret = new JsonModel(array('error' => "Please enter a valid school name",));
			return $resultret;
		}
		
		if($exam==null){
			$resultret = new JsonModel(array('error' => "Please select a Standardized Test",));
			return $resultret;
		}
		
		if($us==null){
			$resultret = new JsonModel(array(
					'error' => "Please select an university system",
			));
			return $resultret;
		}
		
		$rm = new RESTManager();
		
		$all = array();
		
		$result = "";
		if($exam == 'act'){
			if($us=='UC'){
					$result =$rm->getUCACTResult($gpa,$act,$actew,$county,$state,$freelunch,$us,$major,$csize,$cpref,$pref,$schoolname);
				
			}elseif($us=='CSU'){
				$result =$rm->getCSUACTResult($gpa,$act,$actew,$county,$state,$freelunch,$us,$major,$csize,$cpref,$pref,$schoolname);
			}else{
					$result =$rm->getBOTHACTResult($gpa,$act,$actew,$county,$state,$freelunch,$us,$major,$csize,$cpref,$pref,$schoolname);
			}
		}elseif($exam == 'sat' ){			
			
			$satcrm = $satread + $satmath;
			$satwrt = $satwrite;
			if($us=='CSU'){
				$result =$rm->getCSUSATResult($gpa,$satcrm,$satwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$pref,$schoolname);
			}elseif($us=='UC'){
				$result =$rm->getUCSATResult($gpa,$satcrm,$satwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$pref,$schoolname);
			}else{
				$result =$rm->getBOTHSATResult($gpa,$satcrm,$satwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$pref,$schoolname);
			}
		}elseif($exam== 'psat'){
				
				$psatcrm = $psatread + $psatmath;
				$psatwrt = $psatwrite;
				if($us=='UC'){
				$result =$rm->getUCPSATResult($gpa,$psatcrm,$psatwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$pref,$schoolname);
				}elseif($us=='CSU'){
				$result =$rm->getCSUPSATResult($gpa,$psatcrm,$psatwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$pref,$schoolname);
				}else{
					$result =$rm->getBOTHPSATResult($gpa,$psatcrm,$psatwrt,$county,$state,$freelunch,$us,$major,$csize,$cpref,$pref,$schoolname);
				}
			}
			
			
		//return new JsonModel(array("re"=>$result));
		
			
		if(isset($result['exception'])){
			
			$view  = new JsonModel(array('error'=>$result['exception']));
			return $view;

		}elseif(count($result['reach'])==0 && count($result['target'])==0 && count($result['safety'])==0){
			$explorerForm->setisResult(false);
			$view  = new JsonModel(array('error'=>'Based on some of your search criteria, UCEazy isn\'t able to match you with UC or CSU colleges at this time.  You might want to try expanding some of your preferences. Alternatively, there may be no UC or CSU college that fits your specific profile and preferences.  If that is the case, enrolling into a community college and transferring to a 4-year college might be a great alternative.'));
			return $view;
		}else{
			if($result['reach']==null&&$result['target']==null&&$result['safety']==null){
				$explorerForm->setisResult(false);
			$view  = new JsonModel(array('error'=>'Based on some of your search criteria, UCEazy isn\'t able to match you with UC or CSU colleges at this time.  You might want to try expanding some of your preferences. Alternatively, there may be no UC or CSU college that fits your specific profile and preferences.  If that is the case, enrolling into a community college and transferring to a 4-year college might be a great alternative.'));
			return $view;
		}
		$explorerForm->setisResult(true);
		$explorerForm->setReach($result['reach']);
		$explorerForm->setTarget($result['target']);
		$explorerForm->setSafety($result['safety']);
		$tmpsession->exploreForm=$explorerForm;
		unset($result['error']);
		if($result['reach']==null){
			$result['reach']=array("No College Found");
		}
		if($result['target']==null){
			$result['target']=array("No College Found");
		}
		if($result['safety']==null){
			$result['safety']=array("No College Found");
		}
		
		
		
		$tmp = array("Reach"=>$result['reach'],"Target"=>$result['target'],"Safety"=>$result['safety']);
		$json = json_encode($tmp);
		$result = new JsonModel(array(
				'success' => "Here you go. System found some matches for your search",
				'res'=>$json,
				
		));
		return $result;
		}
		}
		
		
		
	}
	

}