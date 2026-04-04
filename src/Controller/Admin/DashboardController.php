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

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        $url = $adminUrlGenerator
            ->setController(UserCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span style="font-weight:700;">Trouve-moi</span> Admin')
            ->renderContentMaximized();
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('admin/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fas fa-gauge-high');

        yield MenuItem::section('Administration');

        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fas fa-users');
        yield MenuItem::linkTo(ServicesCrudController::class, 'Services', 'fas fa-briefcase');
        yield MenuItem::linkTo(ActivityCrudController::class, 'Activités', 'fas fa-layer-group');

        yield MenuItem::section('Offres & paiements');

        yield MenuItem::linkTo(PlanCrudController::class, 'Les forfaits', 'fas fa-box-open');
        yield MenuItem::linkTo(PaymentCrudController::class, 'Paiements', 'fas fa-credit-card');
    }
}
