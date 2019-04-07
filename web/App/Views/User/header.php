<?php
use App\Auth;
use App\Config;
use Core\Security\CSRF;
use Core\View;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="/resources/images/favicon.ico" sizes="32x32" type="image/png"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php View::securePrint(Config::APP_TITLE); ?></title>

    <!-- Bootstrap -->
    <link href="/resources/vendor/bootstrap/css/bootstrap.min.css?v=3.3.7" rel="stylesheet">
    <link href="/resources/vendor/datatables/css/dataTables.bootstrap.css?v=1.10.18" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="/resources/vendor/font-awesome/css/font-awesome.min.css?v=4.6.3" rel="stylesheet">

    <!-- iCheck -->
    <link href="/resources/vendor/iCheck/skins/flat/green.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="/resources/css/custom.css?v=1.0.0" rel="stylesheet">

    <!-- jQuery -->
    <script src="/resources/vendor/jquery/js/jquery.min.js?v=2.2.4"></script>

    <!-- Custom Javascript -->
    <script src="/resources/js/main.js?v=1.0.0"></script>

    <!-- responsive table -->
    <script type="text/javascript" src="/resources/vendor/restable/js/jquery.restable.min.js?v=1.0.0"></script>
    <link rel="stylesheet" href="/resources/vendor/restable/css/jquery.restable.min.css?v=1.0.0">

</head>

<body class="nav-md">
<script type="text/javascript">

    $(document).ready(function(){
        //Auto include CSRF token
        $('form').append('<?php CSRF::addInputField();?>');
        $.ajaxPrefilter(function(options, originalOptions, jqXHR){
            if (options.type.toUpperCase() === "POST"
                && options.data.indexOf("<?php View::securePrint(CSRF::TOKEN_NAME)?>")<0) {
                // initialize `data` to empty string if it does not exist
                options.data = options.data || "";
                // add leading ampersand if `data` is non-empty
                options.data += options.data?"&":"";
                options.data += "<?php View::securePrint(CSRF::TOKEN_NAME)?>=<?php View::securePrint(CSRF::get()) ?>";
            }
        });
    });
</script>
<div class="container body" >
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="/" class="site_title" style="text-align: center">
                        <img src="/resources/images/logo.png" width="32px" height="32px" alt="logo" >
                        <span style="font-size: 16px;font-weight: bold;"><?php View::securePrint(Config::APP_TITLE); ?></span>
                    </a>
                </div>

                <div class="clearfix"></div>

                <!-- menu profile quick info -->
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <img src="/resources/img/default-avatar.png" alt="profile-pic" class="img-circle profile_img">
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2><?php View::securePrint(Auth::user()->getUsername()) ?></h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br />

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <h3>General</h3>
                        <ul class="nav side-menu">
                            <li>
                                <a href="/"><i class="fa fa-home text-aqua"></i> <span>Dashboard</span></a>
                            </li>
                            <?php if(!Auth::user()->hasRole('super_admin')):?>

                                <?php if(Auth::user()->can('view_Task') || Auth::user()->can('add_Task')): ?>
                                    <li><a><i class="fa fa-tasks text-blue"></i> Task <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <?php if(Auth::user()->can('view_Task')): ?>
                                                <li><a href="/task/list">List</a></li>
                                            <?php endif; ?>
                                            <?php if(Auth::user()->can('add_Task')): ?>
                                                <li><a href="/task/add">Add</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if(!Auth::user()->hasRole('super_admin')):?>
                                <?php if(Auth::user()->can('view_Feed') && Auth::user()->can('add_Feed')): ?>
                                <li><a><i class="fa fa-feed text-yellow"></i> Feed <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <?php if(Auth::user()->can('view_Feed')): ?>
                                            <li><a href="/feed/list">List</a></li>
                                        <?php endif; ?>
                                        <?php if(Auth::user()->can('add_Feed')): ?>
                                            <li><a href="/feed/add">Add</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                                <?php elseif(Auth::user()->can('add_Feed')):?>
                                    <li><a href="/feed/add"><i class="fa fa-feed text-yellow"></i> <span>Add Feed</span></a></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if(Auth::user()->hasRole('super_admin')): ?>
                                <li>
                                    <a><i class="fa fa-institution text-green"></i> Organization <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="/organization/list">List</a></li>
                                        <li><a href="/organization/add">Add</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>

                            <?php if(Auth::user()->can('view_User') || Auth::user()->can('add_User')): ?>
                                <li>
                                    <a><i class="fa fa-user text-green"></i> Users <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">

                                        <?php if(Auth::user()->can('view_User')): ?>
                                            <li><a href="/user/list">List</a></li>
                                        <?php endif; ?>
                                        <?php if(Auth::user()->can('add_User')): ?>
                                            <li><a href="/user/add">Add</a></li>
                                        <?php endif; ?>

                                        <?php if(Auth::user()->hasRole('super_admin')): ?>
                                            <li><a href="/role/list">Roles</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>

                            <?php if(Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin')): ?>
                            <li>
                                <a><i class="fa fa-map text-teal"></i> Constituency <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="/constituency/list">List</a></li>
                                    <li><a href="/constituency/add">Add</a></li>
                                </ul>
                            </li>
                            <?php endif; ?>

                            <li><a href="/user/logout"><i class="fa fa-sign-out text-fuchsia"></i> <span>Logout</span></a></li>

                        </ul>
                    </div>

                </div>
                <!-- /sidebar menu -->


            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav>
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <img src="/resources/img/default-avatar.png" alt="">
                                <?php View::securePrint(Auth::user()->getUsername()) ?>
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <!-- <li><a href="#"> Profile</a></li> -->
                                <li><a href="/user/logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->
        <!-- Error, success, warning or any other messages -->
        <div id="app-messages-div" class="app-messages-div-sm">
            <?php if(\App\Config::DEBUG_MODE && Auth::user()->hasRole('super_admin')): ?>
                <div class="alert alert-danger message_box" >
                    <strong>Debug Mode is On!</strong> If you are running in production, turn it off
                </div>
            <?php endif ?>

            <?php if(isset($flash_messages) && $flash_messages): ?>
                <?php foreach ($flash_messages as $flash ): ?>
                    <div class="alert alert-<?php View::securePrint($flash->type) ?> message_box" >
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php View::securePrint($flash->message) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="app-msg-box" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirm-prompt" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header alert-warning">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"> <i class="glyphicon glyphicon-question-sign"></i> Confirm </h4>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary bg-orange" id="prompt-confirm-button">Confirm</button>
                        <button type="button" data-dismiss="modal" class="btn">Cancel</button>
                    </div>
                </div>

            </div>
        </div>

        <!-- /Error, success, warning or any other messages -->
