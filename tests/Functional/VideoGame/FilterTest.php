<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Factory\TagFactory;
use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    /**
     * @return iterable<array{tags: array<int>, expectedCount: int}>
     */
    public static function filterTagsDataProvider(): iterable
    {
        // les nombres correspondent à la position de chaque tag dans la liste des tags sur la page web
        return [
            ['tags' => [1], 'expectedCount' => 10],
            ['tags' => [2, 3], 'expectedCount' => 8],
            ['tags' => [4, 5, 6], 'expectedCount' => 6],
            ['tags' => [7, 8, 9, 10], 'expectedCount' => 4],
            ['tags' => [11, 12, 13, 14, 15], 'expectedCount' => 2],
        ];
    }

    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /**
     * @dataProvider filterTagsDataProvider
     *
     * @param array<int> $tags
     * @param int $expectedCount
     * @return void
     */
    public function testShouldFilterVideoGamesByTags(array $tags, int $expectedCount): void
    {
        // On récupère tous les tags disponibles
        $allTags = TagFactory::all();

        // Créer le tableau des ids réels en fonction des positions des tags
        $form_params = [];
        foreach ($tags as $tagIndex) {
            // $tagIndex correspond à la position du tag dans la liste de tags à l'écran (1 à 25)
            $realTag = $allTags[$tagIndex - 1]; // Récupère le tag réel basé sur la position (0-indexé dans le tableau)
            $form_params["filter[tags][" . $tagIndex . "]"] = ($realTag->getId() ?? 0) + 1;
        }

        $this->get('/');
        self::assertResponseIsSuccessful();
        $this->client->submitForm('Filtrer', $form_params, 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount($expectedCount, 'article.game-card');
    }
}
