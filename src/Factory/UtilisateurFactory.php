<?php

namespace App\Factory;

use App\Entity\Utilisateur;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Utilisateur>
 */
final class UtilisateurFactory extends PersistentProxyObjectFactory
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
        return Utilisateur::class;
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
            'email' => self::faker()->safeEmail(),
            'roles' => ['ROLE_ADMIN'], // Choisissez le rôle approprié (ROLE_ADMIN, ROLE_ENSEIGNANT, ROLE_ELEVE)
            'password' => self::faker()->password(3,6), // Remplacez par le mot de passe crypté
            'prenom' => self::faker()->firstName(),
            'nom' => self::faker()->lastName(),
            'DateDeNaissance' => self::faker()->dateTimeThisCentury(),
            'sexe' => self::faker()->randomElement(['M', 'F']),
            'adresse' => self::faker()->address(),
            'numeroTelephone' => self::faker()->phoneNumber(),
            'photo' => 'your-photo-url', // Remplacez par l'URL de la photo
            'setVerified' => true, // L'utilisateur est vérifié
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Utilisateur $utilisateur): void {})
        ;
    }
}
