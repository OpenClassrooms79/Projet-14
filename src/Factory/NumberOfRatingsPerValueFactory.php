<?php

namespace App\Factory;

use App\Model\Entity\NumberOfRatingsPerValue;
use Zenstruck\Foundry\ObjectFactory;

/**
 * @extends ObjectFactory<NumberOfRatingsPerValue>
 */
final class NumberOfRatingsPerValueFactory extends ObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return NumberOfRatingsPerValue::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(NumberOfRatingsPerValue $numberOfRatingsPerValue): void {})
            ;
    }
}
