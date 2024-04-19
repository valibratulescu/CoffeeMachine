<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Cappuccino extends Coffee
{
    public const string TYPE = "cappuccino";
}