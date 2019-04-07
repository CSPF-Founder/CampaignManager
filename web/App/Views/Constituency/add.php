<?php

use App\Auth;
use App\Models\Organization;
use Core\View;


?>
<script>
    $(document).ready(function () {
        $(document).on("submit", "#add-entry-form", function(event){
            event.preventDefault();
            $.post("add", $("#add-entry-form").serialize(), function(response,status) {
                    if(response.redirect) { redirectToLogin(response.redirect); }
                    else if(response.success) {
                        showSuccess(response.success)
                        $("#add-entry-form")[0].reset();
                    }
                    else{
                        showError(response.error);
                    }
                },
                "json").fail(function(response) {
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
                <h3>Constituency <small>add</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Add Constituency</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br />
                        <form id="add-entry-form" method="POST" data-parsley-validate autocomplete="off" class="form-horizontal form-label-left">
                            <?php if(Auth::user()->hasRole('super_admin') === true && isset($organization_list) && $organization_list): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="organization">User Organization
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select id="organization" name="organization" required="required" class="form-control col-md-7 col-xs-12">
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
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Constituency Name<span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="name" placeholder="Constituency Name" class="form-control col-md-7 col-xs-12" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="strength">Constituency Strength<span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="strength" name="strength"
                                            class="form-control col-md-7 col-xs-12" required>
                                        <?php foreach (\App\Models\Enums\ConstituencyStrength::ENUM_LIST as $index => $value): ?>
                                            <option value="<?php View::securePrint($index); ?>">
                                                <?php View::securePrint($value); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

<!--                            <div class="form-group">-->
<!--                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Constituency List <span class="required">*</span>-->
<!--                                </label>-->
<!--                                <div class="col-md-6 col-sm-6 col-xs-12">-->
<!--                                    <textarea id="constituency_list" rows="5" placeholder="Constituency List (separated by NEW LINE)" name="constituency_list" required="required" class="form-control col-md-7 col-xs-12"></textarea>-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <input type="submit"  class="btn btn-success" value="Add"/>
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