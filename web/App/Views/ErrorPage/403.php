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
                    <h1 class="error-number">403</h1>
                    <h2>Access Denied</h2>
                    <?php if(isset($error_message) && $error_message): ?>
                        <?php \Core\View::securePrint($error_message); ?>
                    <?php else: ?>
                        <p>You are not authorized.</a>
                    <?php endif;?>
                    </p>

                </div>
            </div>
        </div>
        <!-- /page content -->
    </div>
</div>

</body>
</html>