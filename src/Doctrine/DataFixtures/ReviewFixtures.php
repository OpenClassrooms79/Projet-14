<?php

namespace App\Doctrine\DataFixtures;

use App\Factory\ReviewFactory;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly RatingHandler $ratingHandler,
    ) {}

    public function load(ObjectManager $manager): void
    {
        // on récupère le nombre d'utilisateurs et de jeux vidéo
        $nbUsers = count($manager->getRepository(User::class)->findAll());
        $nbGames = count($manager->getRepository(VideoGame::class)->findAll());

        // nombre maximal de commentaires
        $maxReviews = $nbUsers * $nbGames;

        $nbReviews = min($maxReviews, 350);

        ReviewFactory::createMany($nbReviews);


        // Met à jour les statistiques de chaque jeu vidéo
        foreach ($manager->getRepository(VideoGame::class)->findAll() as $game) {
            $this->ratingHandler->calculateAverage($game);
            $this->ratingHandler->countRatingsPerValue($game);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            VideoGameFixtures::class,
        ];
    }
}
