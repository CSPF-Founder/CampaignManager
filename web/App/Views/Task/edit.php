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
            dynamic_form_group.append('<div style="padding-top:50px;" class="dynamic-input-div">    <label class="control-label col-md-2 col-sm-2 col-xs-12">        File(s)    </label>    <div class="col-md-10 col-sm-10 col-xs-12">    <span class="btn btn-default btn-file">        <input name="files[]" type="file" class="file" multiple data-show-upload="true" data-show-caption="true">    </span>        <button type="button" name="add" id="add" class="btn btn-danger remove-dynamic-input"><b>X</b></button>    </div></div>');
        });

        $(document).on('click', '.remove-dynamic-input', function(){
            var dynamic_input_div = $(this).closest(".dynamic-input-div");
            dynamic_input_div.remove();
        });
    });
</script>


<!-- page content -->
<div class="right_col" role="main">
    <?php /** @var \App\Models\Task $task */ if(isset($feeder_list) && $feeder_list && isset($task) && $task): ?>
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
                            <h2>Update Task</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />
                            <form id="add-entry-form" method="POST" enctype="multipart/form-data" data-parsley-validate autocomplete="off" class="form-horizontal form-label-left">

                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="headline">Headline
                                    </label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <input type="text" id="headline" name="headline" value="<?php View::securePrint($task->getHeadline());?>" class="form-control col-md-7 col-xs-12">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Summary
                                    </label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <textarea id="summary" name="summary"  rows="5" class="form-control col-md-7 col-xs-12"><?php View::securePrint($task->getSummary());?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="responsibility">Reassign
                                    </label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <select id="responsibility" name="responsibility" class="form-control col-md-7 col-xs-12"  required>
                                            <?php /** @var User $feeder */ foreach ($feeder_list as $feeder): ?>
                                                <option value="<?php View::securePrint($feeder->getId()); ?>" <?php if($task->getResponsibility() == $feeder->getId()) View::securePrint("selected");?> >
                                                    <?php View::securePrint($feeder->getName()); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="criticality">Criticality
                                    </label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <select id="criticality" name="criticality" class="form-control col-md-7 col-xs-12" required>
                                            <?php foreach (\App\Models\Enums\TaskCriticality::ENUM_LIST as $index => $value): ?>
                                                <option value="<?php View::securePrint($index); ?>" <?php if($task->getCriticality() == $index) View::securePrint("selected");?>>
                                                    <?php View::securePrint($value); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="due_date">Due Date
                                    </label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <input type="date" value="<?php View::securePrint($task->getDueDate('Y-m-d')) ?>" name="due_date" min="<?php View::securePrint(date("Y-m-d"))?>"  required />
                                    </div>
                                </div>

                                <?php /** @var \App\Models\FeedFile $media_file */ if(isset($media_files) && $media_files): ?>
                                    <div class="form-group dynamic-form-group">
                                        <label class="col-md-2 col-sm-2 col-xs-12 control-label">
                                            Existing Files
                                        </label>
                                        <div class="col-md-10 col-sm-10 col-xs-12" style="padding-left:30px;">
                                            <?php foreach ($media_files as $media_file): ?>
                                                <li>
                                                    <a href="/task-file/<?php View::securePrint($media_file->getId()); ?>/view?task_id=<?php View::securePrint($task->getId()); ?>" target="_blank">
                                                        <?php View::securePrint($media_file->getFilename()); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group dynamic-form-group">
                                    <div>
                                        <label class="col-md-2 col-sm-2 col-xs-12 control-label">
                                            New Files (optional)
                                        </label>
                                        <div class="col-md-10 col-sm-10 col-xs-12">
                                    <span class="btn btn-default btn-file">
                                        <input name="files[]" type="file" class="file" multiple data-show-upload="true" data-show-caption="true">
                                    </span>
                                            <button type="button" name="add" class="btn btn-success add-file-input-field">Add More</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-8 col-sm-6 col-xs-12 col-md-offset-2">
                                        <input type="submit"  class="btn btn-success" value="Update"/>
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
