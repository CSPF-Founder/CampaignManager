<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

use Core\View;

?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Organization List</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Organization</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <?php if(isset($organization_list) && $organization_list) : ?>
                        <div class="x_content">
                            <div class="table-responsive table-div">
                                <!-- start pop-over -->
                                <div class="bs-example-popovers">
                                    <button type="button" class="btn btn-danger delete-selected-rows" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." data-original-title="" title="">
                                        Delete Selected
                                    </button>
                                </div>
                                <!-- end pop-over -->
                                <table id="user-list-table" class="table table-striped jambo_table bulk_action">
                                    <thead >
                                    <tr class="headings" >
                                        <th class="text-center" style="width:40px">
                                            <input type="checkbox" id="check-all" class="flat">
                                        </th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Maximum Constituency</th>
                                    </tr>
                                    </thead>

                                    <tbody class="text-center">
                                    <?php /** @var \App\Models\Organization $organization */ ?>
                                    <?php foreach ($organization_list as $organization): ?>
                                        <tr class="even pointer" id="row_<?php View::securePrint($organization->getId()); ?>" data-row-id="<?php View::securePrint($organization->getId()); ?>">
                                            <td class="a-center">
                                                <input type="checkbox" class="flat" name="table_records" value="<?php View::securePrint($organization->getId()); ?>">
                                            </td>
                                            <td class="name-column"><?php View::securePrint($organization->getName()); ?></td>
                                            <td class="name-column"><?php View::securePrint($organization->getMaxConstituencyCount()); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    <?php else: ?>
                        No Organization found.
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->