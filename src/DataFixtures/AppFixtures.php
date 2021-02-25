<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $faker, $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->faker = Factory::create('fr_FR');
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        /**
         * Création des Users
         */
        for ($i = 0; $i < mt_rand(20, 20); $i++) {
            /** @var User */
            $user = new User();
            $user->setFirstName($this->faker->firstName)
                ->setEmail($this->faker->email)
                ->setPassword($this->encoder->encodePassword($user, 'password'))
                ->setLastName($this->faker->lastName);
            $manager->persist($user);

            /**
             * Création des Clients
             */
            for ($i = 0; $i < mt_rand(1, 50); $i++) {
                $customer = new Customer;
                $customer->setFirstName($this->faker->firstName())
                    ->setLastName($this->faker->lastName)
                    ->setCompany($this->faker->company)
                    ->setEmail($this->faker->email)
                    ->setUser($user);

                $manager->persist($customer);

                /**
                 * Création des factures
                 */
                for ($i = 0; $i < mt_rand(2, 50); $i++) {
                    $invoce = new Invoice;
                    $invoce
                        ->setAmount($this->faker->randomFloat(2, 53, 5000))
                        ->setSentAt($this->faker->dateTimeBetween('- 12 months'))
                        ->setStatus($this->faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($i + 1);

                    $manager->persist($invoce);
                }
            }
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
