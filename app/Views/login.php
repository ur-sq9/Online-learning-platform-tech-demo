<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login: Online leerplatform</title>
</head>
<body>
<div class="form_auth">
    <section>
        <header>
            <h1>Login:</h1>
            <span>Don't have an acount?</span> <a href="/register">Register.</a>
        </header>
        <form action="/login" method="post">
            <div><input type="hidden" name="csrf_token" value="<?= $token ?>"></div>
            
            <div>
                <label for="username">Username:</label> <br>
                <input type="text" id="username" name="username_login" value="<?= $username ?? '' ?>">
                <?php if (isset($errors['username_login'])): ?>
                    <span class="error"><?= $errors['username_login'] ?></span>
                <?php endif ?> 
            </div>
            <div>
                <label for="password">Password:</label> <br>
                <input type="password" id="password" name="password_login">
                <?php if (isset($errors['password_login'])): ?>
                    <span class="error"><?= $errors['password_login'] ?></span>
                <?php endif ?> 
            </div>
    
            <div>
                <input type="checkbox" name="remember_me" id="remember_me">
                <label for="remember_me">Remember me</label>
            </div>
            <div><input type="submit" name="login" value="Login"></div>
            <?php if (isset($errors['login'])): ?>
                    <span class="error"><?= $errors['login'] ?></span>
            <?php endif ?> 
        </form>
    </section>
</div>
</body>
</html>