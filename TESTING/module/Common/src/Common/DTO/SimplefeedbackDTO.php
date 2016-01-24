<?php
namespace Common\DTO;
class SimplefeedbackDTO{
	public $id;
	public $q1;
	public $q2;
	public $q3;
	public $q4;
	public $q5;
	public $q6;
	public $q7;

	public function exchangeArray($data)
	{
		$this->id     = (!empty($data['id'])) ? $data['id'] : null;
		$this->q1     = (!empty($data['q1'])) ? $data['q1'] : null;
		$this->q2     = (!empty($data['q2'])) ? $data['q2'] : null;
		$this->q3     = (!empty($data['q3'])) ? $data['q3'] : null;
		$this->q4     = (!empty($data['q4'])) ? $data['q4'] : null;
		$this->q5     = (!empty($data['q5'])) ? $data['q5'] : null;
		$this->q6     = (!empty($data['q6'])) ? $data['q6'] : null;
		$this->q7     = (!empty($data['q7'])) ? $data['q7'] : null;

	}
}