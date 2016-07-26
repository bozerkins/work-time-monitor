<?php

if (!file_exists('var/users.php')) {
    return;
}

$users = require 'var/users.php';

if (!$users || !is_array($users)) {
    return;
}

session_start();

if (array_key_exists('authenticated', $_SESSION)) {
    return;
}

$login = array_key_exists('login', $_POST) ? $_POST['login'] : null;
$password = array_key_exists('password', $_POST) ? $_POST['password'] : null;

if (array_key_exists('submit', $_REQUEST)) {
    if (password_verify($password, $users[$login])) {
        $_SESSION['authenticated'] = true;
        header('Location: /');
        return;
    }
}

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    echo json_encode(array('status' => false, 'result' => 'not authenticated'));
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Authentication</title>

    <!-- Bootstrap core CSS -->
    <link href="resource/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="resource/signin.css" rel="stylesheet">
</head>

<body>

<div class="container">

    <form class="form-signin" method="POST" action="">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input name="login" type="text" id="inputEmail" class="form-control" placeholder="Username" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" name="submit" type="submit">Sign in</button>
    </form>

</div> <!-- /container -->

</body>
</html>
<?php
exit;