<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;


class UserDto
{
    /**
     * @Assert\NotBlank(message="Username is mandatory")
     * @Assert\Email(message="Field `username` is not a valid email address.")
     */
    public string $username;

    /**
     * @Assert\Length(min="6", minMessage="Password length must be grater than 6 characters")
     */
    public string $password;
}