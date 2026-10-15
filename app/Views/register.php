<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <div class="form_auth">
        <section>
            <header>
                <h1>Register:</h1>
                <h4><?= 'Step ' . $step . '/' . $steps ?></h4>
                <span>Already have an account?</span> <a href="/login">Login.</a>
            </header>
            <form action="/register" method="post" novalidate>
                <div><input type="hidden" name="csrf_token" value="<?= $token ?>"></div>
                <?php if ($step === 1): ?>
                    <div>
                        <label for="username">Username:</label> <br>
                        <input type="text" id="username" name="username" value="<?= $formData['username'] ?? '' ?>">
                        <?php if (isset($errors['username'])): ?>
                            <div><span class="error"><?= $errors['username'] ?></span></div>
                        <?php endif ?>
                    </div>
                    <div>
                        <label for="email">Email:</label> <br>
                        <input type="email" id="email" name="email" value="<?= $formData['email'] ?? '' ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div><span class="error"><?= $errors['email'] ?></span></div>
                        <?php endif ?>
                    </div>
                <?php elseif ($step === 2): ?>
                    <div>
                        <label for="full_name">Full name:</label> <br>
                        <input type="text" id="full_name" name="full_name" value="<?= $formData['full_name'] ?? '' ?>">
                        <?php if (isset($errors['full_name'])): ?>
                            <div><span class="error"><?= $errors['full_name'] ?></span></div>
                        <?php endif ?>
                    </div>
                    <div>
                        <label for="birthdate">Birthdate:</label> <br>
                        <input type="date" id="birthdate" name="birthdate" value="<?= $formData['birthdate'] ?? '' ?>">
                        <?php if (isset($errors['birthdate'])): ?>
                            <div><span class="error"><?= $errors['birthdate'] ?></span></div>
                        <?php endif ?>
                    </div>
                <?php elseif ($step === $steps): ?>
                    <div>
                        <label for="password">Password:</label> <br>
                        <input type="password" id="password" name="password">
                        <?php if (isset($errors['password'])): ?>
                            <div><span class="error"><?= $errors['password'] ?></span></div>
                        <?php endif ?>
                    </div>
                    <div>
                        <label for="password_confirmation">Confirm password:</label> <br>
                        <input type="password" id="password_confirmation" name="password_confirmation">
                        <?php if (isset($errors['password_confirmation'])): ?>
                            <div><span class="error"><?= $errors['password_confirmation'] ?></span></div>
                        <?php endif ?>
                    </div>
                    <div>
                        <input <?= !empty($formData['agree']) ? 'checked' : '' ?> type="checkbox" name="agree" id="agree">
                        <label for="agree"> Do you agree with terms and conditions?</label>
                        <?php if (isset($errors['agree'])): ?>
                            <div><span class="error"><?= $errors['agree'] ?></span></div>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <div class="bottom-nav">
                    <?php if ($step === $steps): ?>
                        <input type="submit" name="submit_final" value="Submit">
                    <?php endif ?>
                    <?php if ($step < $steps): ?>
                        <input type="submit" name="next" value="Next">
                    <?php endif ?>
                    <?php if ($step > 1): ?>
                        <input type="submit" name="back" value="Back">
                    <?php endif ?>
                </div>

            </form>
        </section>
    </div>
</body>

</html>