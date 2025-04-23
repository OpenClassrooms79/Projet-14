<?php

namespace App\Doctrine\DataFixtures;

use App\Factory\TagFactory;
use App\Factory\VideoGameFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
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

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TagFixtures::class,
        ];
    }
}
