<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Congo Connect', ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($message ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <p><?= htmlspecialchars($dbMessage ?? '', ENT_QUOTES, 'UTF-8') ?></p>
</body>
</html>
