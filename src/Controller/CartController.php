<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(Cart $cartSession, ProductRepository $productRepo, SessionInterface $session): Response
    {
        $cart = [];
        foreach ($cartSession->getCart() as $id => $quantity) {
            if($quantity>0){
                $cart[] =  ($productRepo->find($id))->setQuantity($quantity);
            }
        }
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/addCart/{id}', name: 'addCart')]
    public function addCart(Cart $cart, Request $request, SessionInterface $session, Product $product): Response
    {
        $quantity=$request->get("quantity");
        $cart->update($product, $quantity);
        $previousUrl = $request->headers->get("referer");
        return $this->redirect($previousUrl);
    }

    #[Route('/cartsize', name: 'cartsize')]
    public function getCartSize(SessionInterface $session): Response
    {
        $totalQuantity = 0;
        foreach ($session->get("cart", []) as $id => $quantity) {
            if($quantity>0){
                $totalQuantity+=$quantity;
          }
        }
        return new Response($totalQuantity);
    }

  
   

  
}






