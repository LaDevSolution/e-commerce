<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    #[Route('/profile/shop', name: 'app_shop')]
    public function index(ProductRepository $productRepository): Response
    {
        //On veut récupérer les produits en BDD grâce à la méthode findall() donc on passe en paramètre ProductRepository
        $products = $productRepository->findAll();
        return $this->render('shop/index.html.twig', [
            'products' => $products,
        ]);
    }
}
