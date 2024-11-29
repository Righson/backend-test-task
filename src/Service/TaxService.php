<?php

namespace App\Service;

class TaxService
{
	public function calculateTax(int $price, int $tax, int $discount = 0): int
	{
		$percent = $price / 100;

		if ($discount) {
			$price = $price - ($percent * $discount);
			$percent = $price / 100;
		}

		return $price + ($percent * $tax);
	}
}
