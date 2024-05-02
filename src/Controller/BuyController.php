<?php

namespace App\Controller;

use Stripe\StripeClient;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuyController extends AbstractController
{
    private $manager;
    private $gateway;
    private $url;
    private $cart;


    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager=$manager;
        
        if ($_ENV['APP_ENV'] === 'dev') {
            $this->gateway= new StripeClient($_ENV['STRIPE_SECRET_KEY_DEV']);
            $this->url = 'http://127.0.0.1:8000';
        } else if ($_ENV['APP_ENV'] === 'prod') {
            var_dump('t\'es en prod !');exit;
            $this->gateway= new StripeClient($_ENV['STRIPE_SECRET_KEY_PROD']);
            $this->url = 'https://easywebjob.fr';
        } else {
            var_dump('BuyController error');exit;
        };
    }

    
    #[Route('/profile/buy', name: 'app_buy')]
    public function index(CartService $cartService): Response
    {   
        $userEmail = $this->getUser()->getEmail();
        $cart = $cartService->getFullCart();
        // if ($_ENV['APP_ENV'] === 'dev') {
        //     $stripeSecretKey = $_ENV["STRIPE_SECRET_KEY_DEV"];
        //     $this->url = 'http://127.0.0.1:8000';
        // } else if ($_ENV['APP_ENV'] === 'prod') {
        //     $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY_PROD'];
        //     $this->url = 'https://easywebjob.fr';
        // } else {
        //     var_dump('BuyController error');exit;
        // };

        $checkout=$this->gateway->checkout->sessions->create([
            'billing_address_collection' => "required",
            'custom_text' => [
                'submit' => [
                    'message' => "En cliquant sur j'accepte, vous renoncez à votre droit à un délai de rétractation de 14 jours et ne pourrez demander un remboursement.",
                ],
            ],
            'consent_collection' => [
                'terms_of_service' => 'required',
            ],
            'customer_email' => $userEmail,
            'line_items' => [
                array_map(fn (array $item) => [
                    "quantity" => $item["quantity"],
                    "price_data" => [
                        "currency" => "EUR",
                        "unit_amount" => $item["product"]->getPrice() * 100,
                        "product_data" => [
                            "name" => $item["product"]->getName(),
                            "description" => $item["product"]->getDescription()
                        ]
                    ],
                ], $cart)
            ],
            'mode' => 'payment',
            'allow_promotion_codes' => true,
            'invoice_creation' => [
                'enabled' => true,
                'invoice_data' => [
                    'custom_fields' => [
                        [
                            'name' => 'SIRET',
                            'value' => '83218902100021',
                        ],
                        [
                            'name' => 'Code APE',
                            'value' => '6201Z',
                        ],
                        [
                            'name' => 'TVA',
                            'value' => 'non applicable ART.293B du CGI',
                        ],
                    ],
                ],
            ],
            'success_url' => $this->url . '/profile/success?id_sessions={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->url . '/profile/cancel?id_sessions={CHECKOUT_SESSION_ID}'
            //We can active if the tax amount is define in stripe
            /*'automatic_tax' => [
                'enabled' => true,
            ],*/
        ]);

        return $this->redirect($checkout->url);









        // \Stripe\Stripe::setApiKey($stripeSecretKey);
        // header('Content-Type: application/json');

        // $checkout_session = \Stripe\Checkout\Session::create([
        //     'billing_address_collection' => "required",
        //     'custom_text' => [
        //         'submit' => [
        //             'message' => "En cliquant sur j'accepte, vous renoncez à votre droit à un délai de rétractation de 14 jours et ne pourrez demander un remboursement.",
        //         ],
        //     ],
        //     'consent_collection' => [
        //         'terms_of_service' => 'required',
        //     ],
        //     'customer_email' => $userEmail,
        //     'line_items' => [
        //         array_map(fn (array $item) => [
        //             "quantity" => $item["quantity"],
        //             "price_data" => [
        //                 "currency" => "EUR",
        //                 "unit_amount" => $item["product"]->getPrice() * 100,
        //                 "product_data" => [
        //                     "name" => $item["product"]->getName(),
        //                     "description" => $item["product"]->getDescription()
        //                 ]
        //             ],
        //         ], $cart)
        //     ],
        //     'mode' => 'payment',
        //     'allow_promotion_codes' => true,
        //     'invoice_creation' => [
        //         'enabled' => true,
        //         'invoice_data' => [
        //             'custom_fields' => [
        //                 [
        //                     'name' => 'SIRET',
        //                     'value' => '83218902100021',
        //                 ],
        //                 [
        //                     'name' => 'Code APE',
        //                     'value' => '6201Z',
        //                 ],
        //                 [
        //                     'name' => 'TVA',
        //                     'value' => 'non applicable ART.293B du CGI',
        //                 ],
        //             ],
        //         ],
        //     ],
        //     'success_url' => $this->url . '/profile/success',
        //     'cancel_url' => $this->url . '/profile/cancel',
        //     //We can active if the tax amount is define in stripe
        //     /*'automatic_tax' => [
        //         'enabled' => true,
        //     ],*/
        // ]);

        // header("HTTP/1.1 303 See Other");
        // header("Location: " . $checkout_session->url);

        // return $this->redirect($checkout_session->url, 303);
    }

    #[Route(path:'/profile/success', name:'app_success')]

    public function success(Request $request): Response
    {
        $id_sessions=$request->query->get('id_sessions');

        //Récupère le customer via l'id de la  session
        $customer=$this->gateway->checkout->sessions->retrieve(
            $id_sessions,
            []
        );

        //Récupérer les informations du customer et de la transaction
        echo '<pre>'.$customer.'</pre>';
        echo $customer["custom_fields"];
        var_dump($customer);exit;





        return $this->render('buy/success.html.twig');
    }

    #[Route(path:'/profile/cancel', name:'app_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('cancel', 'Votre achat a été annulé');

        return $this->redirectToRoute('app_home');

    }
}