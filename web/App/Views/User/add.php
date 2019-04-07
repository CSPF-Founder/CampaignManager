<?php

use App\Auth;
use App\Models\Organization;
use Core\Role;
use Core\View;

?>
<script>
    $(document).ready(function () {

        $('#constituency-div').hide();
        $('#feeder-user-type-div').hide();
        $('#mobile-number-div').hide();
        $('#email-div').hide();
        $('#user-role').change(function(){
            if($('#user-role').val() == 'feeder') {
                $('#constituency-div').show();
                $('#feeder-user-type-div').show();
                $('#mobile-number-div').show();
                $('#email-div').show();
                $('#constituency').prop('required',true);
                $('#feeder_user_type').prop('required',true);
            } else {
                $('#constituency').prop('required',false);
                $('#feeder_user_type').prop('required',false);
                $('#constituency-div').hide();
                $('#feeder-user-type-div').hide();
                $('#mobile-number-div').hide();
                $('#email-div').hide();
            }
        });

        $(document).on('submit', '#add-user-form', function (event) {
            event.preventDefault();
            var url = "/user/add";

            $.post(url, $("#add-user-form").serialize(), function (response, status) {
                    if (response.redirect) {
                        redirectToLogin(response.redirect);
                    }
                    else if (response.success) {
                        showSuccess(response.success)
                        $("#add-user-form")[0].reset();
                    }
                    else {
                        showError(response.error);
                    }
                },
                "json").fail(function (response) {
                showError('Error occurred');
            });
        });

    });
</script>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>User
                    <small>add</small>
                </h3>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Add User</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br/>
                        <form id="add-user-form" method="POST" data-parsley-validate autocomplete="off"
                              class="form-horizontal form-label-left">

                            <?php if(Auth::user()->hasRole('super_admin') === true && isset($organization_list) && $organization_list): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="org_id">User Organization
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select id="org_id" name="org_id" required="required" class="form-control col-md-7 col-xs-12">
                                            <option></option>
                                            <?php /** @var Organization $org */ ?>
                                            <?php foreach ($organization_list as $org): ?>
                                                <option value="<?php View::securePrint($org->getId()); ?>">
                                                    <?php View::securePrint($org->getName()); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input autocomplete="off" type="text" id="name" placeholder="Name" name="name"
                                           required="required" class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="username">User Name <span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input autocomplete="off" type="text" id="username" placeholder="User Name" name="username"
                                           required="required" class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password">Password <span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="password" id="password" placeholder="Password" name="password"
                                           required="required" class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="role">User Role <span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="user-role" name="role" required="required"
                                            class="form-control col-md-7 col-xs-12">
                                        <option></option>
                                        <?php /** @var Role $role */  ?>
                                        <?php foreach (Role::all() as $role): ?>
                                            <?php if (!(isset($constituency_list) && $constituency_list) && $role->keyword === 'feeder') : ?>
                                                <option value="<?php View::securePrint($role->keyword); ?>" disabled title="Add constituency">
                                                    <?php View::securePrint($role->getDescription()); ?> (add constituency first)
                                                </option>
                                        <?php elseif (Auth::user()->hasRole('super_admin') && $role->keyword === 'feeder') : ?>
                                            <option value="<?php View::securePrint($role->keyword); ?>" disabled title="Add constituency">
                                                <?php View::securePrint($role->getDescription()); ?> (Disabled for now - Yet to do from here)
                                            </option>
                                        <?php else: ?>
                                            <option value="<?php View::securePrint($role->keyword); ?>" >
                                                <?php View::securePrint($role->getDescription()); ?>
                                            </option>
                                        <?php endif; ?>

                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="constituency-div">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="constituency">Constituency
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="constituency" name="constituency" required="required"
                                            class="form-control col-md-7 col-xs-12">
                                        <option></option>
                                        <?php /** @var \App\Models\Constituency $constituency */ ?>
                                        <?php if (isset($constituency_list) and $constituency_list): ?>
                                            <?php foreach ($constituency_list as $constituency): ?>
                                                <option value="<?php View::securePrint($constituency->getId()); ?>">
                                                    <?php View::securePrint($constituency->getName()); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="feeder-user-type-div">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="feeder_type">Feeder User Type
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="feeder_type" name="feeder_type"
                                            class="form-control col-md-7 col-xs-12">
                                        <option></option>
                                        <?php /** @var \App\Models\Constituency $constituency */ ?>
                                            <?php foreach (\App\Models\Enums\FeederType::ENUM_LIST as $index => $value): ?>
                                                <option value="<?php View::securePrint($index); ?>">
                                                    <?php View::securePrint($value); ?>
                                                </option>
                                            <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="mobile-number-div">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mobile_number">Mobile Number <span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" id="mobile_number" placeholder="Mobile Number" name="mobile_number" class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>

                            <div class="form-group" id="email-div">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mobile_number">Email <span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" id="email" placeholder="Email" name="email" class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button class="btn btn-primary" type="reset">Reset</button>
                                    <input type="submit" id="add-user-button" class="btn btn-success" value="Add"></input>
                                </div>
                            </div>

                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->