<?php

namespace App\Tests;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function date;
use function sprintf;

class VideoGameRatingTest extends WebTestCase
{
    /**
     * Teste le bon fonctionnement du formulaire d'authentification
     *
     * @return void
     */
    public function testRealLogin(): void
    {
        $client = static::createClient();

        // aller sur la page d'authentification, remplir et soumettre le formulaire
        $crawler = $client->request('GET', '/auth/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'user+0@email.com',
            'password' => 'password',
        ]);
        $client->submit($form);
        self::assertResponseRedirects('/');
    }

    /**
     * Teste l'ajout d'une note et d'un commentaire pour un jeu video
     *
     * @return void
     */
    public function testAddReview(): void
    {
        $client = static::createClient();

        // Trouver un couple (user, game) sans review
        $userAndGame = $this->getRandomUserAndGameWithoutReview();

        if ($userAndGame === null) {
            $this->fail('Aucun couple (user, game) sans review trouvé.');
        }
        $user = $userAndGame['user'];
        $game = $userAndGame['game'];

        // login automatique
        $client->loginUser($user);

        $comment = sprintf('Super jeu ! (%s)', date('Y-m-d H:i:s'));
        $rating = random_int(1, 5);;

        $this->addReview($client, $user, $game, $comment, $rating);
        // Vérifier que le commentaire a bien été ajouté dans la base de données
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $newReview = $em->getRepository(Review::class)->findOneBy([
            'comment' => $comment,
        ]);
    }

    /**
     * Teste que le formulaire d'ajout de note n'est pas affiché pour les utilisateurs non authentifiés
     *
     * @return void
     */
    public function testRatingFormNotDisplayedIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/jeu-video-7');
        self::assertSelectorNotExists('FORM[name=review]');
    }

    public function addReview(KernelBrowser $client, User $user, VideoGame $videoGame, string $comment, int $rating): void
    {
        // aller sur la page d'un jeu vidéo
        $crawler = $client->request('GET', sprintf('/%s', $videoGame->getSlug()));

        // vérifier dans le code de la page la présence du formulaire d'ajout de note
        self::assertSelectorExists('FORM[name=review]');

        $button = $crawler->selectButton('Poster');
        if ($button->count() === 0) {
            throw new RuntimeException("Le bouton 'Poster' n'a pas été trouvé !");
        }

        // remplir et soumettre le formulaire d’ajout de commentaire et de note
        $form = $crawler->selectButton('Poster')->form([
            'review[rating]' => $rating,
            'review[comment]' => $comment,
        ]);
        $client->submit($form);

        // vérifier que la redirection fonctionne
        self::assertResponseRedirects();
        $client->followRedirect();


        // Vérifier que le commentaire a bien été ajouté dans la base de données
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $newReview = $em->getRepository(Review::class)->findOneBy([
            'comment' => $comment,
        ]);

        self::assertNotNull($newReview, "Le commentaire n'a pas été trouvé dans la base de données.");
        self::assertEquals($user, $newReview->getUser(), "L'utilisateur associé au commentaire est incorrect.");
        self::assertEquals($videoGame->getId(), $newReview->getVideoGame()->getId(), "Le jeu vidéo associé au commentaire est incorrect.");
        self::assertEquals($comment, $newReview->getComment(), 'Le commentaire est incorrect.');
        self::assertEquals($rating, $newReview->getRating(), 'La note du commentaire est incorrecte.');

        // vérifier que le nouveau commentaire s’affiche sur la page
        self::assertSelectorTextContains('.d-flex.flex-column', $comment);

        // vérifier que le formulaire n'est plus affiché pour cet utilisateur
        self::assertSelectorNotExists('FORM[name=review]');
    }

    /**
     * Récupère une paire (user, game) qui n'a pas encore de review associée
     */
    private function getRandomUserAndGameWithoutReview(): ?array
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Créer un QueryBuilder pour l'entité User
        $qb = $em->createQueryBuilder();

        // Sélectionner un utilisateur et un jeu vidéo sans review associée
        $qb
            ->select('u', 'vg')
            ->from(User::class, 'u')  // À partir de l'entité User
            ->innerJoin(VideoGame::class, 'vg', 'WITH', 'vg.id IS NOT NULL')  // Joindre VideoGame
            ->leftJoin(Review::class, 'r', 'WITH', 'r.user = u AND r.videoGame = vg')  // Joindre Review
            ->where('r.id IS NULL')  // Aucune review associée
            ->setMaxResults(1);  // Limiter à une seule paire (user, game)

        // Exécuter la requête et récupérer un seul résultat
        $userGamePairs = $qb->getQuery()->getResult();

        if (empty($userGamePairs)) {
            return null;  // Aucun résultat trouvé
        }

        return [
            'user' => $userGamePairs[0],
            'game' => $userGamePairs[1],
        ];
    }
}
