<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'base' ?></title>
    <link rel="stylesheet" href="<?= CSS_DIR ?? '/css/style.css' ?>">
</head>
<body>
     <nav><?php include PARTIALS . 'nav.php'; ?> </nav>
 <main>
    <?php require_once $viewlocation ?>  
 </main>
</body>
</html>