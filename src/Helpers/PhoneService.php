<?php
namespace ContactsAgenda\Helpers;

use ContactsAgenda\Models\Phone;

class PhoneService {
    private $phoneModel;

    public function __construct(Phone $phoneModel) {
        $this->phoneModel = $phoneModel;
    }

    public function syncPhones(int $contactId, array $phones): void {
        // Remove antigos
        foreach ($this->phoneModel->getByContact($contactId) as $p) {
            $this->phoneModel->delete($p['id']);
        }

        // Adiciona novos
        foreach ($phones as $phone) {
            if (!empty($phone) && $this->validatePhoneFormat($phone)) {
                $this->phoneModel->add($contactId, $phone);
            }
        }
    }

    public function validatePhoneFormat(string $phone): bool {
        return preg_match('/^\+?\d+$/', $phone);
    }
}
