<?php

/**
 * Copyright (c) 2026 Auxioma Web Agency
 * https://trouvemoi.eu
 *
 * Ce fichier fait partie du projet Trouvemoi.eu développé par Auxioma Web Agency.
 * Tous droits réservés.
 *
 * Ce code source, son architecture, sa structure, ses scripts et ses composants
 * sont la propriété exclusive de Auxioma Web Agency et de ses partenaires.
 *
 * Toute reproduction, modification, distribution, publication ou utilisation,
 * totale ou partielle, sans autorisation écrite préalable est strictement interdite.
 *
 * Ce code est confidentiel et propriétaire.
 * Droit applicable : Monde.
 */

namespace App\Controller\User;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MessagerieController extends AbstractController
{
    #[Route('/user/messagerie', name: 'app_user_messagerie', methods: ['GET'])]
    public function index(ConversationRepository $conversationRepository): Response
    {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $conversations = $conversationRepository->findConversationsWithMessagesByUser($currentUser);

        $discussions = [];

        foreach ($conversations as $conversation) {
            $otherUser = null;

            foreach ($conversation->getParticipants() as $participant) {
                $participantUser = $participant->getUser();

                if (
                    null !== $participantUser
                    && $participantUser->getId() !== $currentUser->getId()
                ) {
                    $otherUser = $participantUser;
                    break;
                }
            }

            if (null === $otherUser) {
                continue;
            }

            $messages = $conversation->getMessages();
            $lastMessage = $messages->isEmpty() ? null : $messages->last();

            $discussions[] = [
                'user' => $otherUser,
                'conversation' => $conversation,
                'lastMessage' => $lastMessage,
            ];
        }

        return $this->render('user/messagerie/index.html.twig', [
            'discussions' => $discussions,
        ]);
    }

    #[Route('/user/messagerie/{id}', name: 'app_user_messagerie_show', methods: ['GET', 'POST'])]
    public function show(
        int $id,
        ConversationRepository $conversationRepository,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $conversation = $conversationRepository->findUserConversationById($currentUser, $id);

        if (!$conversation) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        if ($request->isMethod('POST')) {
            $content = mb_trim((string) $request->request->get('content'));

            if ('' !== $content) {
                $message = new Message();
                $message->setConversation($conversation);
                $message->setSender($currentUser);
                $message->setContent($content);

                $conversation->setUpdatedAt(new \DateTimeImmutable());

                $entityManager->persist($message);
                $entityManager->flush();

                return $this->redirectToRoute('app_user_messagerie_show', [
                    'id' => $conversation->getId(),
                ]);
            }
        }

        $conversations = $conversationRepository->findConversationsWithMessagesByUser($currentUser);

        $discussions = [];

        foreach ($conversations as $conversationItem) {
            $otherUser = null;

            foreach ($conversationItem->getParticipants() as $participant) {
                $participantUser = $participant->getUser();

                if (
                    null !== $participantUser
                    && $participantUser->getId() !== $currentUser->getId()
                ) {
                    $otherUser = $participantUser;
                    break;
                }
            }

            if (null === $otherUser) {
                continue;
            }

            $messages = $conversationItem->getMessages();
            $lastMessage = $messages->isEmpty() ? null : $messages->last();

            $discussions[] = [
                'user' => $otherUser,
                'conversation' => $conversationItem,
                'lastMessage' => $lastMessage,
            ];
        }

        return $this->render('user/messagerie/show.html.twig', [
            'conversation' => $conversation,
            'messages' => $conversation->getMessages(),
            'discussions' => $discussions,
        ]);
    }
}
