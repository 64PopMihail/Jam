<?php

namespace App\Util;

use Psr\Container\ContainerInterface;

class TokenUtil 
{
    public static function getStripeToken(ContainerInterface $container): string
    {
        dd($container);
        $tokenProvider = $container->get('security.csrf.token_manager');
        
        return $tokenProvider->getToken('stripe_token')->getValue();
    }
}