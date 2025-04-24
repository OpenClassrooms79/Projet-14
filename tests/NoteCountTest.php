<?php

namespace App\Tests;

use App\Model\Entity\NumberOfRatingsPerValue;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class NoteCountTest extends TestCase
{
    public function testRatingsCountByValue(): void
    {
        $videoGame = new VideoGame();

        $expectedNumberOfRatingsPerValue = new NumberOfRatingsPerValue();

        for ($i = 0; $i < 100; $i++) {
            $rating = random_int(1, 5);
            switch ($rating) {
                case 1:
                    $expectedNumberOfRatingsPerValue->increaseOne();
                    break;
                case 2:
                    $expectedNumberOfRatingsPerValue->increaseTwo();
                    break;
                case 3:
                    $expectedNumberOfRatingsPerValue->increaseThree();
                    break;
                case 4:
                    $expectedNumberOfRatingsPerValue->increaseFour();
                    break;
                case 5:
                    $expectedNumberOfRatingsPerValue->increaseFive();
                    break;
            }
            $videoGame->addReview((new Review())->setRating($rating));
        }

        $ratingHandler = new RatingHandler();
        $ratingHandler->countRatingsPerValue($videoGame);

        self::assertEquals($expectedNumberOfRatingsPerValue, $videoGame->getNumberOfRatingsPerValue());
    }
}
