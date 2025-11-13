<?php
ob_start();
?>
<h1>Contacts</h1>
<a href="index.php?action=form">Add Contact</a>

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
            $phones = (new \ContactsAgenda\Models\Phone())->getByContact($c['id']);
            foreach ($phones as $p) echo htmlspecialchars($p['phone']) . '<br>';
            ?>
        </td>
        <td>
            <a href="index.php?action=form&id=<?= $c['id'] ?>">Edit</a>
            <a href="index.php?action=delete&id=<?= $c['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Pagination -->
<div class="pagination">
<?php if ($totalPages > 1): ?>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page): ?>
            <strong><?= $i ?></strong>
        <?php else: ?>
            <a href="index.php?page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
<?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = "Contacts List";
$pageCss = ["assets/css/contacts/list.css"];
$pageJs = [];
include __DIR__ . '/../layout.php';
