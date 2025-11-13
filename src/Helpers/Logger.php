<?php
namespace ContactsAgenda\Helpers;

class Logger {
    private $file;

    public function __construct($file = __DIR__ . '/../../storage/logs/app.log') {
        $this->file = $file;
    }

    public function error($message): void {
        $date = date('Y-m-d H:i:s');
        file_put_contents($this->file, "[$date] ERROR: $message\n", FILE_APPEND);
    }
}
