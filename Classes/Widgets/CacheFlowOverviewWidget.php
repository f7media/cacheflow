<?php

declare(strict_types=1);

namespace F7media\Cacheflow\Widgets;

use Exception;
use F7media\Cacheflow\Service\StatisticsService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

class CacheFlowOverviewWidget implements WidgetInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;

    /**
     * @param array{mixed} $options
     */
    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly array $options
    ) {
    }

    #[\Override]
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @throws Exception
     */
    #[\Override]
    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request, ['typo3/cms-dashboard', 'f7media/cacheflow']);
        $statisticsService = GeneralUtility::makeInstance(StatisticsService::class);

        $view->assignMultiple([
            'options' => $this->options,
            'configuration' => $this->configuration,
            'statistics' => $statisticsService->composeWidgetOutput(),
        ]);
        return $view->render('Widget/CacheFlowOverviewWidget');
    }

    /**
     * @return mixed[]
     */
    #[\Override]
    public function getOptions(): array
    {
        return $this->options;
    }
}
