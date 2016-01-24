<?php
namespace Common\DTO;
Class ShareresultDTO
{
	public $id;
	public $email;
	public $type;
	

public function exchangeArray($data)
{
	$this->id     = (!empty($data['id'])) ? $data['id'] : null;
	$this->email     = (!empty($data['email'])) ? $data['email'] : null;
	$this->type     = (!empty($data['type'])) ? $data['type'] : null;
}
}