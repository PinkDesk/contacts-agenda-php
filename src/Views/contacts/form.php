<?php
ob_start();

// Recupera mensagens de erro da sessão
$messages = $_SESSION['messages'] ?? [];
unset($_SESSION['messages']);

// Recupera dados antigos em caso de erro
$old = $_SESSION['old_input'] ?? [];
if (!empty($old)) {
    $contact = $old;
    $phones = $old['phones'] ?? [];
    unset($_SESSION['old_input']);
}

$isEdit = !empty($contact['id']);
$title = $isEdit ? "Edit Contact" : "Add Contact";
?>
<h1><?= $title ?></h1>

<?php
// Exibe mensagens de erro
if (!empty($messages)) {
    foreach ($messages as $type => $msgs) {
        foreach ((array)$msgs as $msg) {
            echo '<div class="message ' . htmlspecialchars($type) . '">' . htmlspecialchars($msg) . '</div>';
        }
    }
}
?>

<form method="post" action="index.php?action=save">
    <input type="hidden" name="id" value="<?= $contact['id'] ?? '' ?>">

    <label>Name:</label><br>
    <input type="text" name="name" value="<?= $contact['name'] ?? '' ?>"><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= $contact['email'] ?? '' ?>"><br>

    <label>Address:</label><br>
    <input type="text" name="address" value="<?= $contact['address'] ?? '' ?>"><br>

    <label>Phones:</label><br>
    <div id="phones-container">
        <?php
            // Define phones a mostrar
            $phonesToShow = [];
            if (!empty($phones)) {
                // Veio do POST após erro
                $phonesToShow = $phones;
            } elseif (!empty($contact['phones'])) {
                // Veio do banco
                foreach ($contact['phones'] as $p) {
                    $phonesToShow[] = is_array($p) ? $p['phone'] : $p;
                }
            } else {
                $phonesToShow[] = '';
            }

            // Exibe os campos de telefone
            foreach ($phonesToShow as $p) {
                echo '<div class="phone-field">
                        <input type="text" name="phones[]" value="' . htmlspecialchars($p) . '">
                        <button type="button" class="remove-phone">Remove</button>
                    </div>';
            }
        ?>
    </div>

    <button type="button" id="add-phone">Add Phone</button>
    <button type="submit">Save</button>
</form>

<a href="index.php">Back to list</a>

<?php
$content = ob_get_clean();
$title = $contact ? "Edit Contact" : "Add Contact";
$pageCss = ["assets/css/contacts/form.css"];
$pageJs = ["assets/js/contacts/form.js"];
include dirname(__DIR__) . '/layout.php';
