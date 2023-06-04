<?php

namespace App\DTO;

class CourseDTO
{
    public string $name;

    public string $code;

    public float $price;

    public string $type;

    public ?\DateInterval $duration = null;
}