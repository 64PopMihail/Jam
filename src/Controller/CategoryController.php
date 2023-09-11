<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

   #[Route('/category/{slug}', name: 'category')]
    public function index(Category $category): Response
    {
        $products = $category->getProducts();
        return $this->render('home/index.html.twig', [
            "products"=>$products
            
        ]);
    }
}
