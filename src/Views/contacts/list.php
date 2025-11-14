<?php
ob_start();

// Retrieve messages from session
$messages = [];

// Success messages
if (!empty($_SESSION['success'])) {
    $messages['success'] = (array) $_SESSION['success'];
    unset($_SESSION['success']);
}

// Error messages
if (!empty($_SESSION['errors'])) {
    $messages['error'] = (array) $_SESSION['errors'];
    unset($_SESSION['errors']);
}

/**
 * Format phone number with mask
 * Supports:
 * - (XX) XXXXX-XXXX (Brazil)
 * - (XX) XXXX-XXXX (Brazil landline)
 * - +55XXXXXXXXXXX format
 */
function formatPhone(string $phone): string {
    // Remove non-digit characters except leading '+'
    $digits = preg_replace('/[^\d+]/', '', $phone);

    // Handle +55 prefix
    if (str_starts_with($digits, '+55')) {
        $digits = substr($digits, 3); // remove country code
    }

    // Remove any other non-digit characters
    $digits = preg_replace('/\D/', '', $digits);

    $length = strlen($digits);

    if ($length === 11) { // mobile with DDD
        return sprintf('(%s) %s-%s',
            substr($digits, 0, 2),
            substr($digits, 2, 5),
            substr($digits, 7, 4)
        );
    } elseif ($length === 10) { // landline with DDD
        return sprintf('(%s) %s-%s',
            substr($digits, 0, 2),
            substr($digits, 2, 4),
            substr($digits, 6, 4)
        );
    } else {
        return $phone; // unknown format, return as-is
    }
}
?>
<h1>Contacts</h1>

<!-- Button to create a new contact -->
<button onclick="window.location.href='index.php?action=form'">
    Add Contact
</button>

<!-- Contacts table -->
<table border="1" cellpadding="5">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Address</th>
        <th>Phones</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($contacts as $c): ?>
    <tr>
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= htmlspecialchars($c['email']) ?></td>
        <td><?= htmlspecialchars($c['address']) ?></td>
        <td>
            <?php 
            $phones = (new \ContactsAgenda\Models\PhoneModel())->getByContact($c['id']);
            foreach ($phones as $p) echo htmlspecialchars(formatPhone($p['phone'])) . '<br>';
            ?>
        </td>
        <td>
            <button onclick="window.location.href='index.php?action=form&id=<?= $c['id'] ?>'">
                Edit
            </button>

            <button class="delete" onclick="if(confirm('Delete?')) { window.location.href='index.php?action=delete&id=<?= $c['id'] ?>'; }">
                Delete
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Pagination -->
<div class="pagination">
    <button 
        onclick="window.location.href='index.php?page=<?= max(1, $page - 1) ?>'" 
        <?= $page <= 1 ? 'disabled' : '' ?>>
        Previous
    </button>

    <span disabled>
        Page <?= $page ?> of <?= $totalPages ?>
    </span>

    <button 
        onclick="window.location.href='index.php?page=<?= min($totalPages, $page + 1) ?>'" 
        <?= $page >= $totalPages ? 'disabled' : '' ?>>
        Next
    </button>
</div>

<?php
// Capture the content for layout
$content = ob_get_clean();
$title = "Contacts List";

// Include page-specific CSS and JS
$pageCss = ["assets/css/contacts/list.css"];
$pageJs = [];

// Include the main layout
include __DIR__ . '/../layout.php';
