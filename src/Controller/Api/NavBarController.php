<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


final class NavBarController extends AbstractController
{
     public function NavBar(): Response
    {
        return $this->render('_partials/navbar.html.twig');
    }
}