<?php
namespace ContactsAgenda\Helpers;

use ContactsAgenda\Models\PhoneModel;

class PhoneService {
    private $phoneModel;

    /**
     * Constructor
     * @param PhoneModel $phoneModel Injected Phone model dependency
     */
    public function __construct(PhoneModel $phoneModel) {
        $this->phoneModel = $phoneModel;
    }

    /**
     * Synchronize phone numbers for a contact
     * Deletes old phones and adds new validated phones
     * @param int $contactId ID of the contact
     * @param array $phones Array of phone numbers to sync
     */
    public function syncPhones(int $contactId, array $phones): void {
        // Remove old phones
        foreach ($this->phoneModel->getByContact($contactId) as $p) {
            $this->phoneModel->delete($p['id']);
        }

        // Add new phones
        foreach ($phones as $phone) {
            $cleanPhone = preg_replace('/\D+/', '', $phone); // remove tudo que não for dígito
            if (!empty($cleanPhone)) {
                $this->phoneModel->add($contactId, $cleanPhone);
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
