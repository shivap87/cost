<?php
namespace Common\DTO;

Class RoleDTO
{
	
	public $role_id; 
	public $type_2; 
	public $created; 
	public $modified; 
	public $state; 
	public $version;
	
	public function exchangeArray($data)
	{
		$this->role_id     = (!empty($data['role_id'])) ? $data['role_id'] : null;
		$this->type_2   = (!empty($data['type_2'])) ? $data['type_2'] : null;
		$this->created   = (!empty($data['created'])) ? $data['created'] : null;
		$this->modified   = (!empty($data['modified'])) ? $data['modified'] : null;
		$this->state   = (!empty($data['state'])) ? $data['state'] : null;
		$this->version   = (!empty($data['version'])) ? $data['version'] : null;
	}
	
}