<?php
use PHPUnit\Framework\TestCase;
use ContactsAgenda\Models\ContactModel;

class ContactModelTest extends TestCase
{
    /**
     * @var ContactModel Instance of the ContactModel being tested
     */
    private $model;

    /**
     * setUp() is executed before each test method.
     * It initializes a fresh instance of ContactModel to ensure tests are isolated.
     */
    protected function setUp(): void
    {
        $this->model = new ContactModel();
    }

    /**
     * Test that a new contact can be created successfully.
     *
     * Steps:
     * 1. Generate a unique email to avoid collisions with existing records.
     * 2. Call the create() method of the model.
     * 3. Assert that the returned contact ID is numeric.
     * 4. Retrieve the saved contact using findById() and verify that
     *    all properties (name, email, address) match the input.
     */
    public function testCreateContact()
    {
        $uniqueEmail = 'test' . uniqid() . '@example.com';
        $contactId = $this->model->create('Test User', $uniqueEmail, '123 Street');

        $this->assertIsNumeric($contactId, "Contact ID should be numeric");

        $contact = $this->model->findById($contactId);
        $this->assertEquals('Test User', $contact['name']);
        $this->assertEquals($uniqueEmail, $contact['email']);
        $this->assertEquals('123 Street', $contact['address']);
    }

    /**
     * Test that attempting to create a contact with a duplicate email
     * throws an exception.
     *
     * Steps:
     * 1. Generate a unique email.
     * 2. Create the first contact with this email.
     * 3. Attempt to create a second contact using the same email.
     * 4. Assert that the operation throws an Exception with the expected message.
     */
    public function testDuplicateEmail()
    {
        $uniqueEmail = 'test' . uniqid() . '@example.com';

        // Create the first contact to set up the duplicate scenario
        $this->model->create('Test User', $uniqueEmail, '123 Street');

        // Expect an exception when trying to create a contact with the same email
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Email already exists');

        $this->model->create('Test User 2', $uniqueEmail, '456 Avenue');
    }
}
