<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;


use App\Auth;
use App\Models\Feed;
use App\Models\FeedFile;
use App\Models\Organization;
use Core\AuthController;
use Core\Utils;
use Core\View;

class FeedFileController extends AuthController {

    /**
     * @throws \Core\AppError
     */
    protected function viewAction(){
        Auth::throwErrorIfPermissionDenied("view_FeedFile");

        if(!isset($this->route_params["id"])){
            View::throwAppError("Invalid Request");
        }

        if(!isset($_GET["feed_id"]) || !$_GET["feed_id"]){
            View::throwAppError("Invalid Request");
        }

        $organization = $this->__getOrganization();
        $feed = Feed::findByOrgAndId($organization->getId(), $_GET['feed_id']);
        if(!$feed){
            View::throwAppError("Invalid Feed");
        }

        $feed_file = FeedFile::findByFeedIdAndId($feed->getId(), $this->route_params["id"]);
        if(!$feed_file){
            View::throwAppError("Invalid File");
        }

        $file_path  = $feed->getMediaFolder() . $feed_file->getFilename();
        if(!file_exists($file_path)){
            View::throwAppError("File not found");
        }

        header('Content-Type: ' . Utils::getFileMimeType($file_path));
        header('Content-Disposition: filename="' . $feed_file->getFilename() . '"');
        readfile($file_path);
        exit();
    }
}