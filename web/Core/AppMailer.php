<?php

namespace Core;

/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

use App\Config;
use Exception;

/**
 * Custom PHPMailer class for sending mails
 * Class AppMailer
 */
class AppMailer extends \PHPMailer\PHPMailer\PHPMailer {

    public function __construct($exceptions = null) {
        parent::__construct($exceptions);
        $this->isSMTP();
        $this->SMTPAuth = true;
        $this->Host = Config::MAIL_HOST;
        $this->Port = 587;
        $this->Username = Config::MAIL_USER;
        $this->Password = Config::MAIL_PASS;
        $this->From = Config::MAIL_USER;
        $this->FromName = Config::MAIL_FROM_NAME;
        $this->SMTPSecure = 'starttls';
        $this->isHTML ( true );
        $this->XMailer = ' ';
        $this->Timeout = 60;
    }

    /**
     * Custom function to send mail
     * @param $recipients
     * @param $cc_recipients
     * @param $subject
     * @param $msg
     * @param null $alternate_msg
     * @return bool
     */
    public function sendMail($recipients, $cc_recipients, $subject, $msg, $alternate_msg = NULL){
        if (is_array ( $recipients )){
            foreach ( $recipients as $recipient ){
                $this->addAddress ( $recipient );
            }
        }
        else{
            $this->addAddress ( $recipients );
        }

        if($cc_recipients){
            if (is_array ( $cc_recipients )){
                foreach ( $cc_recipients as $recipient ){
                    $this->addCC( $recipient );
                }
            }
            else{
                $this->addAddress ( $cc_recipients );
            }
        }

        $this->Subject = $subject;
        $this->Body = $msg;
        if ($alternate_msg) {
            $this->AltBody = $alternate_msg;
        }

        return $this->send ();
    }

}