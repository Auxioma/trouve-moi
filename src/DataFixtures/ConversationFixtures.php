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

namespace App\DataFixtures;

use App\Entity\Conversation;
use App\Entity\ConversationParticipant;
use App\Entity\Message;
use App\Entity\MessageRead;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ConversationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User[] $users */
        $users = $manager->getRepository(User::class)->findAll();

        if (\count($users) < 2) {
            throw new \RuntimeException('Pas assez d\'utilisateurs. Vérifie UserFixtures.');
        }

        $userRepository = $manager->getRepository(User::class);

        /** @var User|null $specificUser */
        $specificUser = $userRepository->findOneBy(['email' => 'user@user.user']);

        /** @var User|null $specificArtisan */
        $specificArtisan = $userRepository->findOneBy(['email' => 'artisan@artisan.artisan']);

        if (!$specificUser || !$specificArtisan) {
            throw new \RuntimeException('Les utilisateurs user@user.user et artisan@artisan.artisan doivent exister dans UserFixtures.');
        }

        $conversations = [];

        // 1. Conversation prioritaire entre les 2 comptes voulus
        $priorityConversation = $this->createSpecificConversation(
            $manager,
            $specificUser,
            $specificArtisan
        );

        $conversations[] = $priorityConversation;

        // 2. Conversations aléatoires complémentaires
        $randomConversations = $this->createConversations(
            $manager,
            $users,
            [
                $specificUser->getEmail().'|'.$specificArtisan->getEmail(),
                $specificArtisan->getEmail().'|'.$specificUser->getEmail(),
            ]
        );

        $conversations = array_merge($conversations, $randomConversations);

        // 3. Messages : beaucoup pour la conversation spécifique
        $messages = $this->createMessages($manager, $conversations);

        // 4. Lectures des messages
        $this->createMessageReads($manager, $messages);

        $manager->flush();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CONVERSATION SPÉCIFIQUE
    // ──────────────────────────────────────────────────────────────────────────

    private function createSpecificConversation(
        ObjectManager $manager,
        User $user,
        User $artisan,
    ): array {
        $createdAt = $this->randomDate('-6 months', '-2 months');
        $updatedAt = $this->randomDate($createdAt->format('Y-m-d H:i:s'), 'now');

        $conversation = new Conversation();
        $conversation->setCreatedAt($createdAt);
        $conversation->setUpdatedAt($updatedAt);

        $participantA = new ConversationParticipant();
        $participantA->setConversation($conversation);
        $participantA->setUser($user);
        $participantA->setJoinedAt($createdAt);

        $participantB = new ConversationParticipant();
        $participantB->setConversation($conversation);
        $participantB->setUser($artisan);
        $participantB->setJoinedAt($createdAt);

        $manager->persist($conversation);
        $manager->persist($participantA);
        $manager->persist($participantB);

        return [
            'entity' => $conversation,
            'userA' => $user,
            'userB' => $artisan,
            'createdAt' => $createdAt,
            'isPriority' => true,
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CONVERSATIONS ALÉATOIRES
    // ──────────────────────────────────────────────────────────────────────────

    private function createConversations(
        ObjectManager $manager,
        array $users,
        array $excludedPairs = [],
    ): array {
        $conversations = [];
        $pairs = [];

        foreach ($excludedPairs as $excludedPair) {
            $pairs[$excludedPair] = true;
        }

        $attempts = 0;
        while (\count($conversations) < 59 && $attempts < 5000) {
            ++$attempts;

            $aIdx = array_rand($users);
            $bIdx = array_rand($users);

            if ($aIdx === $bIdx) {
                continue;
            }

            $userA = $users[$aIdx];
            $userB = $users[$bIdx];

            $pairKey = $userA->getEmail().'|'.$userB->getEmail();
            $reversePairKey = $userB->getEmail().'|'.$userA->getEmail();

            if (isset($pairs[$pairKey]) || isset($pairs[$reversePairKey])) {
                continue;
            }

            $pairs[$pairKey] = true;
            $pairs[$reversePairKey] = true;

            $createdAt = $this->randomDate('-6 months', '-1 day');
            $updatedAt = $this->randomDate($createdAt->format('Y-m-d H:i:s'), 'now');

            $conversation = new Conversation();
            $conversation->setCreatedAt($createdAt);
            $conversation->setUpdatedAt($updatedAt);

            $participantA = new ConversationParticipant();
            $participantA->setConversation($conversation);
            $participantA->setUser($userA);
            $participantA->setJoinedAt($createdAt);

            $participantB = new ConversationParticipant();
            $participantB->setConversation($conversation);
            $participantB->setUser($userB);
            $participantB->setJoinedAt($createdAt);

            $manager->persist($conversation);
            $manager->persist($participantA);
            $manager->persist($participantB);

            $conversations[] = [
                'entity' => $conversation,
                'userA' => $userA,
                'userB' => $userB,
                'createdAt' => $createdAt,
                'isPriority' => false,
            ];
        }

        return $conversations;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // MESSAGES
    // ──────────────────────────────────────────────────────────────────────────

    private function createMessages(ObjectManager $manager, array $conversations): array
    {
        $pool = $this->messagePool();
        $allMessages = [];

        foreach ($conversations as $conv) {
            /** @var Conversation $conversation */
            $conversation = $conv['entity'];
            /** @var User $userA */
            $userA = $conv['userA'];
            /** @var User $userB */
            $userB = $conv['userB'];

            $currentDate = clone $conv['createdAt'];

            // Beaucoup de messages pour la conversation spécifique
            $msgCount = !empty($conv['isPriority'])
                ? random_int(80, 140)
                : random_int(5, 10);

            for ($i = 0; $i < $msgCount; ++$i) {
                $sender = (0 === $i % 2) ? $userA : $userB;

                // Petite variation pour éviter un échange trop mécanique
                if (1 === random_int(1, 5)) {
                    $sender = ($sender === $userA) ? $userB : $userA;
                }

                $currentDate = $currentDate->modify('+'.random_int(5, 720).' minutes');

                $message = new Message();
                $message->setConversation($conversation);
                $message->setSender($sender);
                $message->setContent($pool[array_rand($pool)]);
                $message->setCreatedAt($currentDate);

                $manager->persist($message);

                $allMessages[] = [
                    'entity' => $message,
                    'sender' => $sender,
                    'userA' => $userA,
                    'userB' => $userB,
                    'sentAt' => clone $currentDate,
                ];
            }

            // Met à jour la date de dernière activité de la conversation
            $conversation->setUpdatedAt($currentDate);
        }

        return $allMessages;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // MESSAGE READS
    // ──────────────────────────────────────────────────────────────────────────

    private function createMessageReads(ObjectManager $manager, array $messages): void
    {
        $tracked = [];

        foreach ($messages as $msgData) {
            if (random_int(1, 10) > 7) {
                continue;
            }

            /** @var Message $message */
            $message = $msgData['entity'];
            /** @var User $sender */
            $sender = $msgData['sender'];
            /** @var User $recipient */
            $recipient = ($sender === $msgData['userA']) ? $msgData['userB'] : $msgData['userA'];

            $key = spl_object_id($message).'-'.spl_object_id($recipient);
            if (isset($tracked[$key])) {
                continue;
            }

            $tracked[$key] = true;

            $readAt = $msgData['sentAt']->modify('+'.random_int(1, 1440).' minutes');

            $read = new MessageRead();
            $read->setMessage($message);
            $read->setUser($recipient);
            $read->setReadAt($readAt);

            $manager->persist($read);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    private function randomDate(string $from, string $to): \DateTimeImmutable
    {
        $start = (new \DateTime($from))->getTimestamp();
        $end = (new \DateTime($to))->getTimestamp();

        return new \DateTimeImmutable('@'.random_int(min($start, $end), max($start, $end)));
    }

    /** @return string[] */
    private function messagePool(): array
    {
        return [
            'Bonjour, je suis intéressé par vos services. Pouvez-vous me donner plus d\'informations ?',
            'Bonjour, j\'ai vu votre profil et je souhaiterais obtenir un devis.',
            'Bonsoir, êtes-vous disponible pour un travail dans ma région ?',
            'Bonjour, j\'aurais besoin de vos services rapidement. Comment puis-je vous contacter ?',
            'Bonjour ! Pouvez-vous m\'en dire plus sur votre expérience dans ce domaine ?',
            'Quel est votre tarif pour ce type de prestation ?',
            'Pouvez-vous m\'envoyer un devis détaillé dès que possible ?',
            'Je dispose d\'un budget de 3 000 €, est-ce que cela correspond à vos tarifs ?',
            'Votre devis me convient, quand pouvez-vous intervenir ?',
            'Le devis est un peu au-dessus de mon budget, y a-t-il une marge de négociation ?',
            'Merci pour votre devis, je vais le comparer avec d\'autres propositions.',
            'Parfait, votre devis est accepté. Quelles sont les prochaines étapes ?',
            'Êtes-vous disponible la semaine prochaine pour un premier état des lieux ?',
            'Je suis disponible le mardi ou le jeudi après-midi, ça vous conviendrait ?',
            'Pouvez-vous passer un samedi matin ? C\'est plus pratique pour moi.',
            'Quels sont vos délais pour démarrer les travaux ?',
            'Nous sommes d\'accord pour le 15 du mois prochain, c\'est noté de mon côté.',
            'Je dois malheureusement reporter notre rendez-vous, pouvez-vous proposer une autre date ?',
            'Il s\'agit d\'une maison de 120 m², les travaux concernent principalement le salon et la cuisine.',
            'Le chantier est situé en centre-ville, l\'accès est facile avec du stationnement disponible.',
            'Ce sont des travaux de rénovation d\'une salle de bain complète, baignoire, douche et WC.',
            'J\'ai besoin d\'une mise aux normes électriques pour une maison construite en 1975.',
            'Il faut refaire l\'isolation des combles, environ 80 m² à traiter.',
            'Le parquet de mon salon fait 40 m², il est à poncer et vitrifier.',
            'Ma toiture présente des infiltrations au niveau du faîtage, pouvez-vous venir l\'inspecter ?',
            'Je souhaite installer une pergola bioclimatique dans mon jardin, environ 20 m².',
            'Utilisez-vous des matériaux certifiés ou des marques spécifiques ?',
            'Le devis inclut-il la fourniture et la pose ?',
            'Y a-t-il des garanties sur vos travaux ? Décennale ? Parfait achèvement ?',
            'Intervenez-vous avec votre propre équipe ou faites-vous appel à des sous-traitants ?',
            'Avez-vous des références de chantiers similaires que je pourrais voir ?',
            'L\'installation sera-t-elle conforme aux normes en vigueur ?',
            'Tout se passe bien de notre côté, merci pour votre professionnalisme.',
            'Pouvez-vous me tenir informé de l\'avancement des travaux régulièrement ?',
            'Les livraisons de matériaux ont eu lieu ce matin, c\'est parfait.',
            'Il y a un petit souci sur la cloison nord, pouvez-vous y jeter un œil demain ?',
            'Super, la première phase est terminée. On peut attaquer la suite ?',
            'Je repasserai sur le chantier vendredi pour valider l\'avancement avec vous.',
            'Les travaux sont terminés, je suis très satisfait du résultat !',
            'Félicitations pour la qualité du travail, je n\'hésiterai pas à vous recommander.',
            'Quelques retouches seraient appréciées sur les finitions de la porte d\'entrée.',
            'La réception définitive est programmée pour vendredi, tout vous convient ?',
            'Je vous laisse un excellent avis sur la plateforme, c\'était un plaisir de travailler avec vous.',
            'Merci pour votre réponse rapide !',
            'Parfait, je reviens vers vous dès que j\'ai les informations nécessaires.',
            'Pas de souci, prenez le temps qu\'il vous faut.',
            'D\'accord, je note tout ça. À très bientôt !',
            'Avec plaisir, n\'hésitez pas si vous avez d\'autres questions.',
            'Je suis en déplacement jusqu\'à jeudi, je vous rappelle dès mon retour.',
            'Bonjour, avez-vous bien reçu les photos que je vous ai envoyées par mail ?',
            'Oui, j\'ai tout reçu. Je vous prépare un retour complet demain matin.',
            'Entendu, bonne journée et à bientôt !',
        ];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
