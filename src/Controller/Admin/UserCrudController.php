<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Enum\UserProfileStatus;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private RequestStack $requestStack,
        private UploaderHelper $uploaderHelper
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des utilisateurs')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un utilisateur')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un utilisateur')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Fiche utilisateur')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields([
                'email',
                'firstName',
                'lastName',
                'compagny',
                'phoneNumber',
                'siren',
                'city',
            ])
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        $showAll = Action::new('showAll', 'Afficher tout')
            ->linkToUrl(
                $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction(Action::INDEX)
                    ->unset('roleFilter')
                    ->generateUrl()
            )
            ->createAsGlobalAction();

        $showArtisans = Action::new('showArtisans', 'Afficher artisans')
            ->linkToUrl(
                $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction(Action::INDEX)
                    ->set('roleFilter', 'artisan')
                    ->generateUrl()
            )
            ->createAsGlobalAction();

        $showUsers = Action::new('showUsers', 'Afficher utilisateurs')
            ->linkToUrl(
                $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction(Action::INDEX)
                    ->set('roleFilter', 'user')
                    ->generateUrl()
            )
            ->createAsGlobalAction();

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $showAll)
            ->add(Crud::PAGE_INDEX, $showArtisans)
            ->add(Crud::PAGE_INDEX, $showUsers)
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $action) => $action->setLabel('Ajouter'))
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $action) => $action->setLabel('Voir'))
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $action) => $action->setLabel('Modifier'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $action) => $action->setLabel('Supprimer'));
    }

    public function configureFilters(Filters $filters): Filters
    {
        $choices = [];
        foreach (UserProfileStatus::cases() as $case) {
            $choices[$this->formatStatus($case)] = $case->value;
        }

        return $filters
            ->add(BooleanFilter::new('isVerified', 'Compte vérifié'))
            ->add(ChoiceFilter::new('profileStatus', 'Statut du profil')->setChoices($choices))
            ->add(DateTimeFilter::new('lastLogin', 'Dernière connexion'));
    }

    public function configureFields(string $pageName): iterable
    {
        $choices = [];
        foreach (UserProfileStatus::cases() as $case) {
            $choices[$this->formatStatus($case)] = $case;
        }

        // INDEX
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield TextField::new('imageName', 'Logo')
            ->formatValue(function ($value, User $user) {
                $url = $this->uploaderHelper->asset($user, 'imageFile');

                if (!$url) {
                    return '<span style="color:#999;">—</span>';
                }

                return sprintf(
                    '<img src="%s" alt="Logo" style="height:56px;width:56px;object-fit:cover;border-radius:14px;border:1px solid #e5e7eb;">',
                    $url
                );
            })
            ->renderAsHtml()
            ->onlyOnIndex();

        yield EmailField::new('email', 'Email')->onlyOnIndex();
        yield TextField::new('compagny', 'Entreprise')->onlyOnIndex();
        yield TextField::new('city', 'Ville')->onlyOnIndex();

        yield ChoiceField::new('profileStatus', 'Statut')
            ->setChoices($choices)
            ->onlyOnIndex();

        yield BooleanField::new('isVerified', 'Vérifié')->onlyOnIndex();
        yield DateTimeField::new('lastLogin', 'Dernière connexion')->onlyOnIndex();

        // FORM
        yield FormField::addTab('Compte')->onlyOnForms();

        yield EmailField::new('email', 'Email')
            ->onlyOnForms()
            ->setColumns(6);

        yield BooleanField::new('isVerified', 'Compte vérifié')
            ->onlyOnForms()
            ->setColumns(3);

        yield ChoiceField::new('roles', 'Rôles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Artisan' => 'ROLE_ARTISAN',
                'Admin' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->onlyOnForms()
            ->setColumns(3);

        yield ChoiceField::new('profileStatus', 'Statut profil')
            ->setChoices($choices)
            ->onlyOnForms()
            ->setColumns(4);

        yield FormField::addTab('Identité')->onlyOnForms();

        yield TextField::new('firstName', 'Prénom')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('lastName', 'Nom')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('phoneNumber', 'Téléphone')
            ->onlyOnForms()
            ->setColumns(6);

        yield FormField::addTab('Entreprise')->onlyOnForms();

        yield TextField::new('compagny', 'Entreprise')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('siren', 'SIREN')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('website', 'Site web')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('address', 'Adresse')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('postalCode', 'Code postal')
            ->onlyOnForms()
            ->setColumns(3);

        yield TextField::new('city', 'Ville')
            ->onlyOnForms()
            ->setColumns(3);

        yield TextField::new('latitude', 'Latitude')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('longitude', 'Longitude')
            ->onlyOnForms()
            ->setColumns(6);

        yield FormField::addTab('Présentation')->onlyOnForms();

        yield TextareaField::new('description', 'Description courte')
            ->onlyOnForms();

        yield TextareaField::new('grandeDescription', 'Grande description')
            ->onlyOnForms();

        yield FormField::addTab('Logo')->onlyOnForms();

        yield Field::new('imageFile', 'Logo')
            ->setFormType(VichImageType::class)
            ->onlyOnForms()
            ->setColumns(12);

        // DETAIL
        yield FormField::addTab('Informations')->onlyOnDetail();

        yield IdField::new('id', 'ID')->onlyOnDetail();

        yield TextField::new('imageName', 'Logo')
            ->formatValue(function ($value, User $user) {
                $url = $this->uploaderHelper->asset($user, 'imageFile');

                if (!$url) {
                    return '<span style="color:#999;">Aucun logo</span>';
                }

                return sprintf(
                    '<img src="%s" alt="Logo" style="max-height:140px;border-radius:18px;border:1px solid #e5e7eb;">',
                    $url
                );
            })
            ->renderAsHtml()
            ->onlyOnDetail();

        yield EmailField::new('email', 'Email')->onlyOnDetail();
        yield TextField::new('firstName', 'Prénom')->onlyOnDetail();
        yield TextField::new('lastName', 'Nom')->onlyOnDetail();
        yield TextField::new('phoneNumber', 'Téléphone')->onlyOnDetail();
        yield TextField::new('compagny', 'Entreprise')->onlyOnDetail();
        yield TextField::new('siren', 'SIREN')->onlyOnDetail();
        yield TextField::new('website', 'Site web')->onlyOnDetail();
        yield TextField::new('address', 'Adresse')->onlyOnDetail();
        yield TextField::new('postalCode', 'Code postal')->onlyOnDetail();
        yield TextField::new('city', 'Ville')->onlyOnDetail();
        yield TextField::new('latitude', 'Latitude')->onlyOnDetail();
        yield TextField::new('longitude', 'Longitude')->onlyOnDetail();

        yield ChoiceField::new('roles', 'Rôles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Artisan' => 'ROLE_ARTISAN',
                'Admin' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->onlyOnDetail();

        yield ChoiceField::new('profileStatus', 'Statut')
            ->setChoices($choices)
            ->onlyOnDetail();

        yield BooleanField::new('isVerified', 'Compte vérifié')->onlyOnDetail();
        yield TextareaField::new('description', 'Description courte')->onlyOnDetail();
        yield TextareaField::new('grandeDescription', 'Grande description')->onlyOnDetail();
        yield TextField::new('imageName', 'Nom du logo')->onlyOnDetail();
        yield DateTimeField::new('lastLogin', 'Dernière connexion')->onlyOnDetail();
        yield DateTimeField::new('updatedAt', 'Mis à jour le')->onlyOnDetail();
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = $this->container
            ->get(EntityRepository::class)
            ->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $request = $this->requestStack->getCurrentRequest();
        $roleFilter = $request?->query->get('roleFilter');

        if ($roleFilter === 'artisan') {
            $qb->andWhere('entity.roles LIKE :artisanRole')
                ->setParameter('artisanRole', '%ROLE_ARTISAN%');
        }

        if ($roleFilter === 'user') {
            $qb->andWhere('entity.roles LIKE :userRole')
                ->setParameter('userRole', '%ROLE_USER%')
                ->andWhere('entity.roles NOT LIKE :artisanRole')
                ->setParameter('artisanRole', '%ROLE_ARTISAN%');
        }

        return $qb;
    }

    private function formatStatus(UserProfileStatus $status): string
    {
        return match ($status) {
            UserProfileStatus::PARTIAL => 'Profil partiel',
            UserProfileStatus::VALIDATED => 'Profil validé',
            UserProfileStatus::BANNED => 'Profil banni',
        };
    }
}