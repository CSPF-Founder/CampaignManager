<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;

/**
 * Flash(don't get confused with flash player) notification messages(like in many other php frameworks)
 * For one time display using the session for storage between requests
 * Class Flash
 * @package App
 */

class Flash {

    const SESSION_VARIABLE = 'flash_notifications';

    //Message Types
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const DANGER = 'danger';

    public $message;
    public $type;
    public $closable;

    public function __construct($message, $type, $closable=true) {
        $this->message = $message;
        $this->type = $type;
        $this->closable = $closable;
    }

    /**
     * Add a message
     * @param $message
     * @param string $type
     * @param bool $closable
     */
    public static function addMessage($message, $type, $closable=true){
        if(!isset($_SESSION[static::SESSION_VARIABLE])){
            $_SESSION[static::SESSION_VARIABLE] = [];
        }

        //Append the message to the array
        $_SESSION[static::SESSION_VARIABLE][] = new Flash($message, $type, $closable);
    }

    /**
     * Get all the messages
     * @return array
     */
    public static function getMessages(){
        if(isset($_SESSION[static::SESSION_VARIABLE])){
            $messages = $_SESSION[static::SESSION_VARIABLE];
            unset($_SESSION[static::SESSION_VARIABLE]);
            if($messages){
                return $messages;
            }
        }
        return [];
    }

    /**
     * Add message and redirect back to the previous  page
     * @param string $message
     * @param string $type
     * @param boolean $closable
     */
    public static function addAndGoBack($message, $type = self::SUCCESS, $closable=true){
        Flash::addMessage($message, $type, $closable);
        Controller::redirect(Utils::getInternalReferer());
    }

    /**
     * Add message and redirect back to the previous  page
     * @param array $messages
     * @param string $type
     * @param boolean $closable
     */
    public static function addMessageListAndGoBack($messages, $type = self::SUCCESS, $closable=true){
        foreach ($messages as $message){
            Flash::addMessage($message, $type, $closable);
        }
        Controller::redirect(Utils::getInternalReferer());
    }

    /**
     * Add a message
     * @param $messages
     * @param string $type
     * @param bool $closable
     */
    public static function addMessageList($messages, $type, $closable=true){
        foreach ($messages as $message){
            Flash::addMessage($message, $type, $closable);
        }
    }
}