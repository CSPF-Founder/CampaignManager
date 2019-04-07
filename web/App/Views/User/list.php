<!-- page content -->
<?php

use App\Auth;
use Core\View;

?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".delete-button").click(function () {
            var row = $(this).closest('tr');
            var row_id = row.attr("id").replace("row_", "");
            var url = "/user/" + row_id +"/delete";

            $("#confirm-prompt").find(".modal-body").text("Delete the User?");
            $('#confirm-prompt').modal({ backdrop: 'static', keyboard: false
            }).one('click', '#prompt-confirm-button', function(e) {
                $.post(url,{ <?php \Core\Security\CSRF::addAjaxField(); ?>},
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
        });
</script>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>User List</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Users</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <?php if(isset($user_list) && $user_list) : ?>
                        <div class="x_content">
                            <div class="table-responsive table-div">
                                <!-- starting table buttons -->
                                <div class="bs-example-popovers">
                                    <button type="button" class="btn btn-danger delete-selected-rows" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." data-original-title="" title="">
                                        Delete Selected
                                    </button>
                                </div>
                                <!-- end table buttons -->

                                <table id="user-list-table" class="table table-striped jambo_table bulk_action">
                                    <thead>
                                    <tr class="headings">
                                        <th class="text-center">
                                            <input type="checkbox" id="check-all" class="flat">
                                        </th>
                                        <th class="text-center">Name </th>
                                        <th class="text-center">Username </th>
                                        <th class="text-center">Role </th>
                                        <th class="no-link last text-center"><span class="nobr"></span>
                                        </th>
                                    </tr>
                                    </thead>

                                    <tbody class="text-center">
                                    <?php /** @var \App\Models\User $user  */ ?>
                                    <?php foreach ($user_list as $user): ?>
                                        <?php if(!$user->hasRole('super_admin') && Auth::user()->getId() !== $user->getId()): ?>
                                        <tr class="even pointer" id="row_<?php View::securePrint($user->getId()); ?>" data-row-id="<?php View::securePrint($user->getId()); ?>">
                                            <td class="a-center ">
                                                <input type="checkbox" class="flat" name="table_records" value="<?php View::securePrint($user->getId()); ?>">
                                            </td>
                                            <td ><?php View::securePrint($user->getName()); ?></td>
                                            <td ><?php View::securePrint($user->getUsername()); ?></td>
                                            <td >
                                                <?php foreach($user->getRolesDescription() as $role_desc):?>
                                                <?php View::securePrint($role_desc);?><br/>
                                                <?php endforeach; ?>
                                            </td>
                                            <td class=" last" >

                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    <?php else: ?>
                        No User found.
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->



