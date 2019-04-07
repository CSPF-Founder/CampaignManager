<!-- page content -->
<?php

use Core\Role;
use Core\View;

?>


<script type="text/javascript">
    $(document).ready(function() {
        $("#sync-permissions-button").click(function () {
            $.post('sync-permissions',{ <?php \Core\Security\CSRF::addAjaxField(); ?>},
                function (res) {
                    if (res.redirect) {
                        redirectToLogin(res.redirect);
                    }
                    else if (res.error) {
                        showError(res.error);
                    }
                    else if (res.success) {
                        showSuccess(res.success);
                        row.remove();
                    }
                    else {
                        showError("Unexpected Error !");
                    }
                }
                , "json").fail(function (res) {
                showError(res.statusText, "Error-" + res.status);
            });
        });
    });
</script>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>User Roles</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="x_content">
            <div class="table-responsive table-div">
                <!-- starting table buttons -->
                <div class="bs-example-popovers">
                    <button id="sync-permissions-button" type="button" class="btn btn-primary" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." data-original-title="" title="">
                        Sync Permissions
                    </button>
                </div>
            </div>
        </div>

        <?php if(isset($roles) && $roles && isset($permissions) && $permissions) : ?>
            <?php /** @var Role $role */ foreach($roles as $role): ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h3><?php View::securePrint($role->getDescription()); ?></h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <table>
                                    <?php $i=0; ?>
                                    <tr>
                                        <?php /** @var \Core\Permission $permission */ foreach($permissions as $permission): ?>
                                        <?php if($i>5): $i =0;?>
                                    </tr><tr>
                                        <?php endif; ?>
                                        <td style="padding:3px;">
                                            <?php if(in_array($permission->keyword, array_keys($role->getPermissions()))): ?>
                                                <input disabled name="permissions[]" class="permissions" type="checkbox" checked value="<?php View::securePrint($permission->keyword); ?>">
                                            <?php else: ?>
                                                <input disabled name="permissions[]" class="permissions" type="checkbox" value="<?php View::securePrint($permission->keyword); ?>">
                                            <?php endif; ?>
                                            <?php View::securePrint($permission->keyword); ?>
                                        </td>
                                        <?php $i++; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                </table>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;; ?>
        <?php else: ?>
            No Roles/Permissions found.
        <?php endif; ?>
    </div>
</div>
<!-- /page content -->