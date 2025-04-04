<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace F7media\Cacheflow\Service;

use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MessagingService
{
    /**
     * @var string
     */
    protected const STATISTICS_PREFIX = '=== Statistics ===';

    /**
     * @var string
     */
    protected const MESSAGE_TITLE = 'Page cache has successfully been flowed.';

    /**
     * @param mixed[] $input
     */
    public function cliMessageStatistics(array $input, SymfonyStyle $io): void
    {
        $output = [];
        foreach ($input as $key => $value) {
            $output[] = [$key => $value];
        }

        $io->success(self::MESSAGE_TITLE);
        $io->definitionList(self::STATISTICS_PREFIX, ...$output);
    }

    /**
     * @param mixed[] $input
     */
    public function flashMessageStatistics(array $input): void
    {
        $output = '';
        foreach ($input as $key => $value) {
            $output .= $key . ': ' . $value . PHP_EOL;
        }

        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            self::STATISTICS_PREFIX . PHP_EOL . $output,
            self::MESSAGE_TITLE,
            ContextualFeedbackSeverity::OK,
            true
        );
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);
    }
}
