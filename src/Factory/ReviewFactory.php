<?php

namespace App\Factory;

use App\Model\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

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
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        // boucle jusqu'à ce que l'on trouve une combinaison unique
        do {
            $user = UserFactory::random();
            $videoGame = VideoGameFactory::random();

            // Vérification dans la base de données si la combinaison existe déjà
            $existingReview = $this->entityManager
                ->getRepository(Review::class)
                ->findOneBy([
                    'user' => $user->getId(),  // Utiliser l'ID du user
                    'videoGame' => $videoGame->getId(),  // Utiliser l'ID du jeu vidéo
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
