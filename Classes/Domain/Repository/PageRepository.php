<?php

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

namespace F7media\Cacheflow\Domain\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use F7media\Cacheflow\Event\AdditionalConstraintsEvent;
use F7media\Cacheflow\Utility\CacheFlowUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;

#[Autoconfigure(public: true)]
class PageRepository
{

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * @param mixed[] $excludedUids
     * @return mixed[]
     * @throws Exception
     */
    public function fillUpBatch(int $amount, array $excludedUids): array
    {
        $queryBuilder = (new ConnectionPool())->getConnectionForTable('pages')->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeByType(StartTimeRestriction::class)->removeByType(EndTimeRestriction::class);
        $statement = $queryBuilder
            ->select('p.uid')->from('pages', 'p')
            ->orderBy('p.last_flowed', 'ASC')
            ->where(
                $queryBuilder->expr()->notIn('p.doktype', $queryBuilder->createNamedParameter(CacheFlowUtility::EXCLUDED_DOKTYPES, ArrayParameterType::INTEGER)),
                $this->getAdditionalConstraint($queryBuilder)
            )
            ->setMaxResults($amount);
        if ($excludedUids !== []) {
            $statement->andWhere(
                $queryBuilder->expr()->notIn('p.uid', $queryBuilder->createNamedParameter($excludedUids, ArrayParameterType::INTEGER))
            );
        }

        return $statement->executeQuery()->fetchFirstColumn();
    }

    /**
     * @return mixed[]
     * @throws Exception
     */
    public function findPagesWhoseVisibilityHasJustChanged(int $lastRun): array
    {
        $queryBuilder = (new ConnectionPool())->getConnectionForTable('pages')->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $now = date('U');
        return $queryBuilder
            ->select('p.uid')->from('pages', 'p')
            ->leftJoin('p', 'tt_content', 't', $queryBuilder->expr()->eq('p.uid', $queryBuilder->quoteIdentifier('t.pid')))
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->gte('p.starttime', $queryBuilder->createNamedParameter($lastRun, Connection::PARAM_INT)),
                        $queryBuilder->expr()->lte('p.starttime', $queryBuilder->createNamedParameter($now, Connection::PARAM_INT)),
                    ),
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->gte('p.endtime', $queryBuilder->createNamedParameter($lastRun, Connection::PARAM_INT)),
                        $queryBuilder->expr()->lte('p.endtime', $queryBuilder->createNamedParameter($now, Connection::PARAM_INT)),
                    ),
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->gte('t.starttime', $queryBuilder->createNamedParameter($lastRun, Connection::PARAM_INT)),
                        $queryBuilder->expr()->lte('t.starttime', $queryBuilder->createNamedParameter($now, Connection::PARAM_INT)),
                    ),
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->gte('t.endtime', $queryBuilder->createNamedParameter($lastRun, Connection::PARAM_INT)),
                        $queryBuilder->expr()->lte('t.endtime', $queryBuilder->createNamedParameter($now, Connection::PARAM_INT)),
                    ),
                ),
                $queryBuilder->expr()->notIn('p.doktype', $queryBuilder->createNamedParameter(CacheFlowUtility::EXCLUDED_DOKTYPES, ArrayParameterType::INTEGER)),
                $this->getAdditionalConstraint($queryBuilder)
            )->groupBy('p.uid')
            ->executeQuery()->fetchFirstColumn();
    }

    public function updatePageLastCacheStatus(int $pid, string $status = ''): void
    {
        $queryBuilder = (new ConnectionPool())->getConnectionForTable('pages')->createQueryBuilder();
        $queryBuilder->update('pages')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)))
            ->set('last_flowed', time())
            ->set('last_flow_status', $status)
            ->executeStatement();
    }

    /**
     * @throws Exception
     */
    public function getOldestCachedPageInSystem(): int
    {
        $queryBuilder = (new ConnectionPool())->getConnectionForTable('pages')->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeByType(StartTimeRestriction::class)->removeByType(EndTimeRestriction::class);
        $statement = $queryBuilder
            ->select('p.last_flowed')->from('pages', 'p')
            ->orderBy('p.last_flowed', 'ASC')
            ->where(
                $queryBuilder->expr()->notIn('p.doktype', $queryBuilder->createNamedParameter(CacheFlowUtility::EXCLUDED_DOKTYPES, ArrayParameterType::INTEGER)),
                $this->getAdditionalConstraint($queryBuilder)
            );
        return $statement->executeQuery()->fetchOne();
    }

    /**
     * @throws Exception
     */
    public function getAllRelevantPages(): int
    {
        $queryBuilder = (new ConnectionPool())->getConnectionForTable('pages')->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeByType(StartTimeRestriction::class)->removeByType(EndTimeRestriction::class);
        $statement = $queryBuilder
            ->select('p.uid')->from('pages', 'p')
            ->orderBy('p.last_flowed', 'ASC')
            ->where(
                $queryBuilder->expr()->notIn('p.doktype', $queryBuilder->createNamedParameter(CacheFlowUtility::EXCLUDED_DOKTYPES, ArrayParameterType::INTEGER)),
                $this->getAdditionalConstraint($queryBuilder)
            );
        return $statement->executeQuery()->rowCount();
    }

    /**
     * @throws Exception
     */
    public function getAllPageStatusStatistics(): array
    {
        $queryBuilder = (new ConnectionPool())->getConnectionForTable('pages')->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeByType(StartTimeRestriction::class)->removeByType(EndTimeRestriction::class);
        $statement = $queryBuilder
            // ->count('p.last_flow_status') // not used because here we can't use "as" for a nicer fieldname
            ->selectLiteral('count(p.last_flow_status) AS count_status')
            ->addSelect('p.last_flow_status')
            ->from('pages', 'p')
            ->where(
                $queryBuilder->expr()->notIn('p.doktype', $queryBuilder->createNamedParameter(CacheFlowUtility::EXCLUDED_DOKTYPES, ArrayParameterType::INTEGER)),
                $this->getAdditionalConstraint($queryBuilder)
            )
            ->groupBy('p.last_flow_status');
        return $statement->executeQuery()->fetchAllAssociative();
    }

    protected function getAdditionalConstraint($queryBuilder): CompositeExpression {

        $additionalConstraint =  $queryBuilder->expr()->or(
            $queryBuilder->expr()->neq('p.uid', $queryBuilder->createNamedParameter(-1, Connection::PARAM_INT))
        );

        /** @var DoingThisAndThatEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new AdditionalConstraintsEvent($queryBuilder, $additionalConstraint),
        );

        return $event->getAdditionalConstraint();



    }
}
