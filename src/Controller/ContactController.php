<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/profile/contact', name: 'app_contact')]
    public function index(MailerInterface $mailer, Request $request): Response
    {
        //Get user datas
        $userName = ucfirst($this->getUser()->getName());
        $userFirstname = ucfirst($this->getUser()->getFirstname());
        $userEmail = $this->getUser()->getEmail();

        //Get admin email
        $adminEmail = $_ENV["ADMIN_EMAIL"];

        //We can add an array below with datas like username or other in order to display in contact form
        $contactForm = $this->createForm(ContactType::class);

        $contactForm->handleRequest($request);

        if($contactForm->isSubmitted() && $contactForm->isValid()) {
            $this->addFlash("success", "Votre message a bien été envoyé.");
            $data = $contactForm->getData();
            $message = $data["message"];
            $mailer->send((new TemplatedEmail())
            ->from($adminEmail)
            ->to($adminEmail) 
            ->replyTo($userEmail)
            ->subject("La Dev'Solution - Contact")     
            ->htmlTemplate("contact/email.html.twig")      
            ->context([
                "message" => $message,
                "name" => $userName,
                "firstname" =>$userFirstname,
                "userEmail" => $userEmail
            ])
            );
        }
        return $this->render('contact/index.html.twig', [
            'contactform' => $contactForm->createView()
        ]);
    }
}
