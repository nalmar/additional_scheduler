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

class ExecqueryFields extends BaseAdditionalFieldProvider
{
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $parentObject)
    {
        $result = true;
        if (empty($submittedData[$this->getFieldName('query')])) {
            $this->addMessage('savedirerror', FlashMessage::ERROR);
            $result = false;
        }

        return $result;
    }

    /**
     * Task namespace, mainly to compute formfield names
     *
     * @return string
     * @see BaseAdditionalFieldProvider::getFieldName()
     */
    protected function getTaskNs(): string
    {
        return 'execquery';
    }

    /**
     * Fields structure
     * keys are field's names, values are formfield data
     * eg
     * [
     *   'foo' => 'input',
     *   'bar' => ['code' => 'input', 'extraAttributes' => 'class="baz"', 'default' => 'biz'],
     * ]
     * By implementing this method, fields will be auto-added to the form
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'query' => 'textarea',
            'subject' => 'input',
            'email' => 'input',
            'emailtemplate' => 'input',
        ];
    }
}
