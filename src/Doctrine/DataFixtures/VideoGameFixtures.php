<?php

namespace App\Doctrine\DataFixtures;

use App\Factory\TagFactory;
use App\Factory\VideoGameFactory;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

use function array_fill_callback;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();

        /*$videoGames = array_fill_callback(
            0,
            50,
            fn(int $index): VideoGame
                => (new VideoGame)
                ->setTitle(sprintf('Jeu vidéo %d', $index))
                ->setDescription($this->faker->paragraphs(10, true))
                ->setReleaseDate(new DateTimeImmutable())
                ->setTest($this->faker->paragraphs(6, true))
                ->setRating(($index % 5) + 1)
                ->setImageName(sprintf('video_game_%d.png', $index))
                ->setImageSize(2_098_872),
        );*/
        VideoGameFactory::createMany(50, function (int $index) {
            return [
                'title' => sprintf('Jeu vidéo %d', $index),
                'description' => $this->faker->paragraphs(10, true),
                'releaseDate' => new \DateTimeImmutable(),
                'test' => $this->faker->paragraphs(6, true),
                'rating' => $this->faker->numberBetween(1, 5),
                'imageName' => sprintf('video_game_%d.png', $index),
                'imageSize' => 2_098_872,
                'tags' => TagFactory::randomRange(1, 8), // on associe 1 à 8 tags aléatoires
            ];
        });

        // TODO : Ajouter les tags aux vidéos

        //array_walk($videoGames, [$manager, 'persist']);

        $manager->flush();
        // TODO : Ajouter des reviews aux vidéos

    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TagFixtures::class,
        ];
    }
}
