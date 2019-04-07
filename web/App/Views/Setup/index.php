<?php

use App\Config;
use Core\View;
?>
<!DOCTYPE html>
<html >
<head>
    <meta charset="UTF-8">
    <title>Setup</title>
    <link rel="stylesheet" href="/resources/css/form.css">
</head>

<div class="box_form_page">
    <div class="form">
        <h3>Setup</h3>
        <form method="POST" action="/setup/index" id="box_form" autocomplete="off">
            <input type="text" placeholder="Super Admin Username" value="Super Admin" name="name" autocomplete="off" required/>
            <input type="text" placeholder="Super Admin Username" value="SuperAdmin" name="username" autocomplete="off" required/>
            <input type="password" placeholder="Super Admin Password" value="" name="password" required />
            <button type="submit" name="setup" value="Setup">Setup</button>
        </form>
        <br/>
        Note: First, Create the database "<?php View::securePrint(Config::DB_NAME); ?>"
        (just database not tables)
        <br/><br/>
        <?php
        if(isset($error_message)){
            echo "<div class='error_box'>";
            View::securePrint($error_message);
            echo "</div>";
        }?>

    </div>
</div>
</body>
</html>
