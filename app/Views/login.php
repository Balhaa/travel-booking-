<?php
use App\Classes\Auth;

if (Auth::check()) {
    header("Location: index.php?page=dashboard");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new Auth();
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($auth->login($username, $password)) {
        header("Location: index.php?page=dashboard");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<div class="login-container">
    <h2 style="text-align: center;">Login Travel Booking</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" style="width: 100%;">Masuk</button>
    </form>
    
</div>
