<?php

namespace App\Tests\Controller;

use App\DataFixtures\CourseFixtures;
use App\DataFixtures\UserFixtures;
use App\Service\PaymentService;
use App\Tests\AbstractTest;

class BillingUserControllerTest extends AbstractTest
{

    protected function getFixtures(): array
    {
        $hasher = static::getContainer()->get('security.user_password_hasher');
        $paymentService = new PaymentService(self::getEntityManager());
        return [new CourseFixtures(), new UserFixtures($hasher, $paymentService)];
    }

    public function getToken(string $username, string $password): string
    {
        $client = static::getClient();

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

        //$client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $data['token'];

//        return $client;
    }

    /**
     * @dataProvider getAuthenticationDataSet
     */
    public function testResponseCode(int $expectedStatusCode, string $url, array $body): void
    {
        $client = static::getClient();

        $json_body = json_encode($body, JSON_THROW_ON_ERROR);

        $client->request(
            'POST',
            $url,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            $json_body
        );

        $status = $client->getResponse()->getStatusCode();

        $this->assertEquals($expectedStatusCode, $status);
    }

    public function getAuthenticationDataSet(): array
    {
        return [
            [200, '/api/v1/auth', ['username' => 'user@email.com', 'password' => 'qwerty']],
            [401, '/api/v1/auth', ['username' => 'bad_user@email.com', 'password' => 'qwerty']],
            [400, '/api/v1/auth', []],
            [201, '/api/v1/register', ['username' => 'new_user@email.com', 'password' => 'qwerty']],
            [401, '/api/v1/register', ['username' => 'user@email.com', 'password' => 'qwerty']],
            [401, '/api/v1/register', []],
//            [200, '/api/v1/users/current', [], ['HTTP_Authorization' => 'Bearer ' . $this->getToken('user@email.com', 'qwerty')]],
        ];
    }

    public function testCurrentUser(): void
    {
        $client = static::getClient();
        $email = "user@email.com";
        $token = $this->getToken($email, "qwerty");

        $client->request(
            'GET',
            "/api/v1/users/current",
            array(),
            array(),
            array('HTTP_Authorization' => "Bearer $token")
        );

        $status = $client->getResponse()->getStatusCode();

        $this->assertEquals(200, $status);

        $userData = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals($email, $userData['username']);
    }


    /**
     * @dataProvider coursesDataSet
     */
    public function testCourses($status, $urn): void
    {
        $client = static::getClient();
        $email = "user@email.com";
        $token = $this->getToken($email, "qwerty");

        $client->request(
            'POST',
            "/api/v1/courses/$urn",
            array(),
            array(),
            array('HTTP_Authorization' => "Bearer $token")
        );

        $this->assertResponseOk();

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals($status, $response['code']);
    }

    public function coursesDataSet(): array
    {
        return [
            [200, 'math/buy'],
            [400, 'db/buy'],
        ];
    }
}
