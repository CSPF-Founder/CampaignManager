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

            $(".ignore-feed-button").on("click", function (e) {
                e.preventDefault();
                if(!confirm('Are you sure want to ignore?')){
                    return;
                }

                var row = $(this).closest('tr');
                var feed_id = row.data("row-id");

                $.post("ignore", {id: feed_id}, function (res){
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

            $('#feed-list-table').DataTable( {
                "iDisplayLength": 50,
                "order": [],
                "columnDefs": [
                    { "width": "1%", "targets": 0 },
                    { "width": "40%", "targets": 1 },
                    { "width": "1%", "targets": 6 }
                ]
            } );
        });
    </script>


<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Feed List</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Input Feeds</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                            <?php if (isset($feed_list) and $feed_list): ?>
                                <table style="width:99.9%" id="feed-list-table" class="table table-striped jambo_table bulk_action">
                                    <thead>
                                    <tr class="headings">
                                        <th width="40px"><input type="checkbox" id="check-all" class="flat"></th>
                                        <th>Headline</th>
                                        <th><i class="fa fa-paperclip"></i></th>
                                        <th>Constituency</th>
                                        <th>User</th>
                                        <th>Added By</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php /** @var \App\Models\Feed $feed */ ?>
                                    <?php foreach ($feed_list as $feed): ?>
                                        <tr data-row-id="<?php View::securePrint($feed->getId()); ?>">
                                            <td class="a-center">
                                                <input type="checkbox" class="flat" name="table_records">
                                            </td>
                                            <td><?php View::securePrint($feed->getHeadline()); ?></td>
                                            <td>
                                                <?php if($feed->getMediaCount()): ?>
                                                    <a href="<?php View::securePrint($feed->getId()); ?>/view"><i class="fa fa-paperclip " ></i></a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php View::securePrint(Constituency::getNameFromId($feed->getConstituencyId())); ?></td>
                                            <td><?php View::securePrint(User::getNameFromId($feed->getUserId())); ?></td>
                                            <td><?php View::securePrint(User::getNameFromId($feed->getAddedBy())); ?></td>
                                            <td>
                                                <a style="width: 100%" class="btn btn-sm btn-primary" href="<?php View::securePrint($feed->getId()); ?>/view">
                                                    View
                                                </a>
                                                <a style="width: 100%" class="btn btn-sm btn-primary create-task-button" href="/task/add?feed_id=<?php View::securePrint($feed->getId()); ?>">
                                                    Create Task
                                                </a>
                                                <button style="width: 100%" class="btn btn-sm btn-primary ignore-feed-button" >
                                                    Ignore feed
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <h4>No Feeds found</h4>
                            <?php endif ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->

