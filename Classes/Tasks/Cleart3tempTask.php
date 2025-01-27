<?php

declare(strict_types=1);

namespace Sng\Additionalscheduler\Tasks;

/*
 * This file is part of the "additional_scheduler" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Sng\Additionalscheduler\Utils;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class Cleart3tempTask extends AbstractTask
{
    public $dirfilter;

    /**
     * @var array
     */
    protected $stats = [];

    /**
     * @var int
     */
    public $nbdays;

    /**
     * This is the main method that is called when a task is executed
     * It MUST be implemented by all classes inheriting from this one
     * Note that there is no error handling, errors and failures are expected
     * to be handled and logged by the client implementations.
     * Should return true on successful execution, false on error.
     *
     * @return bool
     */
    public function execute(): bool
    {
        $this->stats['nbfiles'] = 0;
        $this->stats['nbfilessize'] = 0;
        $this->stats['nbfilesdeleted'] = 0;
        $this->stats['nbfilesdeletedsize'] = 0;
        $this->stats['nbdirectories'] = 0;

        $this->emptyDirectory(Utils::getPathSite() . 'typo3temp', (int)$this->nbdays);

        if (defined('TYPO3_cliMode') && TYPO3_cliMode) {
            echo 'Nb files: ' . $this->stats['nbfiles'] . ' (' . $this->stats['nbfilessize'] . ' ko)' . LF;
            echo 'Nb files deleted: ' . $this->stats['nbfilesdeleted'] . ' (' . $this->stats['nbfilesdeletedsize'] . ' ko)' . LF;
            echo 'Nb directories: ' . $this->stats['nbdirectories'] . LF;
        }

        return true;
    }

    /**
     * Delete all files of a directory older than x days
     *
     * @param string $dirname
     * @param int    $nbdays
     * @return bool
     */
    public function emptyDirectory(string $dirname, int $nbdays): bool
    {
        if ((is_dir($dirname)) && (($dir_handle = opendir($dirname)) !== false)) {
            while ($file = readdir($dir_handle)) {
                if ($file !== '.' && $file !== '..') {
                    $absoluteFileName = $dirname . '/' . $file;
                    if (!is_dir($absoluteFileName)) {
                        $size = round(filesize($absoluteFileName) / 1024);
                        $this->stats['nbfiles']++;
                        $this->stats['nbfilessize'] += $size;
                        if ((time() - filemtime($absoluteFileName)) >= ($nbdays * 86400)) {
                            if (is_writable($absoluteFileName)) {
                                if (empty($this->dirfilter)) {
                                    $this->stats['nbfilesdeleted']++;
                                    $this->stats['nbfilesdeletedsize'] += $size;
                                    @unlink($absoluteFileName);
                                } else {
                                    if (preg_match('#' . $this->dirfilter . '#', $absoluteFileName) === 0) {
                                        $this->stats['nbfilesdeleted']++;
                                        $this->stats['nbfilesdeletedsize'] += $size;
                                        @unlink($absoluteFileName);
                                    }

                                    // dont delete files with this mask
                                }
                            }

                            // cannot delete files
                        }
                    } else {
                        $this->stats['nbdirectories']++;
                        $this->emptyDirectory($absoluteFileName, (int)$nbdays);
                    }
                }
            }

            closedir($dir_handle);
            return true;
        }

        return false;
    }

    /**
     * This method is designed to return some additional information about the task,
     * that may help to set it apart from other tasks from the same class
     * This additional information is used - for example - in the Scheduler's BE module
     * This method should be implemented in most task classes
     *
     * @return string
     */
    public function getAdditionalInformation(): string
    {
        return $this->nbdays . ' days';
    }
}
