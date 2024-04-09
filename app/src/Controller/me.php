<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class me extends AbstractController
{
    #[Route("/", name: "me")]
    public function number(): Response
    {
        return $this->render('me.html.twig');
    }
}
