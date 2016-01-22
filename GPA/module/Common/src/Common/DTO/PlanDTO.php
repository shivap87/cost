<?php
namespace Common\DTO;

Class PlanDTO
{
	
	
	
	public $uid; 
	public $testing; 
	public $personal; 
	public $major; 
	public $activities; 
	public $gpa;
	public $organization;
	public $costs;
	public $myprofile;
	public $mycounselor;
	

	
	public function exchangeArray($data)
	{
		$this->uid     = (!empty($data['uid'])) ? $data['uid'] : null;
		$this->testing   = (!empty($data['testing'])) ? $data['testing'] : null;
		$this->personal   = (!empty($data['personal'])) ? $data['personal'] : null;
		$this->major   = (!empty($data['major'])) ? $data['major'] : null;
		$this->activities   = (!empty($data['activities'])) ? $data['activities'] : null;
		$this->gpa   = (!empty($data['gpa'])) ? $data['gpa'] : null;
		$this->organization   = (!empty($data['organization'])) ? $data['organization'] : null;
		$this->costs   = (!empty($data['costs'])) ? $data['costs'] : null;
		$this->myprofile   = (!empty($data['myprofile'])) ? $data['myprofile'] : null;
		$this->mycounselor   = (!empty($data['mycounselor'])) ? $data['mycounselor'] : null;
	}
	
}