<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;

use App\Auth;
use App\Config;
use App\Models\Constituency;
use App\Models\Enums\FeedStatus;
use App\Models\Feed;
use App\Models\FeedFile;
use App\Models\Organization;
use App\Models\User;
use Core\AppError;
use Core\AuthController;
use Core\Controller;
use Core\Flash;
use Core\Security\Validator;
use Core\Utils;
use Core\View;

class FeedController extends AuthController {

    /**
     * @param $user_id
     * @param $added_by_user_id
     * @throws AppError
     */
    private function __addNew($user_id,$added_by_user_id){
        $required_params = array("headline", "summary");
        if(Validator::checkAllParamsExists($required_params) !== true){
            View::throwAppError("Please fill all the inputs");
        }

        $organization = $this->__getOrganization();

        $constituency = Constituency::findByUserId($user_id);
        if(!$constituency){
            View::throwAppError("Constituency details not found!");
        }

        $feed = Feed::getInstance()
            ->setOrgId($organization->getId())
            ->setConstituencyId($constituency->getId())
            ->setUserId($user_id)
            ->setAddedBy($added_by_user_id)
            ->setHeadline($_POST['headline'])
            ->setSummary($_POST['summary']);

        if($feed->getErrors()){
            Flash::addMessageListAndGoBack($feed->getErrors(), Flash::WARNING);
        }
        else if(!$feed->save()){
            Flash::addAndGoBack("Unable to add the feed", Flash::WARNING);
        }

        $uploaded_files = [];
        if(isset($_FILES) && isset($_FILES["files"])){
            $file_count = count($_FILES['files']["name"]);

            $rearranged_files = Utils::rearrangeFilesArray($_FILES['files']);
            foreach ($rearranged_files as $file){
                $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                if(strlen($file_extension) > 6){
                    // file extension more than 6 characters, skip & eventually will stop addition.
                    continue;
                }
                $file_name = time() . "_" . Utils::getRandomBytes(16) . "." . $file_extension;
                $destination_file = $feed->getMediaFolder() . $file_name ;
                if (move_uploaded_file($file["tmp_name"], $destination_file)) {
                    $feed_file = FeedFile::getInstance()
                        ->setFilename($file_name)
                        ->setFeedId($feed->getId());

                    if($feed_file->save()){
                        $uploaded_files[] = $file_name;
                    }
                }
            }

            if(count($uploaded_files) !== $file_count && count($_FILES['files']["tmp_name"]) > 1){
                foreach ($uploaded_files as $file_name){
                    /** @var Organization $organization */
                    $destination_file = $feed->getMediaFolder() . $file_name ;
                    unlink($destination_file);
                }
                $feed->delete();

                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
                    View::throwAppError("Failed to upload the media file");
                }
                else{
                    Flash::addAndGoBack("Failed to upload the media file", Flash::WARNING);
                }
            }
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
            View::displayJsonSuccess("Added");
        }
        else{
            Flash::addAndGoBack("Added", Flash::SUCCESS);
        }
    }

    /**
     * @throws \Exception
     */
    protected function listAction(){
        Auth::throwErrorIfPermissionDenied("view_Feed");

        $organization = $this->__getOrganization();

        $feed_list = Feed::getListByOrganization($organization->getId());

        $this->masterView('Feed/list.php',
            [
                'feed_list' => $feed_list
            ]
        );
    }

    /**
     * @throws \Core\AppError
     * @throws \Exception
     */
    protected function viewAction(){
        Auth::throwErrorIfPermissionDenied("view_Feed");

        if(!isset($this->route_params["id"])){
            View::throwAppError("Invalid Request");
        }

        $organization = $this->__getOrganization();
        $feed = Feed::findByOrgAndId($organization->getId(), $this->route_params["id"]);
        if(!$feed){
            View::throwAppError("Invalid Request");
        }

        $media_files = FeedFile::getObjectListByFeedId($feed->getId());

        $this->masterView('Feed/view.php',[
            'feed' => $feed,
            'media_files' => $media_files
        ]);
    }


    /**
     * @throws AppError
     */
    protected function ignoreAction() {
        Auth::throwErrorIfPermissionDenied("delete_Feed");

        $required_params = array("id");
        if(Validator::checkAllParamsExists($required_params) !== true){
            View::throwAppError("Invalid Request");
        }

        $organization = $this->__getOrganization();
        $feed = Feed::findByOrgAndId($organization->getId(), $_POST['id']);
        if(!$feed){
            View::throwAppError("Invalid Request");
        }

        if($feed->updateStatus(FeedStatus::IGNORED)){
            View::displayJsonSuccess("Ignored");
        }
        else{
            View::throwAppError("Unable to ignore");
        }
    }

    /**
     * @throws \Exception
     */
    protected function addAction(){
        if(!Auth::user()->can('add_Feed')
            && !Auth::user()->can('addAsFeeder_Feed')){
            View::throwAppError("You are not authorized", 403);
        }

        if($_SERVER['REQUEST_METHOD'] === "GET"){
            $feeder_list = null;
            if(Auth::user()->can('addAsFeeder_Feed')){
                $organization = $this->__getOrganization();
                $feeder_list = $organization->getUserListByRole('feeder');
                if(!$feeder_list){
                    View::throwAppError("Feeder user list empty - Can't add input feeds!");
                }
            }


            $this->masterView('Feed/add.php',[
                'feeder_list' => $feeder_list
            ]);
        }
        else if($_SERVER['REQUEST_METHOD'] === "POST"){

            if(Auth::user()->can('addAsFeeder_Feed')) {
                $organization = $this->__getOrganization();
                $feeder = User::findByOrgAndId($organization->getId(), $_POST['feeder']);
                if (!$feeder || !$feeder->hasRole('feeder')) {
                    View::throwAppError("Invalid User Selected");
                }
                $this->__addNew($feeder->getId(),Auth::user()->getId());
            }
            else{
                $this->__addNew(Auth::user()->getId(),Auth::user()->getId());
            }

        }
    }

}