<?php
namespace Common\DTO;

class CouponMasterDTO
{
	public $coupon_id;
	public $coupon_name;
	public $code;
	public $description;
	public $start_date;
	public $end_date;
	public $discount_value;
	public $discount_type;
	public $curency_type;
	public $coupon_status;
	public $coupon_type;
	public $service_type_ids;



	public function exchangeArray($data)
	{
		$this->coupon_id = (! empty($data['coupon_id'])) ? $data['coupon_id'] : null;
		$this->coupon_name = (! empty($data['coupon_name'])) ? $data['coupon_name'] : null;
		$this->code = (! empty($data['code'])) ? $data['code'] : null;
		$this->description = (! empty($data['description'])) ? $data['description'] : null;
		$this->start_date = (! empty($data['start_date'])) ? $data['start_date'] : null;
		$this->end_date = (! empty($data['end_date'])) ? $data['end_date'] : null;
		$this->discount_value = (! empty($data['discount_value'])) ? $data['discount_value'] : null;		
		$this->curency_type = (! empty($data['curency_type'])) ? $data['curency_type'] : null;
		$this->coupon_status = (! empty($data['coupon_status'])) ? $data['coupon_status'] : null;
		$this->discount_type = (! empty($data['discount_type'])) ? $data['discount_type'] : null;
		$this->coupon_type = (! empty($data['coupon_type'])) ? $data['coupon_type'] : null;
		$this->service_type_ids = (! empty($data['service_type_ids'])) ? $data['service_type_ids'] : null;


	}
}