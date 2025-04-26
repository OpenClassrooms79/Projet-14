<?php

namespace App\Factory;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Review>
 */
final class ReviewFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Review::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        // boucle jusqu'à ce que l'on trouve une combinaison unique
        do {
            $user = UserFactory::random();
            $videoGame = VideoGameFactory::random();

            // Vérification dans la base de données si la combinaison existe déjà
            $existingReview = $this->entityManager
                ->getRepository(Review::class)
                ->findOneBy([
                    'user' => $user->getId(),
                    'videoGame' => $videoGame->getId(),
                ]);
        } while ($existingReview); // tant qu'on trouve une combinaison existante, on recommence

        // générer le reste des données
        return [
            'user' => $user,
            'videoGame' => $videoGame,
            'rating' => self::faker()->numberBetween(1, 5),
            'comment' => self::faker()->paragraphs(1, true),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Review $review): void {})
            ;
    }
}
