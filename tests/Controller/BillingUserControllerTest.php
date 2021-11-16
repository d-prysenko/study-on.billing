<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Tests\AbstractTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class BillingUserControllerTest extends AbstractTest
{

    protected function getFixtures(): array
    {
        return [new UserFixtures()];
    }

    public function testCreateAuthenticatedClient(): void
    {
        $client = static::getClient();
        $username = 'user@email.com';
        $password = 'qwerty';
        $client->request(
            'POST',
            '/api/v1/auth',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'username' => $username,
                'password' => $password,
            ), JSON_THROW_ON_ERROR)
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('token', $data, "Given response\n" . var_export($data, true));


        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));


//        return $client;
    }

//    public function testCurrentUser(): void
//    {
////        $client = $this->createAuthenticatedClient();
////        dd($client);
//    }
}
