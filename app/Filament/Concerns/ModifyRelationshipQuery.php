<?php

namespace App\Filament\Concerns;

use App\Models\Builders\ExpenseBudgetBuilder;
use Closure;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @mixin Summarizer
 */
trait ModifyRelationshipQuery
{
    /**
     * @return Builder|\Illuminate\Database\Eloquent\Builder<Model>
     *
     * @throws \Exception
     */
    public function resolveQuery(Closure $closure): Builder|\Illuminate\Database\Eloquent\Builder
    {
        $column = $this->getColumn();

        $query = $this->getQuery()->clone();

        if ($column->hasRelationship($query->getModel())) {
            $relationship = $column->getRelationship($query->getModel());

            $inverseRelationship = $column->getInverseRelationshipName($query->getModel());

            $baseQuery = $query->toBase();

            /** @var ExpenseBudgetBuilder $query */
            $query = $relationship->getQuery()->getModel()->newQuery()
                ->whereHas(
                    $inverseRelationship,
                    function (Builder|\Illuminate\Database\Eloquent\Builder $relatedQuery) use ($baseQuery, $query): Builder|\Illuminate\Database\Eloquent\Builder {
                        $relatedQuery->mergeConstraintsFrom($query);

                        if ($baseQuery->limit !== null) {
                            /** @var Collection<int, Model> $records */
                            $records = $this->getTable()->getRecords();

                            $relatedQuery->whereKey($records->modelKeys());
                        }

                        return $relatedQuery;
                    },
                );

            $query = $closure($query);
        }

        $asName = (string) str($query->getModel()->getTable())->afterLast('.');

        return DB::connection($query->getModel()->getConnectionName())
            ->table($query->toBase(), $asName);
    }
}
