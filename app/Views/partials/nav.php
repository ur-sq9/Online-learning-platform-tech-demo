<ul>
    <li><a href="/home">Home</a></li>
</ul>
<?php if (!isset($_SESSION['user_id'])): ?>
    <ul>
        <li><a href="/login">Login</a></li>
        <li><a href="/register">Register</a></li>
    </ul>
<?php else: ?>
    <ul>
        <li class="welcome">Welcome<?= ', ' . $_SESSION['logged_username'] ?? '' ?></li>
        <li><a href="/courses?page=1&query=&sort=created_at&order=asc">Courses</a></li>
        <li><a href="/logout">Logout</a></li>
    </ul>
<?php endif ?>