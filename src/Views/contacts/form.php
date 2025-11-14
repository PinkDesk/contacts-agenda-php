<?php
ob_start();

// Retrieve error messages from the session, if any
$messages = $_SESSION['messages'] ?? [];
unset($_SESSION['messages']);

// Retrieve old input data in case of form validation errors
$old = $_SESSION['old_input'] ?? [];
if (!empty($old)) {
    $contact = $old;
    $phones = $old['phones'] ?? [];
    unset($_SESSION['old_input']);
}

// Determine if this is an edit or create form
$isEdit = !empty($contact['id']);
$title = $isEdit ? "Edit Contact" : "Add Contact";
?>
<h1><?= $title ?></h1>

<?php
// Display messages (errors or success)
if (!empty($messages)) {
    foreach ($messages as $type => $msgs) {
        foreach ((array)$msgs as $msg) {
            echo '<div class="message ' . htmlspecialchars($type) . '">' . htmlspecialchars($msg) . '</div>';
        }
    }
}
?>

<!-- Contact Form -->
<form method="post" action="index.php?action=save">
    <!-- Hidden field for contact ID (used on edit) -->
    <input type="hidden" name="id" value="<?= $contact['id'] ?? '' ?>">

    <!-- Name field -->
    <label>Name:</label><br>
    <input type="text" name="name" value="<?= $contact['name'] ?? '' ?>"><br>

    <!-- Email field -->
    <label>Email:</label><br>
    <input type="email" name="email" value="<?= $contact['email'] ?? '' ?>"><br>

    <!-- Address field -->
    <label>Address:</label><br>
    <input type="text" name="address" value="<?= $contact['address'] ?? '' ?>"><br>

    <!-- Phones section -->
    <label>Phones:</label><br>
    <div id="phones-container">
        <?php
            // Determine which phones to display
            $phonesToShow = [];
            if (!empty($phones)) {
                // Use phones from POST after a validation error
                $phonesToShow = $phones;
            } elseif (!empty($contact['phones'])) {
                // Use phones from the database
                foreach ($contact['phones'] as $p) {
                    $phonesToShow[] = is_array($p) ? $p['phone'] : $p;
                }
            } else {
                // Default empty phone field
                $phonesToShow[] = '';
            }

            // Render phone input fields
            foreach ($phonesToShow as $p) {
                echo '<div class="phone-field">
                        <input type="text" name="phones[]" value="' . htmlspecialchars($p) . '">
                        <button type="button" class="remove-phone delete">Remove</button>
                    </div>';
            }
        ?>
    </div>

    <!-- Buttons to add new phone or save contact -->
    <button type="button" id="add-phone">Add Phone</button>
    <button type="submit">Save</button>
</form>

<!-- Button to go back to the list -->
<button onclick="window.location.href='index.php'">
    Back to list
</button>

<?php
// Capture the content for the layout
$content = ob_get_clean();
$title = $contact ? "Edit Contact" : "Add Contact";

// Include page-specific CSS and JS
$pageCss = ["assets/css/contacts/form.css"];
$pageJs = ["assets/js/contacts/form.js"];

// Include the main layout
include dirname(__DIR__) . '/layout.php';
