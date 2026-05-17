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

namespace App\Controller\Dashboard\Visiteurs;

use App\Entity\Devis;
use App\Entity\DevisArtisan;
use App\Entity\DevisImage;
use App\Entity\Enum\DebutChantierEnum;
use App\Form\Visiteurs\Devis\CreationDevisType;
use App\Repository\ActivityRepository;
use App\Repository\DevisRepository;
use App\Repository\UserRepository;
use App\Service\Email\EmailPropositionDevisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/projects', name: 'dashboard-visiteurs_')]
#[IsGranted('ROLE_USER')]
final class MesProjectsController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ActivityRepository $activityRepository,
        private readonly EmailPropositionDevisService $EmailPropositionDevisService,
    ) {
    }

    #[Route('/', name: 'liste_projects')]
    public function index(): Response
    {
        $listDevis = $this->userRepository->findOneBy(['id' => $this->getUser()], ['createdAt' => 'DESC'])->getDevis();

        return $this->render('dashboard/user/mes_projects/liste-des-projects.html.twig', [
            'listDevis' => $listDevis,
        ]);
    }

    #[Route('/nouveau-projet', name: 'demarre_projects', methods: ['GET', 'POST'])]
    public function demarre(Request $request, EntityManagerInterface $entityManeger): Response
    {
        $user = $this->getUser();

        $devis = new Devis();

        $form = $this->createForm(CreationDevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $creationDevis = $request->request->all('creation_devis');
            $photo = $request->files->all('creation_devis')['photos'] ?? null;

            $metierPrincipal = $creationDevis['metierPrincipal'] ?? null;
            $autreMetier = $creationDevis['autreMetier'] ?? null;

            if (empty($metierPrincipal) && empty($autreMetier)) {
                $this->addFlash('error', 'Veuillez sélectionner ou saisir un métier.');

                return $this->redirect($request->headers->get('referer'));
            }

            if (!empty($metierPrincipal)) {
                $metier = $creationDevis['metierPrincipal'];
            } else {
                $metier = $creationDevis['autreMetier'];
            }

            $findMetier = $this->activityRepository->find($metier);

            $devis->setTitre($creationDevis['titre']);
            $devis->setDescription($creationDevis['description']);
            $devis->setSurface($creationDevis['surface']);
            $devis->setBudget($creationDevis['budget']);
            $devis->setVisiteur($user);
            $devis->setDebutChantier(DebutChantierEnum::from($creationDevis['debutChantier']));
            $devis->setMetier($findMetier);

            $photos = $form->get('photos')->getData();
            foreach ($photos as $key => $photo) {
                $imageDevis = new DevisImage();
                $imageDevis->setImageFile($photo);
                $devis->addDevisImage($imageDevis);
                $entityManeger->persist($imageDevis);
            }
            $entityManeger->persist($devis);

            if ($request->query->get('slug')) {
                $findArtisan = $this->userRepository->findOneBy(['slug' => $request->query->get('slug')]);
                $sendDevis = new DevisArtisan();
                $sendDevis->setDevis($devis);
                $sendDevis->setArtisan($findArtisan);
                $sendDevis->setStatus('envoye');
                $sendDevis->setSendAt(new \DateTimeImmutable());
                $entityManeger->persist($sendDevis);

                $entityManeger->flush();

                $this->EmailPropositionDevisService->send($findArtisan);

                $this->addFlash('success', 'Votre projet a été créé et envoyé à l\'artisan sélectionné.');

                return $this->redirectToRoute('dashboard-visiteurs_liste_projects');
            }
            /* je vais faire une requete pour avoir les 4 meilleurs notes et max avis */
            $trouve4artisantparAvis = $this->userRepository->findTopArtisansByActivity($metier);
            foreach ($trouve4artisantparAvis as $artisan) {
                $findArtisan = $this->userRepository->find($artisan['id']);
                $sendDevis = new DevisArtisan();
                $sendDevis->setDevis($devis);
                $sendDevis->setArtisan($findArtisan);
                $sendDevis->setStatus('envoye');
                $sendDevis->setSendAt(new \DateTimeImmutable());
                $entityManeger->persist($sendDevis);

                $entityManeger->flush();

                $this->EmailPropositionDevisService->multipleArtisat(
                    $findArtisan->getEmail()
                );

                $this->addFlash('success', 'Votre projet a été créé et envoyé aux meilleurs artisans.');

                return $this->redirectToRoute('dashboard-visiteurs_liste_projects');
            }
        }

        return $this->render('dashboard/user/mes_projects/demarre-un-projet.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/nouveau-projet/recapitulatif', name: 'recapitulatif', methods: ['GET'])]
    public function recapitulatif(): Response
    {
        return $this->render('user/mes_projects/recapitulatif.html.twig');
    }

    #[Route('/nouveau-projet/recapitulatif/avis', name: 'avis')]
    public function avis(): Response
    {
        return $this->render('user/mes_projects/avis.html.twig');
    }
}
