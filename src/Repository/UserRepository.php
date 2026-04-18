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

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findLatestArtisans(int $limit = 10): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.isVerified = :verified')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('verified', true)
            ->setParameter('role', '%ROLE_ARTISAN%')
            ->orderBy('u.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findDistinctCitiesByTerm(string $term): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.city AS city, MIN(u.latitude) AS latitude, MIN(u.longitude) AS longitude')
            ->andWhere('u.city IS NOT NULL')
            ->andWhere('u.latitude IS NOT NULL')
            ->andWhere('u.longitude IS NOT NULL')
            ->andWhere('LOWER(u.city) LIKE LOWER(:term)')
            ->setParameter('term', '%'.$term.'%')
            ->groupBy('u.city')
            ->orderBy('u.city', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult();
    }

    public function findByActivityAndCity($activity, $city, $latitude, $longitude): array
    {
        $qb = $this->createQueryBuilder('u')
            // ->andWhere('u.isVerified = :verified')
            // ->setParameter('verified', true)
        ;

        if (!empty($activity)) {
            $qb->leftJoin('u.activity', 'a')
                ->andWhere('LOWER(a.name) = LOWER(:activity)')
                ->setParameter('activity', mb_trim($activity));
        }

        if ($latitude && $longitude) {
            $qb->addSelect(
                '(6371 * acos(
                        cos(radians(:lat)) 
                        * cos(radians(u.latitude)) 
                        * cos(radians(u.longitude) - radians(:lng)) 
                        + sin(radians(:lat)) 
                        * sin(radians(u.latitude))
                    )) AS HIDDEN distance'
            )
            ->having('distance <= :radius')
            ->setParameter('lat', $latitude)
            ->setParameter('lng', $longitude)
            ->setParameter('radius', 50)
            ->orderBy('distance', 'ASC');
        }

        return $qb->getQuery()->getResult();
    }
}
