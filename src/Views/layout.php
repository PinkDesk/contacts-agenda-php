<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Contacts Agenda' ?></title>

    <!-- Global CSS -->
    <link rel="stylesheet" href="assets/css/global.css">

    <?php
    // Include page-specific CSS files if defined
    if (!empty($pageCss)) {
        foreach ($pageCss as $cssFile) {
            echo '<link rel="stylesheet" href="' . htmlspecialchars($cssFile) . '">';
        }
    }
    ?>
</head>

<body>
    <div class="container">

        <?php
        // Display session messages (only if not on the form page)
        $currentAction = $_GET['action'] ?? 'index';
        if (!empty($messages) && is_array($messages) && $currentAction !== 'form'): ?>
            <div class="messages">
                <?php foreach ($messages as $type => $msgs): ?>
                    <?php foreach ((array)$msgs as $msg): ?>
                        <div class="message <?= htmlspecialchars($type) ?>"><?= htmlspecialchars($msg) ?></div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Main page content -->
        <?= $content ?>
    </div>

    <?php
    // Include page-specific JS files if defined
    if (!empty($pageJs)) {
        foreach ($pageJs as $jsFile) {
            echo '<script src="' . htmlspecialchars($jsFile) . '"></script>';
        }
    }
    ?>
</body>

</html>
