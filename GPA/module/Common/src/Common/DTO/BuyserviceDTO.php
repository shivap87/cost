<?php
namespace Common\DTO;

class BuyserviceDTO
{
	public $id;
	public $user_id;
	public $service_id;
	public $payment_date;
	public $payment_status;
	public $transaction_id;
	public $payment_type;
	public $payment_fee;
	public $txn_type;
	public $payment_gross;
	public $curency_type;
	public $user_email;
	public $serviceType;
	public $paypal_id;
	public $coupon_id;
	public $is_accessed;





	public function exchangeArray($data)
	{
		$this->id = (! empty($data['id'])) ? $data['id'] : null;
		$this->user_id = (! empty($data['user_id'])) ? $data['user_id'] : null;
		$this->service_id = (! empty($data['service_id'])) ? $data['service_id'] : null;
		$this->payment_date = (! empty($data['payment_date'])) ? $data['payment_date'] : null;
		$this->payment_status = (! empty($data['payment_status'])) ? $data['payment_status'] : null;
		$this->transaction_id = (! empty($data['transaction_id'])) ? $data['transaction_id'] : null;
		$this->payment_type = (! empty($data['payment_type'])) ? $data['payment_type'] : null;
		$this->payment_fee = (! empty($data['payment_fee'])) ? $data['payment_fee'] : null;
		$this->txn_type = (! empty($data['txn_type'])) ? $data['txn_type'] : null;
		$this->payment_gross = (! empty($data['payment_gross'])) ? $data['payment_gross'] : null;
		$this->curency_type = (! empty($data['curency_type'])) ? $data['curency_type'] : null;
		$this->user_email = (! empty($data['user_email'])) ? $data['user_email'] : null;
		$this->paypal_id = (! empty($data['paypal_id'])) ? $data['paypal_id'] : null;
		$this->serviceType = (! empty($data['serviceType'])) ? $data['serviceType'] : null;
		$this->coupon_id = (! empty($data['coupon_id'])) ? $data['coupon_id'] : null;
		$this->is_accessed = (! empty($data['is_accessed'])) ? $data['is_accessed'] : "no";

	}
}