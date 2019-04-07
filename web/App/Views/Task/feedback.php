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

<!-- page content -->
<div class="right_col" role="main">
    <?php /** @var \App\Models\Task $task  */ if(isset($task) && $task): ?>
        <div class="">
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Task Details</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="table-responsive">
                                <table class="table table-striped jambo_table" >
                                    <tbody>
                                    <tr>
                                        <th style="width: 18%">Summary</th>
                                        <td><?php View::securePrint($task->getSummary()); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Constituency</th>
                                        <td><?php View::securePrint(\App\Models\Constituency::getNameFromId($task->getConstituencyId())); ?></td>
                                    </tr>
                                    <tr>
                                        <th>User</th>
                                        <td><?php View::securePrint(\App\Models\User::getNameFromId($task->getResponsibility())); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Media</th>
                                        <td>
                                            <?php /** @var \App\Models\TaskFile $media_file */ if(isset($media_files) && $media_files): ?>
                                                <?php foreach ($media_files as $media_file): ?>
                                                    <li style="padding-bottom: 14px;">
                                                        <a href="/task-file/<?php View::securePrint($media_file->getId()); ?>/view?task_id=<?php View::securePrint($task->getId()); ?>" target="_blank">
                                                            <?php View::securePrint($media_file->getFilename()); ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                No media files
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Feedback</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br/>
                    <form id="add-entry-form" method="POST" enctype="multipart/form-data" data-parsley-validate
                          autocomplete="off" class="form-horizontal form-label-left">

                        <?php if (isset($existing_feedback) && $existing_feedback): ?>


                            <div class="form-group">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="comments">Feedback
                                    comments
                                </label>
                                <div class="col-md-10">
                                    <ul class="list-unstyled message-list">
                                        <?php /** @var \App\Models\TaskFeedback $feedback */ foreach ($existing_feedback as $feedback): ?>
                                            <li>
                                                <a>
                                                <span class="message" style="font-size:14px;">
                                                    <pre><?php View::securePrint($feedback->getComment()); ?></pre>
                                                </span>
                                                    <div class="message-list-footer">
                                                        <span><i>posted by : <?php View::securePrint(User::getNameFromId($feedback->getUserId())); ?></i></span>
                                                        <span class="right-side">posted date : <?php View::securePrint($feedback->getDateTime());?></span>
                                                    </div>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="new_comment">New Comment
                                <span class="required">*</span>
                            </label>
                            <div class="col-md-10 col-sm-10 col-xs-12">
                                    <textarea id="new_comment" placeholder="Write New comment" rows="5"
                                              name="new_comment" required="required"
                                              class="form-control col-md-7 col-xs-12"></textarea>
                            </div>
                        </div>

                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-2">
                                <input type="submit" class="btn btn-success" value="Submit"/>
                            </div>
                        </div>

                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->

