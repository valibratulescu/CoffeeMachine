<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class LatteMacchiato extends Coffee
{
    public const string TYPE = "lattemacchiato";
}