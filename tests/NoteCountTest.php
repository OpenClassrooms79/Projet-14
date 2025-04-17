<?php

namespace App\Tests;

use App\Model\Entity\NumberOfRatingPerValue;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class NoteCountTest extends TestCase
{
    public function testRatingsCountByValue(): void
    {
        $videoGame = new VideoGame();

        $expectedNumberOfRatingPerValue = new NumberOfRatingPerValue();

        for ($i = 0; $i < 100; $i++) {
            $rating = random_int(1, 5);
            switch ($rating) {
                case 1:
                    $expectedNumberOfRatingPerValue->increaseOne();
                    break;
                case 2:
                    $expectedNumberOfRatingPerValue->increaseTwo();
                    break;
                case 3:
                    $expectedNumberOfRatingPerValue->increaseThree();
                    break;
                case 4:
                    $expectedNumberOfRatingPerValue->increaseFour();
                    break;
                case 5:
                    $expectedNumberOfRatingPerValue->increaseFive();
                    break;
            }
            $videoGame->addReview((new Review())->setRating($rating));
        }

        $ratingHandler = new RatingHandler();
        $ratingHandler->countRatingsPerValue($videoGame);

        self::assertEquals($expectedNumberOfRatingPerValue, $videoGame->getNumberOfRatingsPerValue());
    }
}
