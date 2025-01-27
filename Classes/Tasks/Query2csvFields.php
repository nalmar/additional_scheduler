<?php

declare(strict_types=1);

namespace Sng\Additionalscheduler\Tasks;

/*
 * This file is part of the "additional_scheduler" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Sng\Additionalscheduler\BaseAdditionalFieldProvider;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

class Query2csvFields extends BaseAdditionalFieldProvider
{
    /**
     * @return bool
     */
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $parentObject): bool
    {
        $result = true;
        if (empty($submittedData[$this->getFieldName('query')])) {
            $this->addMessage('query.error.required', FlashMessage::ERROR);
            $result = false;
        }

        return $result;
    }

    /**
     * Field structure
     * keys are field's names, values form field data
     * eg
     * [
     *   'foo' => 'input',
     *   'bar' => ['code' => 'input', 'extraAttributes' => 'class="baz"],
     * ]
     * By implementing this method, fields will be auto-added to the form
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'filename' => ['code' => 'input', 'default' => 'data.csv'],
            'noDatetimeFlag' => ['code' => 'checkbox', 'default' => '0'],
            'noHeader' => ['code' => 'checkbox', 'default' => '0'],
            'delimiter' => ['code' => 'input', 'extraAttributes' => 'size="2"', 'default' => ','],
            'enclosure' => ['code' => 'input', 'extraAttributes' => 'size="2"', 'default' => '"'],
            'escape' => ['code' => 'input', 'extraAttributes' => 'size="2"', 'default' => '\\'],
            'query' => 'textarea',
            'email' => 'input',
            'subject' => 'input',
            'body' => 'textarea',
        ];
    }

    /**
     * @return string
     */
    protected function getTaskNs(): string
    {
        return 'query2csv';
    }
}
