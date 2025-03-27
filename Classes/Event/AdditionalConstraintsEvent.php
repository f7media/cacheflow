<?php

namespace F7media\Cacheflow\Event;

use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

final class AdditionalConstraintsEvent
{

    public function __construct(
        private QueryBuilder $queryBuilder,
        private CompositeExpression $additionalConstraint,
    ) {}

    public function getAdditionalConstraint(): CompositeExpression
    {
        return $this->additionalConstraint;
    }

    public function setAdditionalConstraint(CompositeExpression $additionalConstraint): void
    {
        $this->additionalConstraint = $additionalConstraint;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

}
