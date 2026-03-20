<?php

namespace App\Controller\Api;

use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ServicesAjaxController extends AbstractController
{
    #[Route('/ajax/services/by-activity/{id}', name: 'ajax_services_by_activity', methods: ['GET'])]
    public function byActivity(int $id, ServicesRepository $servicesRepository): JsonResponse
    {
        $services = $servicesRepository->findBy(
            ['activity' => $id],
            ['name' => 'ASC']
        );

        $data = [];

        foreach ($services as $service) {
            $data[] = [
                'id' => $service->getId(),
                'name' => $service->getName(),
            ];
        }

        return $this->json($data);
    }
}