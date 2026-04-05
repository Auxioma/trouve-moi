<?php

namespace App\Controller\User\Quote;

use App\Entity\Message;
use App\Entity\Quote;
use App\Entity\QuoteFile;
use App\Entity\User;
use App\Form\Quote\QuotePdfType;
use App\Repository\ConversationRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CreateQuoteController extends AbstractController
{
    #[Route('/user/quote/create/{id}', name: 'app_user_quote_create_quote')]
    public function index(
        int $id,
        Request $request,
        ConversationRepository $conversationRepository,
        EntityManagerInterface $em
    ): Response {
        $conversation = $conversationRepository->find($id);

        if (!$conversation) {
            throw $this->createNotFoundException('Conversation introuvable.');
        }

        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $isParticipant = false;
        $clientUser = null;

        foreach ($conversation->getParticipants() as $participant) {
            $participantUser = $participant->getUser();

            if (!$participantUser instanceof User) {
                continue;
            }

            if ($participantUser->getId() === $user->getId()) {
                $isParticipant = true;
            } else {
                $clientUser = $participantUser;
            }
        }

        if (!$isParticipant) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à cette conversation.');
        }

        $quote = new Quote();
        $quote->setArtisan($user);
        $quote->setClientUser($clientUser);
        $quote->setConversation($conversation);

        if ($clientUser instanceof User) {
            $quote->setClientName($clientUser->getFirstName() ?? '');
            $quote->setClientEmail($clientUser->getEmail() ?? '');
            $quote->setClientPhone($clientUser->getPhoneNumber() ?? '');
            $quote->setClientAddress($clientUser->getAddress() ?? '');
        }

        $form = $this->createForm(QuotePdfType::class, $quote, [
            'action' => $this->generateUrl('app_user_quote_create_pdf_post', [
                'id' => $conversation->getId(),
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quote->touch();

            $em->persist($quote);
            $em->flush();

            return $this->redirectToRoute('app_user_quote_create_quote', [
                'id' => $conversation->getId(),
            ]);
        }

        return $this->render('user/quote/create_quote/index.html.twig', [
            'formQuotePdf' => $form->createView(),
            'conversation' => $conversation,
            'artisan' => $user,
            'clientUser' => $clientUser,
        ]);
    }

    #[Route('/user/quote/pdf/{id}', name: 'app_user_quote_create_pdf_post', methods: ['POST'])]
    public function createPdf(
        int $id,
        Request $request,
        ConversationRepository $conversationRepository,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $conversation = $conversationRepository->find($id);

        if (!$conversation) {
            throw $this->createNotFoundException('Conversation introuvable.');
        }

        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $isParticipant = false;
        $clientUser = null;

        foreach ($conversation->getParticipants() as $participant) {
            $participantUser = $participant->getUser();

            if (!$participantUser instanceof User) {
                continue;
            }

            if ($participantUser->getId() === $user->getId()) {
                $isParticipant = true;
            } else {
                $clientUser = $participantUser;
            }
        }

        if (!$isParticipant) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à cette conversation.');
        }

        $quote = new Quote();
        $quote->setArtisan($user);
        $quote->setClientUser($clientUser);
        $quote->setConversation($conversation);

        if ($clientUser instanceof User) {
            $quote->setClientName($clientUser->getFirstName() ?? '');
            $quote->setClientEmail($clientUser->getEmail() ?? '');
            $quote->setClientPhone($clientUser->getPhoneNumber() ?? '');
            $quote->setClientAddress($clientUser->getAddress() ?? '');
        }

        $form = $this->createForm(QuotePdfType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $pdfFile */
            $pdfFile = $form->get('attachment')->getData();

            if ($pdfFile instanceof UploadedFile) {
                $originalName = $pdfFile->getClientOriginalName();
                $fileSize = $pdfFile->getSize();
                $extension = $pdfFile->guessExtension() ?: 'pdf';

                $conversationId = (string) $conversation->getId();
                $parts = str_split($conversationId);

                $baseDir = $this->getParameter('kernel.project_dir') . '/public/devis';
                $relativeDir = implode('/', $parts);
                $targetDir = $baseDir . '/' . $relativeDir;

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0775, true);
                }

                $safeName = pathinfo($originalName, PATHINFO_FILENAME);
                $safeName = preg_replace('/[^A-Za-z0-9_-]/', '-', $safeName);

                $fileName = $safeName . '-' . uniqid() . '.' . $extension;

                $pdfFile->move($targetDir, $fileName);

                $relativePath = 'devis/' . $relativeDir . '/' . $fileName;
                $pdfUrl = $request->getSchemeAndHttpHost() . '/' . ltrim($relativePath, '/');

                $quote->setIsPdfUploaded(true);
                $quote->setIsPdfGenerated(false);
                $quote->setStatus(Quote::STATUS_SENT);
                $quote->setSentAt(new \DateTimeImmutable());
                $quote->touch();

                $quoteFile = new QuoteFile();
                $quoteFile->setQuote($quote);
                $quoteFile->setType(QuoteFile::TYPE_UPLOADED_PDF);
                $quoteFile->setOriginalName($originalName);
                $quoteFile->setFileName($fileName);
                $quoteFile->setFilePath($relativePath);
                $quoteFile->setFileSize($fileSize);

                $message = new Message();
                $message->setConversation($conversation);
                $message->setSender($user);
                $message->setContent(sprintf(
                    'Un devis PDF a été envoyé : <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
                    $pdfUrl,
                    htmlspecialchars($originalName, ENT_QUOTES, 'UTF-8')
                ));

                $em->persist($quote);
                $em->persist($quoteFile);
                $em->persist($message);
                $em->flush();

                if ($clientUser instanceof User && $clientUser->getEmail()) {
                    $messagerieUrl = $this->generateUrl(
                        'app_user_messagerie_show',
                        ['id' => $conversation->getId()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    $email = (new TemplatedEmail())
                        ->from('contact@trouvemoi.eu')
                        ->to($clientUser->getEmail())
                        ->subject('Nouveau message dans votre messagerie')
                        ->htmlTemplate('emails/new_message.html.twig')
                        ->context([
                            'client' => $clientUser,
                            'fileName' => $originalName,
                            'messagerieUrl' => $messagerieUrl,
                        ]);

                    try {
                        $mailer->send($email);
                    } catch (\Throwable $e) {
                        // Tu peux logger l'erreur ici si besoin
                    }
                }

                $this->addFlash('success', 'Le devis PDF a bien été envoyé dans la conversation.');

                return $this->redirectToRoute('app_user_messagerie_show', [
                    'id' => $conversation->getId(),
                ]);
            }

            $this->addFlash('error', 'Aucun fichier PDF n’a été envoyé.');
        }

        return $this->redirectToRoute('app_user_messagerie_show', [
            'id' => $conversation->getId(),
        ]);
    }

    #[Route('/user/quote', name: 'app_user_quote_create_list')]
    public function list(QuoteRepository $quoteRepository): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $quotes = $quoteRepository->findBy(
            ['artisan' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('user/quote/create_quote/list.html.twig', [
            'quotes' => $quotes,
            'artisan' => $user,
        ]);
    }
}