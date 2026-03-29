<?php

namespace App\Controller;

use App\Dto\QuoteRequestDto;
use App\Entity\Conversation;
use App\Entity\ConversationParticipant;
use App\Entity\Message;
use App\Entity\User;
use App\Form\QuoteRequestType;
use App\Repository\ActivityRepository;
use App\Repository\ServicesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route(
        '/{codePostal}/{ville}/{slug}/devis-en-ligne',
        name: 'app_ask_quote',
        requirements: [
            'codePostal' => '\d{5}',
            'ville' => '[a-zA-ZÀ-ÿ\-]+',
            'slug' => '[a-zA-Z0-9\-]+'
        ],
        defaults: ['step' => 1],
    )]
    public function askQuote(
        string $slug,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {

        $artisan = $userRepository->findOneBy(['slug' => $slug]);

        if (!$artisan) {
            throw $this->createNotFoundException('Artisan introuvable.');
        }

        if (!in_array('ROLE_ARTISAN', $artisan->getRoles(), true)) {
            throw $this->createNotFoundException('Le profil demandé n’est pas un artisan.');
        }

        $quote = new QuoteRequestDto();
        $form = $this->createForm(QuoteRequestType::class, $quote);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {


            $customerEmail = $quote->getEmail();
            $customer = $userRepository->findOneBy(['email' => $customerEmail]);

            if (!$customer) {
                $plainPassword = bin2hex(random_bytes(12));

                $customer = new User();
                $customer->setEmail($customerEmail);
                $customer->setRoles(['ROLE_USER']);
                $customer->setIsVerified(true);
                $customer->setPassword($plainPassword);

                // Si ton DTO contient prénom / nom / téléphone
                if (method_exists($quote, 'getFirstName')) {
                    $customer->setFirstName($quote->getFirstName());
                }

                if (method_exists($quote, 'getLastName')) {
                    $customer->setLastName($quote->getLastName());
                }

                if (method_exists($quote, 'getPhone')) {
                    $customer->setPhoneNumber($quote->getPhone());
                }

                $entityManager->persist($customer);
                $entityManager->flush();
            }

            /**
             * Création ou récupération d'une conversation entre le client et l'artisan
             */
            $conversation = $entityManager
                ->getRepository(Conversation::class)
                ->createQueryBuilder('c')
                ->innerJoin('c.participants', 'cp1')
                ->innerJoin('cp1.user', 'u1')
                ->innerJoin('c.participants', 'cp2')
                ->innerJoin('cp2.user', 'u2')
                ->andWhere('u1.id = :customerId')
                ->andWhere('u2.id = :artisanId')
                ->setParameter('customerId', $customer->getId())
                ->setParameter('artisanId', $artisan->getId())
                ->getQuery()
                ->getOneOrNullResult()
            ;

            if (!$conversation) {
                $conversation = new Conversation();

                $participantCustomer = new ConversationParticipant();
                $participantCustomer->setConversation($conversation);
                $participantCustomer->setUser($customer);

                $participantArtisan = new ConversationParticipant();
                $participantArtisan->setConversation($conversation);
                $participantArtisan->setUser($artisan);

                $conversation->addParticipant($participantCustomer);
                $conversation->addParticipant($participantArtisan);

                $entityManager->persist($conversation);
                $entityManager->persist($participantCustomer);
                $entityManager->persist($participantArtisan);
            }

            /**
             * Création d’un premier message automatique à partir de la demande de devis
             */
            $messageContent = sprintf(
                "Nouvelle demande de devis\n\nNom : %s %s\nEmail : %s\nTéléphone : %s\n\nMessage :\n%s",
                method_exists($quote, 'getFirstName') ? $quote->getFirstName() : '',
                method_exists($quote, 'getLastName') ? $quote->getLastName() : '',
                $quote->getEmail(),
                method_exists($quote, 'getPhone') ? $quote->getPhone() : '',
                method_exists($quote, 'getMessage') ? $quote->getMessage() : ''
            );

            $message = new Message();
            $message->setConversation($conversation);
            $message->setSender($customer);
            $message->setContent(trim($messageContent));

            $conversation->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($message);

            /**
             * Envoi email à l'artisan
             */
            $artisanEmail = (new Email())
                ->from('contact@tonsite.com')
                ->to($artisan->getEmail())
                ->subject('Nouvelle demande de devis')
                ->html($this->renderView('emails/quote_request.html.twig', [
                    'quote' => $quote,
                    'artisan' => $artisan,
                    'customer' => $customer,
                ]));

            $mailer->send($artisanEmail);

            $entityManager->flush();

           

            return $this->redirectToRoute('app_ask_quote_success');
        }

        return $this->render('ask_quote/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/{codePostal}/{ville}/{slug}',
        name: 'app_company',
        requirements: [
            'codePostal' => '\d{5}',
            'ville' => '[a-zA-ZÀ-ÿ\-]+',
            'slug' => '[a-zA-Z0-9\-]+',
        ]
    )]
    public function showCompany(
        string $codePostal,
        string $ville,
        string $slug
    ): Response {
        $company = $this->userRepository->findOneBy([
            'slug' => $slug,
        ]);

        if (!$company) {
            throw $this->createNotFoundException('Entreprise introuvable.');
        }

        return $this->render('company/show.html.twig', [
            'item' => $company,
            'codePostal' => $codePostal,
            'ville' => $ville,
            'slug' => $slug,
        ]);
    }

    #[Route('/demande-devis/succes', name: 'app_ask_quote_success')]
    public function success(): Response
    {
        return $this->render('ask_quote/success.html.twig');
    }

    /**
     * Génère un mot de passe aléatoire sécurisé
     *
     * @param int $length La longueur du mot de passe à générer
     * @return string Le mot de passe généré
     */
    function generatePassword($length = 20)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}<>?';
    $password = '';
    $max = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $max)];
    }

    return $password;
}
}