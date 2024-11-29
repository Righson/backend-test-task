<?php

namespace App\Type;

enum PaymentStatus: string
{
	case OK = 'ok';
	case FAIL = 'fail';
	case UNKNOWN = 'unknown';
}
