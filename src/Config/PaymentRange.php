<?php

namespace App\Config;

enum PaymentRange: int
{
    case MIN = 0;
    case MAX = 100;
}
