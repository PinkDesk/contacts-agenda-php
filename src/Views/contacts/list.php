<?php
ob_start();
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
        <!-- Display contact details safely -->
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= htmlspecialchars($c['email']) ?></td>
        <td><?= htmlspecialchars($c['address']) ?></td>
        <td>
            <?php 
            // Fetch and display phones for this contact
            $phones = (new \ContactsAgenda\Models\Phone())->getByContact($c['id']);
            foreach ($phones as $p) echo htmlspecialchars($p['phone']) . '<br>';
            ?>
        </td>
        <td>
            <!-- Edit and Delete actions -->
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
    <!-- Previous button -->
    <button 
        onclick="window.location.href='index.php?page=<?= max(1, $page - 1) ?>'" 
        <?= $page <= 1 ? 'disabled' : '' ?>>
        Previous
    </button>

    <!-- Page info -->
    <span disabled>
        Page <?= $page ?> of <?= $totalPages ?>
    </span>

    <!-- Next button -->
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
