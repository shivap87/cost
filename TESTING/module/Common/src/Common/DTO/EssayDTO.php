<?php
namespace Common\DTO;

class EssayDTO
{
	public $essay_id;
	public $user_id;
	public $buy_id;
	public $service_id;
	public $reviewer_id;
	public $user_input1;
	public $user_input2;
	public $essay_comment1;
	public $essay_comment2;
	public $status;
	public $submit_date;
	public $review_date;
	public $feedback;

	public function exchangeArray($data)
	{
		$this->essay_id = (! empty($data['essay_id'])) ? $data['essay_id'] : null;
		$this->user_id = (! empty($data['user_id'])) ? $data['user_id'] : null;
		$this->buy_id = (! empty($data['buy_id'])) ? $data['buy_id'] : null;
		$this->service_id = (! empty($data['service_id'])) ? $data['service_id'] : null;
		$this->reviewer_id = (! empty($data['reviewer_id'])) ? $data['reviewer_id'] : null;
		$this->user_input1 = (! empty($data['user_input1'])) ? $data['user_input1'] : null;
		$this->user_input2 = (! empty($data['user_input2'])) ? $data['user_input2'] : null;
		$this->essay_comment1 = (! empty($data['essay_comment1'])) ? $data['essay_comment1'] : null;
		$this->essay_comment2 = (! empty($data['essay_comment2'])) ? $data['essay_comment2'] : null;
		$this->feedback = (! empty($data['feedback'])) ? $data['feedback'] : null;
		$this->submit_date = (! empty($data['submit_date'])) ? $data['submit_date'] : null;
		$this->review_date = (! empty($data['review_date'])) ? $data['review_date'] : null;
		$this->status = (! empty($data['status'])) ? $data['status'] : "un-paid";

	}
}