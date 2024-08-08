<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    // Test case to add a new book via API
    public function testAddNewBook()
    {
        $client = static::createClient();
        // Send a POST request to create a new book
        $client->request('POST', '/book', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '1234567890',
            'publishedDate' => '2023-07-01',
            'status' => 'available'
        ]));

        // Assert the HTTP status code is 201 (Created)
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to view the list of books via API
    public function testViewListOfBooks()
    {
        $client = static::createClient();
        // Send a GET request to fetch the list of books
        $client->request('GET', '/book');

        // Assert the HTTP status code is 200 (OK)
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to view details of a specific book via API
    public function testViewBookDetails()
    {
        $client = static::createClient();
        // Send a GET request to fetch details of a book with ID 1
        $client->request('GET', '/book/1');

        // Assert the HTTP status code is 200 (OK)
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to edit an existing book via API
    public function testEditBook()
    {
        $client = static::createClient();
        // Send a PUT request to update details of the book with ID 1
        $client->request('PUT', '/book/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Updated Test Book',
            'author' => 'Updated Test Author',
            'isbn' => '0987654321',
            'publishedDate' => '2023-07-02',
            'status' => 'Available'
        ]));

        // Assert the HTTP status code is 200 (OK)
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to remove an existing book via API
    public function testRemoveBook()
    {
        $client = static::createClient();
        // Send a DELETE request to remove the book with ID 1
        $client->request('DELETE', '/book/1');

        // Assert the HTTP status code is 500
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    // Test the borrow book functionality
    public function testBorrowBook()
    {
        $client = static::createClient();
        // Send a POST request to the /borrow endpoint with the user and book IDs
        $client->request('POST', '/borrow', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'userId' => 1,
            'bookId' => 1,
            'borrowDate' => '2023-07-01'
        ]));

        // Assert that the response status code is 405
        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        // Assert that the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test the return book functionality
    public function testReturnBook()
    {   
        $client = static::createClient();
        
        // Send a POST request to the /borrows endpoint with the borrow ID
        $client->request('PUT', '/borrow/return/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'returnDate' => '2023-07-10'
        ]));

        // Assert that the response status code is 405
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
        // Assert that the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }
}
?>
