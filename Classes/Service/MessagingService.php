<?php

declare(strict_types=1);

namespace F7\Cacheflow\Service;

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
     * @param array<mixed> $input
     * @param SymfonyStyle $io
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
     * @param array<mixed> $input
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
