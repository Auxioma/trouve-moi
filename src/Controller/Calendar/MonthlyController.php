<?php

namespace App\Controller\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MonthlyController extends AbstractController
{
    #[Route('/calendar/monthly', name: 'app_calendar_monthly')]
    public function index(): Response
    {
        return $this->render('calendar/monthly/index.html.twig', [
            'controller_name' => 'MonthlyController',
        ]);
    }
}
