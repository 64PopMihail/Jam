<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


//EXEMPLE DE SERVICE CART
//si on souhaiterait regrouper tout ce qui concerne le panier ici plutôt que dans les controlleurs

class Cart
{
    private SessionInterface $session;
    private ParameterBag $parameterBag;
    public function __construct(
        RequestStack $requestStack, ParameterBagInterface $bag
    )
    {
        $this->session = $requestStack->getSession();
        $this->parameterBag=$bag;
    }


    public function update(Product $product, int $quantity){
         $cart = $this->getCart();
        $cart[$product->getId()] = (int)$quantity;
        $this->session->set("cart", $cart);
    }

   
    public function getCart(){
        return $this->session->get("cart",[]);
    }


}