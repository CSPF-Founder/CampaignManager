<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

use App\Models\Enums\ConstituencyStrength;
use Core\View;

?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#constituency-table').DataTable( {
            "iDisplayLength": 50,
            "order": [],
            "columnDefs": [
                {"targets": 'no-sort', "orderable": false},
                { "width": "2%", "targets": 0 },
                { "width": "50%", "targets": 1 },
            ]
        } );
    });
</script>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Constituency List</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Constituency</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <?php if(isset($constituency_list) && $constituency_list) : ?>
                        <div class="x_content">
                            <div class="table-responsive table-div">
                                <!-- start pop-over -->
                                <div class="bs-example-popovers">
                                    <button type="button" class="btn btn-danger delete-selected-rows" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." data-original-title="" title="">
                                        Delete Selected
                                    </button>
                                </div>
                                <!-- end pop-over -->
                                <table style="width: 99.9%" id="constituency-table" class="table table-striped jambo_table bulk_action">
                                    <thead >
                                    <tr class="headings" >
                                        <th class="text-center no-sort" >
                                            <input type="checkbox" id="check-all" class="flat">
                                        </th>
                                        <th class="text-left">Name</th>
                                        <th class="text-left">Strength</th>
                                    </tr>
                                    </thead>

                                    <tbody class="text-left">
                                    <?php /** @var \App\Models\Constituency $constituency */ ?>
                                    <?php foreach ($constituency_list as $constituency): ?>
                                        <tr class="even pointer" data-row-id="<?php View::securePrint($constituency->getId()); ?>">
                                            <td class="text-center">
                                                <input type="checkbox" class="flat" name="table_records" value="<?php View::securePrint($constituency->getId()); ?>">
                                            </td>
                                            <td class="name-column"><?php View::securePrint($constituency->getName()); ?></td>
                                            <td class="name-column" data-order="<?php View::securePrint($constituency->getStrength()); ?>">
                                                <?php if($constituency->getStrength() == ConstituencyStrength::STRONG): ?>
                                                    <span class="table-label table-label-100px label-success">
                                                        <?php View::securePrint($constituency->getStrength($format='text')); ?>
                                                    </span>
                                                <?php elseif ($constituency->getStrength() == ConstituencyStrength::MODERATE): ?>
                                                    <span class="table-label table-label-100px label-warning">
                                                        <?php View::securePrint($constituency->getStrength($format='text')); ?>
                                                    </span>
                                                <?php elseif ($constituency->getStrength() == ConstituencyStrength::WEAK): ?>
                                                    <span class="table-label table-label-100px label-danger">
                                                        <?php View::securePrint($constituency->getStrength($format='text')); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    <?php else: ?>
                        No Constituency found.
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->