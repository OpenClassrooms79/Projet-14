<?php

namespace App\Doctrine\DataFixtures;

use App\Factory\TagFactory;
use App\Factory\VideoGameFactory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

use function count;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $allTags = TagFactory::all();

        VideoGameFactory::createMany(50, function (int $index) use ($allTags) {
            // sélectionner 5 tags consécutifs à associer à ce jeu vidéo (pour pouvoir tester avec phpunit)
            $tags = [
                $allTags[($index - 1) % count($allTags)],
                $allTags[$index % count($allTags)],
                $allTags[($index + 1) % count($allTags)],
                $allTags[($index + 2) % count($allTags)],
                $allTags[($index + 3) % count($allTags)],
            ];

            return [
                'title' => sprintf('Jeu vidéo %d', $index),
                'description' => $this->faker->paragraphs(10, true),
                'releaseDate' => new DateTimeImmutable(),
                'test' => $this->faker->paragraphs(6, true),
                'rating' => $this->faker->numberBetween(1, 5),
                'imageName' => sprintf('video_game_%d.png', $index),
                'imageSize' => 2_098_872,
                'tags' => $tags,
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
