<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Enum\UserProfileStatus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /** @var Activity[] $activities */
        $activities = $manager->getRepository(Activity::class)->findAll();

        if ([] === $activities) {
            throw new \RuntimeException('Aucune activité trouvée. Vérifie ActivityFixtures.');
        }

        $passwordHolder = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($passwordHolder, 'admin');

        // User fixe
        $adminActivity = $activities[array_rand($activities)];
        $adminServices = $adminActivity->getServices()->toArray();

        $admin = new User();
        $admin->setEmail('admin@admin.admin');
        $admin->setRoles(['ROLE_ARTISAN']);
        $admin->setPassword($hashedPassword);
        $admin->setIsVerified(true);
        $admin->setFirstName('Guillaume');
        $admin->setLastName('Devaux');
        $admin->setCompagny('Auxioma Web Agency');
        $admin->setPhoneNumber('0601020304');
        $admin->setSiren('123456789');
        $admin->setAddress('10 rue de Paris');
        $admin->setPostalCode('76600');
        $admin->setCity('Le Havre');
        $admin->setLatitude('49.4944');
        $admin->setLongitude('0.1079');
        $admin->setProfileStatus(UserProfileStatus::PARTIAL);
        $admin->setActivity($adminActivity);
        $admin->setDescription('Agence web spécialisée dans la création de sites internet, le développement sur mesure et l’accompagnement digital.');
        $admin->setWebsite('https://www.auxioma.fr');
        $admin->setUpdatedAt(new \DateTimeImmutable());

        shuffle($adminServices);
        foreach (array_slice($adminServices, 0, min(2, count($adminServices))) as $service) {
            $admin->addService($service);
        }

        $manager->persist($admin);

        // 1000 artisans
        for ($i = 1; $i <= 1000; $i++) {
            $user = $this->createArtisan($faker, $activities, $hashedPassword);
            $manager->persist($user);

            if ($i % 100 === 0) {
                $manager->flush();
                $manager->clear();

                /** @var Activity[] $activities */
                $activities = $manager->getRepository(Activity::class)->findAll();
            }
        }

        $manager->flush();
    }

    /**
     * @param Activity[] $activities
     */
    private function createArtisan(
        Generator $faker,
        array $activities,
        string $hashedPassword
    ): User {
        $user = new User();

        $firstName = $faker->firstName();
        $lastName = $faker->lastName();

        /** @var Activity $activity */
        $activity = $activities[array_rand($activities)];
        $services = $activity->getServices()->toArray();

        $user->setEmail($faker->unique()->safeEmail());
        $user->setRoles(['ROLE_ARTISAN']);
        $user->setPassword($hashedPassword);
        $user->setIsVerified($faker->boolean(90));
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setCompagny($this->generateCompanyName($faker, $lastName));
        $user->setPhoneNumber($this->generateFrenchPhoneNumber($faker));
        $user->setSiren($faker->unique()->numerify('#########'));
        $user->setAddress($faker->streetAddress());
        $user->setPostalCode($faker->postcode());
        $user->setCity($faker->city());
        $user->setLatitude((string) $faker->latitude(41.0, 51.5));
        $user->setLongitude((string) $faker->longitude(-5.5, 9.5));
        $user->setProfileStatus(UserProfileStatus::PARTIAL);
        $user->setActivity($activity);
        $user->setDescription($this->generateDescription($faker, $activity->getName() ?? 'artisanat'));
        $user->setWebsite('https://www.' . $faker->slug(2) . '.fr');
        $user->setUpdatedAt(
            \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 year', 'now')
            )
        );

        shuffle($services);

        $selectedServices = array_slice(
            $services,
            0,
            min(random_int(1, 3), count($services))
        );

        foreach ($selectedServices as $service) {
            $user->addService($service);
        }

        return $user;
    }

    private function generateCompanyName(Generator $faker, string $lastName): string
    {
        return sprintf(
            '%s %s',
            $faker->randomElement([
                'Atelier',
                'Entreprise',
                'Société',
                'Services',
                'Artisan',
                'Habitat',
                'Rénovation',
                'Bâtiment',
                'Concept',
            ]),
            $lastName
        );
    }

    private function generateFrenchPhoneNumber(Generator $faker): string
    {
        return $faker->numerify('06########');
    }

    private function generateDescription(Generator $faker, string $activityName): string
    {
        return sprintf(
            '%s expérimenté dans le domaine de %s. %s',
            $faker->randomElement([
                'Artisan',
                'Professionnel',
                'Spécialiste',
                'Entreprise locale',
            ]),
            mb_strtolower($activityName),
            $faker->paragraphs(2, true)
        );
    }

    public function getDependencies(): array
    {
        return [
            ActivityFixtures::class,
        ];
    }
}