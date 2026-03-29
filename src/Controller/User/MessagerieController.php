<?php

namespace App\Controller\User;


use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MessagerieController extends AbstractController
{
    #[Route('/user/messagerie', name: 'app_user_messagerie')]
    public function messages(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();

        $messages = $messageRepository->findMessagesByUser($user);

        return $this->render('user/messagerie/index.html.twig', [
            'messages' => $messages,
        ]);
    }
}
