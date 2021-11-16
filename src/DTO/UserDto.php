<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @UniqueEntity(fields={"username": "email"}, entityClass="App\Entity\User", em="default")
 */
class UserDto
{
    /**
     * @Assert\NotBlank(message="Name is mandatory")
     * @Assert\Email(message="This value is not a valid email address.")
     */
    public string $username;

    /**
     * @Assert\Length(min="6", minMessage="Password length must be grater than 6 characters")
     */
    public string $password;
}