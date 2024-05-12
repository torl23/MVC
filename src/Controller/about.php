<?php

namespace App\Controller;

use App\Controller\card;
use App\Controller\deck;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class about extends AbstractController
{
    #[Route("/", name: "me")]
    public function me(): Response
    {
        return $this->render('me.html.twig');
    }

    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route("/report", name: "report")]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }

    #[Route("/lucky", name: "lucky")]
    public function lucky(): Response
    {

        $number = random_int(0, 100);

        $data = [
            'number' => $number
        ];

        return $this->render('lucky.html.twig', $data);
    }

    #[Route("/api", name: "api")]
    public function api(): Response
    {
        return $this->render('api.html.twig');
    }

    #[Route("/api/quote", name: "api/quote")]
    public function api_quote(): JsonResponse
    {
        $number = random_int(1, 3);

        if ($number === 3) {
            $quote = "If you want something done right, do it yourself.";
        } elseif ($number === 2) {
            $quote = "If at first you dont succeed, try, try again.";
        } else {
            $quote = "Theres no place like home.";
        }

        $date = date('Y-m-d');
        $current = date('H:i:s');

        $data = [
            'quote' => $quote,
            'date' => $date,
            'generated' => $current
        ];

        return new JsonResponse($data);
    }
}
