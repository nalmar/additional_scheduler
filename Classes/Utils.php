<?php

namespace Sng\Additionalscheduler;

/*
 * This file is part of the "additional_scheduler" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;

/**
 * tx_additionalscheduler_utils
 * Class with some utils functions
 *
 * @author     Yohann CERDAN <cerdanyohann@yahoo.fr>
 * @package    TYPO3
 * @subpackage additional_scheduler
 */
class Utils
{
    /**
     * Define all the reports
     *
     * @return array
     */
    public static function getTasksList()
    {
        return array('savewebsite', 'exec', 'execquery', 'clearcache', 'cleart3temp','query2csv');
    }

    /**
     * Send a email using t3lib_htmlmail or the new swift mailer
     * It depends on the TYPO3 version
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $type
     * @param string $charset
     * @param array  $files
     */
    public static function sendEmail($to, $subject, $message, $type = 'plain', $charset = 'utf-8', $files = array())
    {
        $mail = GeneralUtility::makeInstance(MailMessage::class);
        $mail->setTo(explode(',', $to));
        $mail->setSubject($subject);
        $mail->setCharset($charset);
        $from = MailUtility::getSystemFrom();
        $mail->setFrom($from);
        $mail->setReplyTo($from);
        // add Files
        if (!empty($files)) {
            foreach ($files as $fileName => $path) {
                $attachment = \Swift_Attachment::fromPath($path);
                if (is_string($fileName)) {
                    $attachment->setFilename($fileName);
                }
                $mail->attach($attachment);
            }
        }
        // add Plain
        if ($type == 'plain') {
            $mail->addPart($message, 'text/plain');
        }
        // add HTML
        if ($type == 'html') {
            $mail->setBody($message, 'text/html');
        }
        // send
        $mail->send();
    }

}

