<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

use App\Models\Constituency;
use App\Models\User;
use Core\View;

?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".task-completed-button").on("click", function (e) {
            e.preventDefault();
            if(!confirm('Are you sure want to mark it as completed?')){
                return;
            }

            var row = $(this).closest('tr');
            var task_id = row.data("row-id");

            $.post("mark-as-completed", {id: task_id}, function (res){
                    if (res.redirect){
                        redirectToLogin(res.redirect);
                    }
                    if (res.error){
                        showError(res.error);
                    }
                    else if (res.success){
                        showSuccess(res.success);
                        row.remove();
                    }
                    else{
                        showError("Unexpected Error !");
                    }
                }
                , "json").fail(function(response) {
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
                <h3>Task List</small></h3>
            </div>
        </div>


        <div class="clearfix"></div>
        <?php if (isset($overdue_task_list) and $overdue_task_list): ?>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Overdue Tasks</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                                <table id="url-list-table" class="table table-striped jambo_table bulk_action">
                                    <thead>
                                    <tr class="headings">
                                        <th>Date</th>
                                        <th>Headline</th>
                                        <th><i class="fa fa-paperclip"></i></th>
                                        <th>Constituency</th>
                                        <th>Criticality</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php /** @var \App\Models\Task $task */ ?>
                                    <?php foreach ($overdue_task_list as $task): ?>
                                        <tr data-row-id="<?php View::securePrint($task->getId()); ?>">
                                            <td><?php View::securePrint($task->getCreatedDate()); ?></td>
                                            <td class="long-table-text"><span><?php View::securePrint($task->getHeadline()); ?></span></td>
                                            <td>
                                                <?php if($task->getMediaCount()): ?>
                                                    <a href="<?php View::securePrint($task->getId()); ?>/view"><i class="fa fa-paperclip " ></i></a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php View::securePrint(Constituency::getNameFromId($task->getConstituencyId())); ?></td>
                                            <td>
                                                <span class="bg-red task-criticality <?php View::securePrint($task->getCriticality('text')); ?>">
                                                    <?php View::securePrint($task->getCriticality('text')); ?>
                                                </span>
                                            </td>
                                            <td><?php View::securePrint($task->getStatusText()); ?></td>
                                            <td><?php View::securePrint($task->getDueDate()); ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="<?php View::securePrint($task->getId()); ?>/view?hide_feedback=1">
                                                    View
                                                </a>
                                                <button class="btn btn-sm btn-primary task-completed-button">
                                                    Mark Completed
                                                </button>
                                                <a class="btn btn-sm btn-primary" href="/task/<?php View::securePrint($task->getId()); ?>/view">
                                                    Feedback
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>

        <?php if (isset($task_list) and $task_list): ?>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Assigned Tasks</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">

                                <table id="url-list-table" class="table table-striped jambo_table bulk_action">
                                    <thead>
                                    <tr class="headings">
                                        <th>Date</th>
                                        <th>Headline</th>
                                        <th><i class="fa fa-paperclip"></i></th>
                                        <th>Constituency</th>
                                        <th>Criticality</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php /** @var \App\Models\Task $task */ ?>
                                    <?php foreach ($task_list as $task): ?>
                                        <tr data-row-id="<?php View::securePrint($task->getId()); ?>">
                                            <td><?php View::securePrint($task->getCreatedDate()); ?></td>
                                            <td class="long-table-text"><span><?php View::securePrint($task->getHeadline()); ?></span></td>
                                            <td>
                                                <?php if($task->getMediaCount()): ?>
                                                    <a href="<?php View::securePrint($task->getId()); ?>/view"><i class="fa fa-paperclip " ></i></a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php View::securePrint(Constituency::getNameFromId($task->getConstituencyId())); ?></td>
                                            <td>
                                                <span class="bg-red task-criticality <?php View::securePrint($task->getCriticality('text')); ?>">
                                                    <?php View::securePrint($task->getCriticality('text')); ?>
                                                </span>
                                            </td>
                                            <td><?php View::securePrint($task->getStatusText()); ?></td>
                                            <td><?php View::securePrint($task->getDueDate()); ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="<?php View::securePrint($task->getId()); ?>/view?hide_feedback=1">
                                                    View
                                                </a>
                                                <button class="btn btn-sm btn-primary task-completed-button">
                                                    Mark Completed
                                                </button>
                                                <a class="btn btn-sm btn-primary" href="/task/<?php View::securePrint($task->getId()); ?>/view">
                                                    Feedback
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>
    </div>
</div>
<!-- /page content -->

