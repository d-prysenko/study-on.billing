<?php

namespace App\DTO;

use App\Entity\Course;

class CourseDTO
{
    public string $name;

    public string $code;

    public float $price;

    public string $type;

    public ?\DateInterval $duration = null;
}