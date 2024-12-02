<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints as Assert;

use App\Type\PaymentStatus;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PaymentService
{
	const COUPON_PREFIX = 'DIS';
	private string $message = '';

	private int $productId = 0;
	private string $taxNumber = '';
	private string $couponCode = '';
	private string $paymentProcessor = 'no';

	private int $calulatedPrice = -1;

	private array $paymentProcessors = [
		'stripe' => 'doStripePayment',
		'paypal' => 'doPaypalPayment',
		'no' => 'doNoPayment'
	];

	private array $taxNumberPatterns = [];
	private array $coupons = [];


	#[Assert\IsTrue(message: 'Product id must be greater than 0')]
	public function isValidProductId(): bool
	{
		return $this->productId > 0;
    }

    /**
    * Решил сделать валидацию так, вместо того чтобы делать свой кастомный валидатор
    * так как этот способ мне показался более удобным
    * для валидации налогового номера мне нужны паттерны которые находятся в базе
    * в валидаторе пришлось бы проверять теже паттерны что используются в сервисе
    * а тут они уже тоже есть.
    */
	#[Assert\IsTrue(message: 'Tax number is invalid or unknown')]
	public function isValidTaxNumber(): bool
	{

		$value = strtoupper($this->taxNumber);

		$county = $this->getCountyCode();
		$code = preg_replace(['/[A-Z]/', '/[0-9]/'], ['Y', 'X'], substr($value, 2));
		$pattern = $county . $code;


		foreach($this->taxNumberPatterns as $taxNumber) {
			if($taxNumber->getPattern() === $pattern) {
				return true;
			}
		}
		return false;
	}

    /**
    * Тут нужно было бы проверить существование купона отделным валидатором
    * и отедльным валдатором корректность самого кода купоны. 
    * Чтобы было две ошибки на каждый кейс
    */
	#[Assert\IsTrue(message: 'Coupon code is invalid or unknown')]
	public function isValidCouponCode(): bool
	{
		if ('' === $this->couponCode) {
			return true;
		}

		if (str_starts_with($this->couponCode, self::COUPON_PREFIX)) {
			foreach($this->coupons as $coupon) {
				if($coupon->getName() === $this->couponCode) {
					return true;
				}
			}
		}
		return false;
	}

	#[Assert\IsTrue(message: 'Payment processor is invalid or unknown')]
	public function isValidPaymentProcessor(): bool
	{
		return array_key_exists($this->paymentProcessor, $this->paymentProcessors);
	}

	private function getCountyCode(?string $taxNumber = null): string
	{
		if ( null === $taxNumber ) {
			$taxNumber = $this->taxNumber;
		}

		return substr($taxNumber, 0, 2);
	}

	public function getProductID(): int
	{
		return $this->productId;
	}

	public function setCopons(array $coupons): self
	{
		$this->coupons = $coupons;
		return $this;
	}

	public function setTaxNumberPatterns(array $taxNumberPatterns): self
	{
		$this->taxNumberPatterns = $taxNumberPatterns;
		return $this;
	}

	public function setTaxNumber(string $taxNumber): self
	{
		$this->taxNumber = $taxNumber;
		return $this;
	}

	public function setProductId(int $productId): self
	{
		$this->productId = $productId;
		return $this;
	}

	public function setCouponCode(string $couponCode): self
	{
		$this->couponCode = $couponCode;
		return $this;
	}

	public function setPaymentProcessor(string $paymentProcessor): self
	{
		$this->paymentProcessor = $paymentProcessor;
		return $this;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function pay(int $price): PaymentStatus
	{
        try {
		$price = $this->calculateTax($price);
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            return PaymentStatus::FAIL;
        }

		if ($this->isValidPaymentProcessor()) {
			$isSuccess = call_user_func([$this, $this->paymentProcessors[$this->paymentProcessor]], $price);
			return $isSuccess ? PaymentStatus::OK : PaymentStatus::FAIL;
		}

		return PaymentStatus::UNKNOWN;
	}

	public function calculateTax(int $price): int
	{
        // на случай если валидация не производилась в контроллере
		if($this->calulatedPrice == -1) {
            try {
			$discount = $this->getCouponDiscount();
            } catch (\Exception $e) {
                // пробросим исключение наверх
                throw $e;
            }
			$tax = $this->getTax();
			$percent = $price / 100;

			if ($discount) {
				$price = $price - ($percent * $discount);
				$percent = $price / 100;
			}

			$this->calulatedPrice = $price + ($percent * $tax);
		}
		return $this->calulatedPrice;
	}

	private function getCouponDiscount(): int
    {
        // на случай если валидация не производилась в контроллере
		if($this->isValidCouponCode()) {
			return (int) str_replace(self::COUPON_PREFIX, '', $this->couponCode);
        }
        throw new \Exception('Invalid coupon code');
	}

	private function getTax(): int
	{
		if($this->isValidTaxNumber()) {
			foreach($this->taxNumberPatterns as $taxNumber) {
				$thatContry = $this->getCountyCode($taxNumber->getPattern());
				$thisContry = $this->getCountyCode();

				if($thatContry === $thisContry) {
					return $taxNumber->getTax();
				}
			}
		}

		throw new \Exception('Invalid tax number');
	}

	private function doStripePayment(int $price): PaymentStatus
	{
		$price = $price / 100;
		if ((new StripePaymentProcessor())->processPayment($price)) {
			return PaymentStatus::OK;
		}

		return PaymentStatus::FAIL;
	}

	private function doPaypalPayment(int $price): PaymentStatus
	{
		try {
			(new PaypalPaymentProcessor())->pay($price);
		} catch (\Exception $e) {
			$this->message = $e->getMessage();
			return PaymentStatus::FAIL;
		}

		return PaymentStatus::OK;
	}

	private function doNoPayment(int $price): PaymentStatus
	{
		return PaymentStatus::OK;
	}
}
