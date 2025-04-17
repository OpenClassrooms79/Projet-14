<?php

namespace App\Tests;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

use function random_int;

class AverageRatingTest extends TestCase
{
    public function testAverageRatingNull(): void
    {
        $videoGame = new VideoGame();

        $ratingHandler = new RatingHandler();
        $ratingHandler->calculateAverage($videoGame);

        $this->assertEquals(null, $videoGame->getAverageRating());
    }

    public function testAverageRating(): void
    {
        $videoGame = new VideoGame();

        $videoGame->addReview((new Review())->setRating(3));
        $videoGame->addReview((new Review())->setRating(4));
        $videoGame->addReview((new Review())->setRating(5));

        $ratingHandler = new RatingHandler();
        $ratingHandler->calculateAverage($videoGame);

        $this->assertEquals(4.0, $videoGame->getAverageRating());
    }

    public function testAverageRatingRandom(): void
    {
        $videoGame = new VideoGame();

        $sum = 0;
        for ($i = 0; $i < 50; $i++) {
            $rating = random_int(1, 5);
            $videoGame->addReview((new Review())->setRating($rating));
            $sum += $rating;
        }

        $ratingHandler = new RatingHandler();
        $ratingHandler->calculateAverage($videoGame);

        $expectedRating = ceil($sum / 50);

        $this->assertEquals($expectedRating, $videoGame->getAverageRating());
    }
}
