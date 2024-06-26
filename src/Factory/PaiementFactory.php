<?php

namespace App\Factory;

use App\Entity\Paiement;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Paiement>
 */
final class PaiementFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Paiement::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'modePaiement' => self::faker()->randomElement(['VISA', 'GUICHET', 'PAYPAL', 'MOBILE_MONEY']),
            'montant' => self::faker()->randomFloat(2, 80, 100),
            'status' => self::faker()->randomElement(['EN ORDRE', 'INSOLVABLE']),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Paiement $paiement): void {})
        ;
    }
}
