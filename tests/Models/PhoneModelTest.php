<?php
use PHPUnit\Framework\TestCase;
use ContactsAgenda\Models\PhoneModel;
use ContactsAgenda\Models\ContactModel;

class PhoneModelTest extends TestCase
{
    /**
     * @var PhoneModel Instance of PhoneModel used for testing
     */
    private $model;

    /**
     * @var ContactModel Instance of ContactModel used to create contacts for phone tests
     */
    private $contactModel;

    /**
     * setUp() is executed before each test method.
     * It initializes fresh instances of PhoneModel and ContactModel
     * to ensure that tests are isolated and do not interfere with each other.
     */
    protected function setUp(): void
    {
        $this->model = new PhoneModel();
        $this->contactModel = new ContactModel();
    }

    /**
     * Test adding a phone number to a contact and retrieving it.
     *
     * Steps:
     * 1. Create a new contact with a unique email.
     * 2. Add a phone number to this contact using PhoneModel::add().
     * 3. Assert that the returned phone ID is numeric.
     * 4. Retrieve all phone numbers for the contact and assert:
     *    - The list is not empty.
     *    - The phone number matches the one added.
     */
    public function testAddAndGetPhone()
    {
        $contactId = $this->contactModel->create(
            'Test Contact',
            'test' . uniqid() . '@example.com',
            '123 Street'
        );

        $phoneId = $this->model->add($contactId, '+551199999999');
        $this->assertIsNumeric($phoneId);

        $phones = $this->model->getByContact($contactId);
        $this->assertNotEmpty($phones);
        $this->assertEquals('+551199999999', $phones[0]['phone']);
    }

    /**
     * Test deleting a phone number from a contact.
     *
     * Steps:
     * 1. Create a new contact with a unique email.
     * 2. Add a phone number to this contact.
     * 3. Delete the phone number using PhoneModel::delete().
     * 4. Assert that the deletion returned true.
     * 5. Retrieve the contact's phone list and assert that it is empty after deletion.
     */
    public function testDeletePhone()
    {
        $contactId = $this->contactModel->create(
            'Test Contact',
            'deletephone' . uniqid() . '@example.com',
            '123 Street'
        );

        $phoneId = $this->model->add($contactId, '+551188888888');

        $deleted = $this->model->delete($phoneId);
        $this->assertTrue($deleted, "Phone should have been deleted");

        $phones = $this->model->getByContact($contactId);
        $this->assertEmpty($phones, "Phone list should be empty after deletion");
    }
}
