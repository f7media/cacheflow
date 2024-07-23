<?php

namespace F7media\Cacheflow\Domain\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use F7media\Cacheflow\Utility\CacheFlowUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;

class PageRepository
{
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
                $queryBuilder->expr()->notIn('p.doktype', $queryBuilder->createNamedParameter(CacheFlowUtility::EXCLUDED_DOKTYPES, ArrayParameterType::INTEGER))
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
                $queryBuilder->expr()->notIn('p.doktype', $queryBuilder->createNamedParameter(CacheFlowUtility::EXCLUDED_DOKTYPES, ArrayParameterType::INTEGER))
            )->groupBy('p.uid')
            ->executeQuery()->fetchFirstColumn();
    }

    public function updatePageLastCacheStatus(int $pid): void
    {
        $queryBuilder = (new ConnectionPool())->getConnectionForTable('pages')->createQueryBuilder();
        $queryBuilder->update('pages')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)))
            ->set('last_flowed', time())->executeStatement();
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
                $queryBuilder->expr()->notIn('p.doktype', $queryBuilder->createNamedParameter(CacheFlowUtility::EXCLUDED_DOKTYPES, ArrayParameterType::INTEGER))
            );
        return $statement->executeQuery()->rowCount();
    }
}
