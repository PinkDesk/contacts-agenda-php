<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Contacts Agenda' ?></title>
    <link rel="stylesheet" href="assets/css/global.css">
    <?php
    // Incluir CSS específico da página, se definido
    if (!empty($pageCss)) {
        foreach ($pageCss as $cssFile) {
            echo '<link rel="stylesheet" href="' . $cssFile . '">';
        }
    }
    ?>
</head>

<body>
    <div class="container">
        <?php
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
        <?= $content ?>
    </div>

    <script src="assets/js/global.js"></script>
    <?php
    // Incluir JS específico da página, se definido
    if (!empty($pageJs)) {
        foreach ($pageJs as $jsFile) {
            echo '<script src="' . $jsFile . '"></script>';
        }
    }
    ?>
</body>

</html>