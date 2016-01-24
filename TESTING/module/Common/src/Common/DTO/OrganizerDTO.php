<?php
namespace Common\DTO;

Class OrganizerDTO
{
	public $event_id;
	public $event_title;
	public $event_name;
	public $event_type;
	public $event_des;
	public $start_date;
	public $end_date;
	public $is_notify;
	
	public function exchangeArray($data)
	{
		$this->event_id = (!empty($data['event_id'])) ? $data['event_id'] : null;
		$this->event_title = (!empty($data['event_title'])) ? $data['event_title'] : null;
		$this->event_name = (!empty($data['event_name'])) ? $data['event_name'] : null;
		$this->event_type = (!empty($data['event_type'])) ? $data['event_type'] : null;
		$this->event_des = (!empty($data['event_des'])) ? $data['event_des'] : null;
		$this->start_date = (!empty($data['start_date'])) ? $data['start_date'] : null;
		$this->end_date = (!empty($data['end_date'])) ? $data['end_date'] : null;
		$this->is_notify = (!empty($data['is_notify'])) ? $data['is_notify'] : null;
	}
}