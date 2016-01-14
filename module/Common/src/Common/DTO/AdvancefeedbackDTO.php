<?php
namespace Common\DTO;

class AdvancefeedbackDTO{
	public $id;
	public $a1;
	public $a2;
	public $a3;
	public $a4;
	public $a5;

	public $s1;
	public $s2;
	public $s3;
	public $s4;
	public $s5;

	public function exchangeArray($data)
	{
		$this->id     = (!empty($data['id'])) ? $data['id'] : null;
		$this->a1     = (!empty($data['a1'])) ? $data['a1'] : null;
		$this->a2     = (!empty($data['a2'])) ? $data['a2'] : null;
		$this->a3     = (!empty($data['a3'])) ? $data['a3'] : null;
		$this->a4     = (!empty($data['a4'])) ? $data['a4'] : null;
		$this->a5     = (!empty($data['a5'])) ? $data['a5'] : null;
		$this->s1     = (!empty($data['s1'])) ? $data['s1'] : null;
		$this->s2     = (!empty($data['s2'])) ? $data['s2'] : null;
		$this->s3     = (!empty($data['s3'])) ? $data['s3'] : null;
		$this->s4     = (!empty($data['s4'])) ? $data['s4'] : null;
		$this->s5     = (!empty($data['s5'])) ? $data['s5'] : null;

	}
}