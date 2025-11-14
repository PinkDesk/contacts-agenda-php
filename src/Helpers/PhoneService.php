<?php
namespace ContactsAgenda\Helpers;

use ContactsAgenda\Models\Phone;

class PhoneService {
    private $phoneModel;

    /**
     * Constructor
     * @param Phone $phoneModel Injected Phone model dependency
     */
    public function __construct(Phone $phoneModel) {
        $this->phoneModel = $phoneModel;
    }

    /**
     * Synchronize phone numbers for a contact
     * Deletes old phones and adds new validated phones
     * @param int $contactId ID of the contact
     * @param array $phones Array of phone numbers to sync
     */
    public function syncPhones(int $contactId, array $phones): void {
        // Remove old phone records for this contact
        foreach ($this->phoneModel->getByContact($contactId) as $p) {
            $this->phoneModel->delete($p['id']);
        }

        // Add new phone numbers if valid
        foreach ($phones as $phone) {
            if (!empty($phone) && $this->validatePhoneFormat($phone)) {
                $this->phoneModel->add($contactId, $phone);
            }
        }
    }

    /**
     * Validate phone number format
     * Accepts only digits and optional leading '+'
     * @param string $phone Phone number string
     * @return bool True if phone format is valid, false otherwise
     */
    public function validatePhoneFormat(string $phone): bool {
        return preg_match('/^\+?\d+$/', $phone);
    }
}
