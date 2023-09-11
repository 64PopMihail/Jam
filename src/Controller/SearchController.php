<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    
    #[Route('/api-search/{val}', name: 'api-search')]
    public function searchAPI(ProductRepository $productRepository, string $val): Response
    {
        $products = $productRepository->findAllLike($val);
       
        // $productsNames = array_map(function($p){
        //     return $p->getName();
        // },$products);

        $productsNames=[];
        foreach($products as $product){
            $productsNames[] = $product->getName();
        }
        return $this->json($productsNames);
    }

    
    #[Route('/search', name: 'search')]
    public function search(ProductRepository $productRepository, Request $request): Response
    {
        $val=$request->query->get("search");
        $products = $productRepository->findAllLike($val);
       
        return $this->render('search/index.html.twig', [
            "products" => $products
        ]);
    }

  
    
}







