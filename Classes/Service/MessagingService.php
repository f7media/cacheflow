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
    const STATISTICS_PREFIX = '=== Statistics ===';

    /**
     * @param SymfonyStyle $io
     * @param string $title
     * @param array<mixed> $statistics
     */
    public function cliMessageStatistics(SymfonyStyle $io, string $title, array $statistics): void
    {
        $io->success($title);
        $io->definitionList(self::STATISTICS_PREFIX, ...$statistics);
    }

    /**
     * @param string $title
     * @param string $content
     */
    public function flashMessageStatistics(string $title, string $content): void
    {
        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            self::STATISTICS_PREFIX . PHP_EOL . $content,
            $title,
            ContextualFeedbackSeverity::OK,
            true
        );
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);
    }
}
