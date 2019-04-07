<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

use App\Models\User;
use Core\View;

?>
<script>
    $(document).ready(function () {

        $('.add-file-input-field').click(function(){
            var dynamic_form_group = $(this).closest(".dynamic-form-group");
            dynamic_form_group.append('<div style="padding-top:50px;" class="dynamic-input-div">    <label class="col-sm-3 control-label">        File(s)    </label>    <div class="col-sm-9">    <span class="btn btn-default btn-file">        <input name="files[]" type="file" class="file" multiple data-show-upload="true" data-show-caption="true">    </span>        <button type="button" name="add" id="add" class="btn btn-danger remove-dynamic-input"><b>X</b></button>    </div></div>');
        });

        $(document).on('click', '.remove-dynamic-input', function(){
            var dynamic_input_div = $(this).closest(".dynamic-input-div");
            dynamic_input_div.remove();
        });
    });
</script>


<!-- page content -->
<div class="right_col" role="main">
    <?php if(isset($feeder_list) && $feeder_list): ?>
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Task</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Create Task</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br />
                        <form id="add-entry-form" method="POST" enctype="multipart/form-data" data-parsley-validate autocomplete="off" class="form-horizontal form-label-left">
                        <?php /** @var \App\Models\Feed $feed */ if(isset($feed) && $feed): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="headline">Headline <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="headline" placeholder="Headline" name="headline" value="<?php View::securePrint($feed->getHeadline()); ?>" required="required" class="form-control col-md-7 col-xs-12">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Summary <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="summary" placeholder="Summary" rows="5" name="summary" required="required" class="form-control col-md-7 col-xs-12"><?php View::securePrint($feed->getSummary()); ?></textarea>
                                    </div>
                                </div>
                        <?php else: ?>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="headline">Headline <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="headline" placeholder="Headline" name="headline" required="required" class="form-control col-md-7 col-xs-12">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Summary <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="summary" placeholder="Summary" rows="5" name="summary" required="required" class="form-control col-md-7 col-xs-12"></textarea>
                                    </div>
                                </div>

                        <?php endif; ?>


                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="responsibility">Responsibility
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="responsibility" name="responsibility" class="form-control col-md-7 col-xs-12"  required>
                                        <option></option>
                                        <?php /** @var User $feeder */ foreach ($feeder_list as $feeder): ?>
                                            <option value="<?php View::securePrint($feeder->getId()); ?>">
                                                <?php View::securePrint($feeder->getName()); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="criticality">Criticality
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="criticality" name="criticality" class="form-control col-md-7 col-xs-12" required>
                                        <option></option>
                                        <?php foreach (\App\Models\Enums\TaskCriticality::ENUM_LIST as $index => $value): ?>
                                            <option value="<?php View::securePrint($index); ?>">
                                                <?php View::securePrint($value); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="due_date">Due Date
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="date" name="due_date" min="<?php View::securePrint(date("Y-m-d"))?>"  required />
                                </div>
                            </div>
                            <?php /** @var \App\Models\FeedFile $media_file */ if(isset($feed) && $feed && isset($feed_files) && $feed_files): ?>
                                <div class="form-group dynamic-form-group">
                                    <label class="col-sm-3 control-label">
                                        Files from feed<br/>
                                        (These files will be automatically attached)
                                    </label>
                                    <div class="col-sm-9" style="padding-left:30px;">
                                        <?php foreach ($feed_files as $media_file): ?>
                                            <li>
                                                <a href="/feed-file/<?php View::securePrint($media_file->getId()); ?>/view?feed_id=<?php View::securePrint($feed->getId()); ?>" target="_blank">
                                                    <?php View::securePrint($media_file->getFilename()); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <input type="hidden" name="feed_source" value="<?php View::securePrint($feed->getId());?>"/>
                            <?php endif; ?>

                            <div class="form-group dynamic-form-group">
                                <div>
                                    <label class="col-sm-3 control-label">
                                        Files (optional)
                                    </label>
                                    <div class="col-sm-9">
                                    <span class="btn btn-default btn-file">
                                        <input name="files[]" type="file" class="file" multiple data-show-upload="true" data-show-caption="true">
                                    </span>
                                        <button type="button" name="add" class="btn btn-success add-file-input-field">Add More</button>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <input type="submit"  class="btn btn-success" value="Create"/>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    Feeder List Empty - Please add users !
    <?php endif; ?>
</div>
<!-- /page content -->
