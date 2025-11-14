<?php
namespace ContactsAgenda\Helpers;

class Logger {
    private $file;

    /**
     * Constructor
     * @param string $file Path to the log file (default: storage/logs/app.log)
     */
    public function __construct($file = __DIR__ . '/../../storage/logs/app.log') {
        $this->file = $file;
    }

    /**
     * Log an error message to the log file
     * @param string $message Error message to log
     */
    public function error($message): void {
        $date = date('Y-m-d H:i:s'); // Current timestamp
        // Append error message to log file
        file_put_contents($this->file, "[$date] ERROR: $message\n", FILE_APPEND);
    }
}
