<?php

namespace App\Controller\User\Offer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PackageController extends AbstractController
{
    #[Route('/user/offer/package', name: 'app_user_offer_package')]
    public function index(): Response
    {
        return $this->render('user/offer/package/index.html.twig', [
            'controller_name' => 'PackageController',
        ]);
    }
}
