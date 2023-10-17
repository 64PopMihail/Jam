<?php

namespace App\Exception;

use Exception;

class EmailAlreadyExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Un compte utilise déjà cette adresse email.'
        );
    }
}