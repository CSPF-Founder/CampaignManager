<?php
use Core\View;
?>
<!DOCTYPE html>
<html >
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <link rel="stylesheet" href="/resources/css/form.css">
</head>

<div class="box_form_page">
    <div class="form">
        <h3>Login Page</h3>
        <form class="box-form" autocomplete="off" action="" method="POST" id="login_form" autocomplete="off">
            <input type="text" placeholder="username" name="username" required />
            <input type="password" placeholder="password" name="password" required />
            <button type="submit" form="login_form" name="login" value="Login">Login</button>
        </form>
        <div class="error_box">
            <?php
            if(isset($error_message)){
                    View::securePrint($error_message);
                }
            ?>
        </div>
    </div>
</div>
</body>
</html>
