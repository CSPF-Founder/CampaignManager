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

    });
</script>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">

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
                            <div class="form-group">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="headline">Task Headline
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-10 col-sm-10 col-xs-12">
                                    <input type="text" readonly id="headline"
                                           value="<?php View::securePrint($task->getHeadline()); ?>"
                                           class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>

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
</div>
<!-- /page content -->
