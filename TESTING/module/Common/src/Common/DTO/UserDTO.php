<?php
namespace Common\DTO;
Class UserDTO
{
	
	public $user_id;
	public $tbl_role_role_id;
	public $first_name; 
	public $middle_name; 
	public $last_name; 
	public $email_id; 
	public $user_password; 
	public $phone; 
	public $profile_picture; 
	public $dob; 
	public $resident_state; 
	public $related_to;
	public $regchannel;
	public $verificationkey;
	public $isverified;
	public $regdate;
	public $lastlogin;
	public $gensubkey;
	public $isgensub;
	public $usersubkey;
	public $isusersub;
	
	
	
	public function exchangeArray($data)
	{
		$this->user_id=(!empty($data['user_id'])) ? $data['user_id'] : null;
		$this->tbl_role_role_id=(!empty($data['tbl_role_role_id'])) ? $data['tbl_role_role_id'] : null;
		$this->first_name=(!empty($data['first_name'])) ? $data['first_name'] : null;
		$this->middle_name=(!empty($data['middle_name'])) ? $data['middle_name'] : null;
		$this->last_name=(!empty($data['last_name'])) ? $data['last_name'] : null;
		$this->email_id=(!empty($data['email_id'])) ? $data['email_id'] : null;
		$this->user_password=(!empty($data['user_password'])) ? $data['user_password'] : null;
		$this->phone=(!empty($data['phone'])) ? $data['phone'] : null;
		$this->profile_picture=(!empty($data['profile_picture'])) ? $data['profile_picture'] : null;
		$this->dob=(!empty($data['dob'])) ? $data['dob'] : null;
		$this->resident_state=(!empty($data['resident_state'])) ? $data['resident_state'] : null;
		$this->related_to=(!empty($data['related_to'])) ? $data['related_to'] : null;
		
		$this->verificationkey=(!empty($data['verificationkey'])) ? $data['verificationkey'] : null;
		$this->isverified=(!empty($data['isverified'])) ? $data['isverified'] : null;
		$this->regdate=(!empty($data['regdate'])) ? $data['regdate'] : null;
		$this->lastlogin=(!empty($data['lastlogin'])) ? $data['lastlogin'] : null;
		$this->gensubkey=(!empty($data['gensubkey'])) ? $data['gensubkey'] : null;
		$this->isgensub=(!empty($data['isgensub'])) ? $data['isgensub'] : null;
		$this->usersubkey=(!empty($data['usersubkey'])) ? $data['usersubkey'] : null;
		$this->isusersub=(!empty($data['isusersub'])) ? $data['isusersub'] : null;
		$this->regchannel=(!empty($data['regchannel'])) ? $data['regchannel'] : null;
		
	}
	
}