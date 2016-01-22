<?php
namespace Common\ActionForms;

class ExplorerForm{
	
	public $searchType="";
	
	function setsearchType($searchType){
		$this->searchType=$searchType;
	}

	function getsearchType(){
		return $this->searchType;
	}
	
	public $json;
	
	function setJson($json){
		$this->json=$json;
	}
	
	function getJson(){
		return $this->json;
	}
	
	
	public $exam;
	public $act="1";
	public $actew="1";
	public $gpa;
	
	public $county;
	public $state;
	
	public $satcrm="";
	public $satwrt="";
	
	public $nocaval=0;
	
	
	public $email="";
	
	public $isResult=false;
	public $reach = array();
	public $target = array();
	public $safety = array();
	

	public $satread="200";
	public $satmath="200";
	public $satwrite="200";
	
	
	public $psatread="20";
	public $psatmath="20";
	public $psatwrite="20";

	// Added for Advance search
	public $noca;
	public $major;
	public $freelunch;
	public $csize;
	public $cpref;
	public $us;
	
	public function setus($us){
		$this->us=$us;
	}
	
	public function getus(){
		return $this->us;
	}
	
	public function setcpref($cpref){
		$this->cpref=$cpref;
	}
	
	public function getcpref(){
		return $this->cpref;
	}
	
	public function setcsize($csize){
		$this->csize=$csize;
	}
	
	public function getcsize(){
		return $this->csize;
	}
	
	public function setfreelunch($freelunch){
		$this->freelunch=$freelunch;
	}
	
	public function getfreelunch(){
		return $this->freelunch;
	}
	
	public function setMajor($major){
		$this->major=$major;
	}
	
	public function getMajor(){
		return $this->major;
	}
	
	public function setNoca($noca){
		$this->noca=$noca;
	}
	
	public function getNoca(){
		return $this->noca;
	}
	
	public function setNocaval($nocaval){
		$this->nocaval=$nocaval;
	}
	
	public function getNocaval(){
		return $this->nocaval;
	}
	
	
	
	public function setCounty($county){
		$this->county=$county;
	}
	public function setState($state){
		$this->state=$state;
	}
	
	public function getCounty(){
		return $this->county;
	}
	public function getState(){
		return $this->state;
	}
	
	public function setEmail($email){
		
		$this->email=$email;
		
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function setsatread($satread){
	$this->satread=$satread;	
	}
	
	public function setsatmath($satmath){
		$this->satmath=$satmath;
	}
	
	public function setsatwrite($satwrite){
		$this->satwrite=$satwrite;
	}
	
	public function setpsatread($satread){
		$this->psatread=$satread;
	}
	
	public function setpsatmath($satmath){
		$this->psatmath=$satmath;
	}
	
	public function setpsatwrite($satwrite){
		$this->psatwrite=$satwrite;
	}
	
	
	public function getsatread(){
		return $this->satread;
	}
	
	public function getsatmath(){
		return $this->satmath;
	}
	
	public function getsatwrite(){
		return $this->satwrite;
	}
	
	

	public function getpsatread(){
		return $this->psatread;
	}
	
	public function getpsatmath(){
		return $this->psatmath;
	}
	
	public function getpsatwrite(){
		return $this->psatwrite;
	}
	
	
	
	public function setSatcrm($satcrm){
		$this->setSatcrm($satcrm);
		
	}

	public function setSatwrt($satwrt){
		$this->setSatwrt($satwrt);
	}
	
	public function getSatcrm(){
		return $this->getSatcrm();
	}
	
	public function getSatwrt(){
		return $this->getSatwrt();
	}
	
	
	public function setisResult($flag){
		$this->isResult=$flag;
	}
	
	public function getisResult(){
		return $this->isResult;
	}
	
	
	public function setReach($reach){
		$this->reach = $reach;
	}
	public function setTarget($target){
		$this->target = $target;
	}
	public function setSafety($safety){
		$this->safety = $safety;
	}
	
	public  function getReach(){
	return $this->reach;
	}
	public function getTarget(){
	return $this->target;
	}
	public function getSafety(){
	return $this->safety;
	}
	
	public function setExam($exam){
		$this->exam=$exam;
	}
	
	public function setAct($act){
		$this->act=$act;
	}
	
	public function setActew($actew){
	$this->actew=$actew;
	}
	public function setGpa($gpa){
	$this->gpa=$gpa;
	}
	
	public function getExam(){
		return $this->exam;
	}
	
	public function getAct(){
		return $this->act;
	}
	
	public function getActew(){
		return $this->actew;
	}
	public function getGpa(){
		return $this->gpa;
	}
	
	
	
}