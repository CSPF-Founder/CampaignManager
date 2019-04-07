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
    <?php /** @var \App\Models\Feed $feed  */ if(isset($feed) && $feed): ?>
        <div class="">
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Feed Details</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-4 ">Headline</div>
                                <div class="col-md-10 col-sm-6 col-xs-8"><?php View::securePrint($feed->getHeadline()); ?></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-4">Summary</div>
                                <div class="col-md-10 col-sm-8 col-xs-8"><?php View::securePrint($feed->getSummary()); ?></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-4 ">Constituency</div>
                                <div class="col-md-10 col-sm-8 col-xs-8"><?php View::securePrint(\App\Models\Constituency::getNameFromId($feed->getConstituencyId())); ?></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-4 ">User</div>
                                <div class="col-md-10 col-sm-10 col-xs-8"><?php View::securePrint(\App\Models\User::getNameFromId($feed->getUserId())); ?></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-4 ">Added By</div>
                                <div class="col-md-10 col-sm-10 col-xs-8"><?php View::securePrint(\App\Models\User::getNameFromId($feed->getAddedBy())); ?></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-4 ">Media</div>
                                <div class="col-md-10 col-sm-10 col-xs-8">
                                    <?php /** @var \App\Models\FeedFile $media_file */ if(isset($media_files) && $media_files): ?>
                                        <?php foreach ($media_files as $media_file): ?>
                                            <li style="padding-bottom: 14px;">
                                                <a href="/feed-file/<?php View::securePrint($media_file->getId()); ?>/view?feed_id=<?php View::securePrint($feed->getId()); ?>" target="_blank">
                                                    <?php View::securePrint($media_file->getFilename()); ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        No media files
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<!-- /page content -->

