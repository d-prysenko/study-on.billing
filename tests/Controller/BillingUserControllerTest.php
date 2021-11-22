<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Tests\AbstractTest;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BillingUserControllerTest extends AbstractTest
{

    protected function getFixtures(): array
    {
        $hasher = static::$container->get('security.user_password_hasher');
        return [new UserFixtures($hasher)];
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
    public function testJsonRequest(int $expectedStatusCode, string $url, array $body): void
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
        $contentType = $client->getResponse()->headers->get('content-type');

        $info = 'status: ' . $status . "\n";
        $info .= 'content-type: ' . $contentType;
        if ($contentType === "application/json")
        {
            $info .= "content:\n" . $client->getResponse()->getContent();
        }
//        dd($info);

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
        $contentType = $client->getResponse()->headers->get('content-type');

        $info = 'status: ' . $status . "\n";
        $info .= 'content-type: ' . $contentType;
        if ($contentType === "application/json")
        {
            $info .= "content:\n" . $client->getResponse()->getContent();
        }
        //dd($info);

        $this->assertEquals(200, $status);

        $userData = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals($email, $userData['username']);
    }
}
