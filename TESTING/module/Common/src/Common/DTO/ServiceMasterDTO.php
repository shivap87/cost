<?php

namespace Common\DTO;

class ServiceMasterDTO
{
	public $service_id;
	public $service_name;
	public $service_cost;	
	public $is_active;





	public function exchangeArray($data)
	{
		$this->service_id = (! empty($data['service_id'])) ? $data['service_id'] : null;
		$this->service_name = (! empty($data['service_name'])) ? $data['service_name'] : null;
		$this->service_cost = (! empty($data['service_cost'])) ? $data['service_cost'] : null;
		$this->is_active = (! empty($data['is_active'])) ? $data['is_active'] : "yes";
	}
}