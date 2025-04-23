<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    public static function filterTagsDataProvider(): array
    {
        return [
            ['tags' => [], 'expectedCount' => 10],
            ['tags' => [36], 'expectedCount' => 9],
            ['tags' => [24, 31], 'expectedCount' => 0],
            ['tags' => [19, 42], 'expectedCount' => 1],
            ['tags' => [25, 58], 'expectedCount' => 3],
            ['tags' => [13, 27, 51], 'expectedCount' => 1],
            /*['tags' => [1000], 'expectedCount' => 0],*/
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
     * @return void
     */
    public function testShouldFilterVideoGamesByTags(array $tags, int $expectedCount): void
    {
        $tags2 = [];
        foreach ($tags as $tag) {
            $idx = $tag - 1;
            $tags2["filter[tags][$idx]"] = $tag;
        }
        //print_r($tags2);
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', $tags2, 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount($expectedCount, 'article.game-card');
    }
}
