<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
class UserRegistrationDto
{
    #[Assert\NotBlank(message: "Vous devez saisir un email")]
    #[Assert\Email(message: "L'email saisi n'est pas valide")]
    public string $email;

    #[Assert\NotBlank(message: "Vous devez saisir un mot de passe")]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/",
        message: "Le mot de passe doit contenir au moins une lettre majuscule. Le mot de passe doit contenir au moins une lettre minuscule. Le mot de passe doit contenir au moins un chiffre. Le mot de passe doit contenir au moins un des caractères spéciaux -+!*$@%_. Le mot de passe doit faire entre 8 et 15 caractères"
    )]
    public string $plainPassword;
    
    #[Assert\IsTrue(message:"Vous devez cocher la case.")]
    public bool $agreedTerms;
}