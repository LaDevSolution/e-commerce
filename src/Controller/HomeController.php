<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        $formation = $productRepository->find(1);
        $soutient = $productRepository->find(2);
        return $this->render('home/index.html.twig', [
            'formation' => $formation,
            'soutient' => $soutient,
        ]);
    }
}
