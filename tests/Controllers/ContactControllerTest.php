<?php
use PHPUnit\Framework\TestCase;
use ContactsAgenda\Controllers\ContactController;

// Define a constant to indicate that tests are running.
// This allows the controller to bypass redirects during unit testing.
if (!defined('UNIT_TEST')) {
    define('UNIT_TEST', true);
}

class ContactControllerTest extends TestCase
{
    /**
     * @var ContactController Mocked instance of ContactController for testing
     */
    private $controller;

    /**
     * setUp is executed before each test method.
     * Here we create a mocked instance of ContactController,
     * overriding the 'redirect' method so that it does not actually perform HTTP redirects.
     */
    protected function setUp(): void
    {
        $this->controller = $this->getMockBuilder(ContactController::class)
                                 ->onlyMethods(['redirect'])
                                 ->getMock();

        // Configure the mocked redirect method to simply return true instead of executing a real redirect.
        $this->controller->method('redirect')->willReturn(true);
    }

    /**
     * Test that saving a contact via the controller successfully creates a new record.
     *
     * Steps:
     * 1. Provide sample input data for a new contact.
     * 2. Call the save() method on the controller (redirect is mocked).
     * 3. Retrieve the saved contact from the underlying model using getContactModel().
     * 4. Assert that the saved contact's name and email match the input.
     */
    public function testSaveContactCreatesNew()
    {
        $data = [
            'name' => 'Controller Test',
            'email' => 'controller@test.com',
            'address' => 'Controller Street',
            'phones' => ['+551177777777']
        ];

        // Perform the save operation; redirect is intercepted by the mock
        $this->controller->save($data);

        // Retrieve the newly created contact directly from the model for validation
        $contact = $this->controller->getContactModel()->findByEmail('controller@test.com');

        // Verify that the contact was saved correctly
        $this->assertEquals('Controller Test', $contact['name']);
        $this->assertEquals('controller@test.com', $contact['email']);
    }
}
