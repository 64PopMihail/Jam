<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Action\CheckoutAction;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/checkout',
            stateless: false,
            controller: CheckoutAction::class
        )
    ]
)]
class CheckoutActionResource
{
}