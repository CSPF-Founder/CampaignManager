<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

use Core\View;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Error ! </title>

    <!-- Bootstrap -->
    <link href="/resources/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/resources/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="/resources/css/custom.css" rel="stylesheet">
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <!-- page content -->
        <div class="col-md-12">
            <div class="col-middle">
                <div class="text-center">
                    <h1 class="error-number">500</h1>
                    <h2>Application Error</h2>
                    <?php
                    if(isset($error_message)) {
                        View::securePrint("Error Message: ".$error_message);
                    }
                    else {
                        echo '<p>Sorry, an error has occurred - Please contact the admin </p>';
                    }?>
                    <p>If the problem persists feel free to contact us</a>
                    </p>

                </div>
            </div>
        </div>
        <!-- /page content -->
    </div>
</div>

</body>
</html>
