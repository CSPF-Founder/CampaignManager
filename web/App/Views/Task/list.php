<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

use App\Models\Constituency;
use App\Models\Enums\ConstituencyStrength;
use App\Models\Enums\TaskCriticality;
use App\Models\Enums\TaskStatus;
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

            $.post("mark-as-verified", {id: task_id}, function (res){
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

        $('#other-incomplete-tasks-table').DataTable( {
            "iDisplayLength": 50,
            "searching": false,
            "order": [],
            "columnDefs": [
                { "width": "1%", "targets": 0 },
                { "width": "40%", "targets": 1 },
                { "width": "10%", "targets": 5 },
                { "width": "10%", "targets": 6 },

            ],
            "fnDrawCallback": function(oSettings) {
                if ($('#other-incomplete-tasks-table tr').length < 50) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }
            }
        } );
        $('#over-due-tasks-table').DataTable( {
            "iDisplayLength": 50,
            "searching": false,
            "order": [],
            "columnDefs": [
                { "width": "1%", "targets": 0 },
                { "width": "40%", "targets": 1 },
                { "width": "10%", "targets": 5 },
                { "width": "10%", "targets": 6 },
            ],
            "fnDrawCallback": function(oSettings) {
                if ($('#other-incomplete-tasks-table tr').length < 50) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }
            }
        } );
        $('#completed-tasks-to-verify-table').DataTable( {
            "iDisplayLength": 50,
            "searching": false,
            "order": [],
            "columnDefs": [
                { "width": "1%", "targets": 0 },
                { "width": "40%", "targets": 1 },
                { "width": "10%", "targets": 5 },
                { "width": "10%", "targets": 6 },
            ],
            "fnDrawCallback": function(oSettings) {
                if ($('#other-incomplete-tasks-table tr').length < 50) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }
            }
        } );
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
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Search Task</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" class="col-md-12 col-sm-12 col-xs-12">
                        <br />
                        <form id="search-task-form" method="POST" data-parsley-validate autocomplete="off" class="form-horizontal form-label-left">
                            <div class="row">
                                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="filter_keyword">Keyword
                                </label>
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <?php if(isset($filter) && $filter && isset($filter["keyword"])):?>
                                        <input type="text" id="filter_keyword" value="<?php View::securePrint($filter["keyword"]);?>" name="filter_keyword"  class="form-control col-md-7 col-xs-12">
                                    <?php else:?>
                                        <input type="text" id="filter_keyword" placeholder="Keyword" name="filter_keyword"  class="form-control col-md-7 col-xs-12">
                                    <?php endif;?>
                                </div>
                                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="filter_from_date">From Date
                                </label>
                                <div class="col-md-2 col-sm-2 col-xs-12">
                                    <?php if(isset($filter) && $filter && isset($filter["from_date"]) && $filter["from_date"] instanceof DateTime):?>
                                        <input style="line-height: 23px;" type="date" id="filter_from_date" value="<?php View::securePrint($filter["from_date"]->format('Y-m-d')); ?>" name="filter_from_date"  class="form-control col-md-7 col-xs-12">
                                    <?php else: ?>
                                        <input style="line-height: 23px;" type="date" id="filter_from_date" name="filter_from_date"  class="form-control col-md-7 col-xs-12">
                                    <?php endif; ?>
                                </div>

                                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="filter_to_date">To Date
                                </label>
                                <div class="col-md-2 col-sm-2 col-xs-12">
                                    <?php if(isset($filter) && $filter && isset($filter["to_date"]) && $filter["to_date"] instanceof DateTime):?>
                                        <input style="line-height: 23px;" type="date" id="filter_to_date" value="<?php View::securePrint($filter["to_date"]->format('Y-m-d')); ?>" name="filter_to_date"  class="form-control col-md-7 col-xs-12">
                                    <?php else: ?>
                                        <input style="line-height: 23px;" type="date" id="filter_to_date" name="filter_to_date"  class="form-control col-md-7 col-xs-12">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row" style="padding-top:8px;">
                                    <label class="control-label col-md-1 col-sm-1 col-xs-12" for="filter_constituency">  Constituency
                                    </label>
                                    <div class="col-md-3 col-sm-3 col-xs-12">
                                        <select id="filter_constituency" name="filter_constituency" class="form-control" >
                                            <option></option>
                                            <?php if(isset($constituency_list) && $constituency_list): ?>
                                                <?php /** @var Constituency $constituency */ foreach ($constituency_list as $constituency): ?>
                                                    <?php if(isset($filter) && $filter && isset($filter["constituency_id"]) && $filter["constituency_id"] == $constituency->getId()):?>
                                                        <option value="<?php View::securePrint($constituency->getId()); ?>" selected>
                                                            <?php View::securePrint($constituency->getName()); ?>
                                                        </option>
                                                    <?php else:?>
                                                        <option value="<?php View::securePrint($constituency->getId()); ?>">
                                                            <?php View::securePrint($constituency->getName()); ?>
                                                        </option>
                                                    <?php endif;?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <label class="control-label col-md-1 col-sm-1 col-xs-12" for="filter_criticality">  Criticality
                                    </label>
                                    <div class="col-md-2 col-sm-2 col-xs-12">
                                        <select id="filter_criticality" name="filter_criticality" class="form-control col-md-7 col-xs-12" >
                                            <option></option>
                                            <?php foreach (TaskCriticality::ENUM_LIST as $index => $value): ?>
                                                <?php if(isset($filter) && $filter && isset($filter["criticality"]) && $filter["criticality"] == $index):?>
                                                    <option value="<?php View::securePrint($index); ?>" selected>
                                                        <?php View::securePrint($value); ?>
                                                    </option>
                                                <?php else:?>
                                                    <option value="<?php View::securePrint($index); ?>">
                                                        <?php View::securePrint($value); ?>
                                                    </option>
                                                <?php endif;?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="filter_constituency_strength">  Constituency Strength
                                </label>
                                <div class="col-md-2 col-sm-2 col-xs-12">
                                    <select id="filter_constituency_strength" name="filter_constituency_strength" class="form-control col-md-7 col-xs-12" >
                                        <option></option>
                                        <?php foreach (ConstituencyStrength::ENUM_LIST as $index => $value): ?>
                                            <?php if(isset($filter) && $filter && isset($filter["constituency_strength"]) && $filter["constituency_strength"] == $index):?>
                                                <option value="<?php View::securePrint($index); ?>" selected>
                                                    <?php View::securePrint($value); ?>
                                                </option>
                                            <?php else:?>
                                                <option value="<?php View::securePrint($index); ?>">
                                                    <?php View::securePrint($value); ?>
                                                </option>
                                            <?php endif;?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <br/><br/>
                                <div class="col-md-1 col-sm-1 col-xs-12"></div>
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <input type="submit"  class="btn btn-success" value="Search"/>
                                    <a href="/task/list" class="btn btn-success">Clear</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

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
                                <table style="width: 99.9%" id="over-due-tasks-table" class="table table-striped jambo_table bulk_action">
                                    <thead>
                                    <tr class="headings">
                                        <th width="96px;">Date</th>
                                        <th>Headline</th>
                                        <th><i class="fa fa-paperclip"></i></th>
                                        <th width="200px;">Constituency</th>
                                        <th width="180px;">Responsibility</th>
                                        <th width="87px;">Criticality</th>
                                        <th width="96px;">Due Date</th>
                                        <th width="50px;">Action</th>
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
                                            <td><?php View::securePrint(User::getNameFromId($task->getResponsibility())); ?></td>
                                            <td data-order="<?php View::securePrint($task->getCriticality()); ?>">
                                                <span class="bg-red task-criticality <?php View::securePrint($task->getCriticality('text')); ?>">
                                                    <?php View::securePrint($task->getCriticality('text')); ?>
                                                </span>
                                            </td>
                                            <td><?php View::securePrint($task->getDueDate()); ?></td>
                                            <td>
                                                <a style="width: 100%" class="btn btn-sm btn-primary" href="<?php View::securePrint($task->getId()); ?>/view">
                                                    View
                                                </a>
                                                <a style="width: 100%" class="btn btn-sm btn-primary" href="<?php View::securePrint($task->getId()); ?>/edit">
                                                    Edit
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
                        <h2>Other Incomplete Tasks</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                                <table style="width: 99.9%" id="other-incomplete-tasks-table" class="table table-striped jambo_table bulk_action">
                                    <thead>
                                    <tr class="headings">
                                        <th width="96px;">Date</th>
                                        <th>Headline</th>
                                        <th><i class="fa fa-paperclip"></i></th>
                                        <th width="200px;">Constituency</th>
                                        <th width="180px;">Responsibility</th>
                                        <th width="87px;">Criticality</th>
                                        <th width="96px;">Due Date</th>
                                        <th width="50px;">Action</th>
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
                                            <td><?php View::securePrint(User::getNameFromId($task->getResponsibility())); ?></td>
                                            <td data-order="<?php View::securePrint($task->getCriticality()); ?>">
                                                <span class="bg-red task-criticality <?php View::securePrint($task->getCriticality('text')); ?>">
                                                    <?php View::securePrint($task->getCriticality('text')); ?>
                                                </span>
                                            </td>
                                            <td><?php View::securePrint($task->getDueDate()); ?></td>
                                            <td>
                                                <a style="width: 100%;" class="btn btn-sm btn-primary" href="<?php View::securePrint($task->getId()); ?>/view">
                                                    View
                                                </a>
                                                <a style="width: 100%;" class="btn btn-sm btn-primary" href="<?php View::securePrint($task->getId()); ?>/edit">
                                                    Edit
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

        <?php if (isset($tasks_to_verify) and $tasks_to_verify): ?>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Completed Tasks to be verified</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                                <table style="width: 99.9%" id="completed-tasks-to-verify-table" class="table table-striped jambo_table bulk_action">
                                    <thead>
                                    <tr class="headings">
                                        <th width="96px;">Date</th>
                                        <th>Headline</th>
                                        <th><i class="fa fa-paperclip"></i></th>
                                        <th width="200px;">Constituency</th>
                                        <th width="180px;">Responsibility</th>
                                        <th width="87px;">Criticality</th>
                                        <th width="96px;">Due Date</th>
                                        <th width="50px;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php /** @var \App\Models\Task $task */ ?>
                                    <?php foreach ($tasks_to_verify as $task): ?>
                                        <tr data-row-id="<?php View::securePrint($task->getId()); ?>">
                                            <td><?php View::securePrint($task->getCreatedDate()); ?></td>
                                            <td class="long-table-text"><span><?php View::securePrint($task->getHeadline()); ?></span></td>
                                            <td>
                                                <?php if($task->getMediaCount()): ?>
                                                    <a href="<?php View::securePrint($task->getId()); ?>/view"><i class="fa fa-paperclip " ></i></a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php View::securePrint(Constituency::getNameFromId($task->getConstituencyId())); ?></td>
                                            <td><?php View::securePrint(User::getNameFromId($task->getResponsibility())); ?></td>
                                            <td data-order="<?php View::securePrint($task->getCriticality()); ?>">
                                                <span class="bg-red task-criticality <?php View::securePrint($task->getCriticality('text')); ?>">
                                                    <?php View::securePrint($task->getCriticality('text')); ?>
                                                </span>

                                            </td>
                                            <td><?php View::securePrint($task->getDueDate()); ?></td>
                                            <td>
                                                <a style="width: 100%" class="btn btn-sm btn-primary" href="<?php View::securePrint($task->getId()); ?>/view">
                                                    View
                                                </a>
                                                <button style="width: 100%" class="btn btn-sm btn-primary task-completed-button">
                                                    Mark as Verified
                                                </button>
                                                <a style="width: 100%" class="btn btn-sm btn-primary" href="<?php View::securePrint($task->getId()); ?>/edit">
                                                    Edit
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

