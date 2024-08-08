<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    // Test case to add a new user via API
    public function testAddNewUser()
    {
        $client = static::createClient();
        // Send a POST request to create a new user
        $client->request('POST', '/api/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'role' => 'Member',
            'password' => 'securepassword'
        ]));

        // Assert the HTTP status code is 201 (Created)
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to view the list of users via API
    public function testViewListOfUsers()
    {
        $client = static::createClient();
        // Send a GET request to fetch the list of users
        $client->request('GET', '/api/users');

        // Assert the HTTP status code is 200 (OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to view details of a specific user via API
    public function testViewUserDetails()
    {
        $client = static::createClient();
        // Send a GET request to fetch details of a user with ID 1
        $client->request('GET', '/api/users/1');

        // Assert the HTTP status code is 200 (OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to edit an existing user via API
    public function testEditUser()
    {
        $client = static::createClient();
        // Send a PUT request to update details of the user with ID 1
        $client->request('PUT', '/api/users/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Updated Test User',
            'email' => 'updatedtestuser@example.com',
            'role' => 'Admin',
            'password' => 'newsecurepassword'
        ]));

        // Assert the HTTP status code is 200 (OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to remove an existing user via API
    public function testRemoveUser()
    {
        $client = static::createClient();
        // Send a DELETE request to remove the user with ID 1
        $client->request('DELETE', '/api/users/1');

        // Assert the HTTP status code is 204 (No Content)
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }
}
?>
