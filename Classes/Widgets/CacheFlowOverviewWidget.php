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

namespace F7media\Cacheflow\Widgets;

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
        private readonly array $options,
        private readonly StatisticsService $statisticsService
    ) {}

    #[\Override]
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request, ['typo3/cms-dashboard', 'f7media/cacheflow']);

        $view->assignMultiple([
            'options' => $this->options,
            'configuration' => $this->configuration,
            'statistics' => $this->statisticsService->composeWidgetOutput(),
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
