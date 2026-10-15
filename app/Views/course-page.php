<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $course['title'] ?? 'course page' ?></title>
</head>
<body>
    <section class="course-page">
        <header>
            <h1><?= $course['title'] ?></h1>
            <?php include PARTIALS . 'overview.php'; ?> 
        </header>
        <p><?= $course['description'] ?></p>
        <?php if (!$enrolled): ?>
            <!-- CHECK ENROLL STATUS OTHERWISE SHOW FORM - IF NOT ENROLLED <br> -->
            <form action="/courses/<?= $course['id'] ?>/<?= str_replace(' ', '-', $course['title']) ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?= $token ?>">
                <input class="enrol" type="submit" name="enrol" value="Enrol">
            </form>
        <?php else: ?>
            <?php if ($hasSubmitted): ?>
                <div class="succes flex"><span>✅ You have finished this course!</span></div>
            <?php else: ?>
                <!-- CHECK ENROLL STATUS OTHERWISE SHOW FORM - IF ENROLLED <br> -->
                <form action="/courses/<?= $course['id'] ?>/<?= str_replace(' ', '-', $course['title']) ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $token ?>">
                    <input class="witdraw" type="submit" name="withdraw" value="Withdraw">
                </form>
                <h4>Submit final project</h4>
            <form action="/courses/<?= $course['id'] ?>/<?= str_replace(' ', '-', $course['title']) ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $token ?>">
                <div class="file-container">
                    <input type="file" name="file" id="uploaded_file">
                    <label class="upload" for="uploaded_file"><span>⬆ Upload file</span></label>
                    <input type="submit" name="submit_assigment" value="Submit assigment">
                </div>
                
                <?php if (isset($error)): ?>
                    <div><span class="error"><?= $error ?></span></div>
                <?php endif ?>    
            </form>
            <?php endif ?> 
        <?php endif ?>
    </section>
</body>
</html>