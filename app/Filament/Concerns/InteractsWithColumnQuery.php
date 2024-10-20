<?php

namespace App\Filament\Concerns;

use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @mixin Summarizer
 */
trait InteractsWithColumnQuery
{
    /**
     * @return array<int, mixed>
     *
     * @throws \Exception
     */
    public function resolveQuery(): array
    {
        $column = $this->getColumn();
        $query = $this->getQuery()->clone();

        $relationship = $column->getRelationship($query->getModel());

        $inverseRelationship = $column->getInverseRelationshipName($query->getModel());

        $baseQuery = $query->toBase();

        $query = $relationship->getQuery()->getModel()->newQuery()
            ->whereHas(
                $inverseRelationship,
                function (Builder $relatedQuery) use ($baseQuery, $query): Builder {
                    $relatedQuery->mergeConstraintsFrom($query);

                    if ($baseQuery->limit !== null) {
                        /** @var Collection<int, Model> $records */
                        $records = $this->getTable()->getRecords();

                        $relatedQuery->whereKey($records->modelKeys());
                    }

                    return $relatedQuery;
                },
            );

        [, $period] = $baseQuery->wheres;

        //Merge filter query where from base query into the budget query
        $query = $query->mergeWheres($period['query']->wheres, $period['query']->bindings);

        $asName = (string) str($query->getModel()->getTable())->afterLast('.');

        return [
            DB::connection($query->getModel()->getConnectionName())->table($query->toBase(), $asName),
            [$period],
        ];
    }
}
