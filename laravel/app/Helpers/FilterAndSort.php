<?php

namespace App\Helpers;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait FilterAndSort
{
  public static array $filterOperators = [
    'like' => '~',
    'eq' => '=',
    'lte' => '&lt;=',
    'gte' => '&gt;=',
    'in' => 'in',
    'neq' => '!=',
    'null' => 'Null',
    'notNull' => '!Null',
  ];

  public function applySort(Builder $query, array $allowedSort = [], string|array $defaultSortColumn = 'id', string $defaultSortDirection = 'desc'): Builder
  {
    $sortColumn = request()->query('sort', $defaultSortColumn);
    $sortColumn = (!$allowedSort || in_array($sortColumn, $allowedSort)) ? $sortColumn : $defaultSortColumn;
    $sortDirection = request()->query('d', $defaultSortDirection) === 'asc' ? 'asc' : 'desc';
    foreach (is_array($sortColumn) ? $sortColumn : [$sortColumn] as $column) {
      if (str_contains($column, '.')) {
        [$relation, $relatedColumn] = explode('.', $column, 2);

        if (method_exists($query->getModel(), $relation)) {
          $relationQuery = $query->getModel()->{$relation}();

          if (method_exists($relationQuery, 'getQualifiedOwnerKeyName')) {
            $parentKey = $relationQuery->getQualifiedOwnerKeyName();
            $foreignKey = $relationQuery->getQualifiedForeignKeyName();
          } else {
            $parentKey = $relationQuery->getQualifiedParentKeyName();
            $foreignKey = $relationQuery->getQualifiedForeignKeyName();
          }

          $query->orderBy(
            $relationQuery->getRelated()->select($relatedColumn)->whereColumn($parentKey, $foreignKey),
            $sortDirection
          );

          continue;
        }
      }

      $query->orderBy($column, $sortDirection);
    }

    return $query;
  }

  public function applyFilter(Builder $query, array $allowedFilter = []): Builder
  {
    $allowedOperators = array_keys(self::$filterOperators);
    $allowedOperators[] = 'interval';

    foreach (request()->query('filter', []) as $field => $value) {
      $value = trim($value);

      if ($value === '-1::all') {
        continue;
      }

      if ($value !== '' && (!$allowedFilter || in_array($field, $allowedFilter))) {
        // Skip "q" search
        if (!$allowedFilter && $field === 'q') {
          continue;
        }

        $operator = request()->query('op', [])[$field] ?? 'like';
        $operator = in_array($operator, $allowedOperators) ? $operator : 'like';

        if ($operator === 'eq') {
          $query->where($field, '=', $value);
        } elseif ($operator === 'lte') {
          $query->where($field, '<=', $value);
        } elseif ($operator === 'gte') {
          $query->where($field, '>=', $value);
        } elseif ($operator === 'in') {
          $query->whereIn($field, explode(',', $value));
        } elseif ($operator === 'neq') {
          $query->where($field, '!=', $value);
        } elseif ($operator === 'null') {
          $query->whereNull($field);
        } elseif ($operator === 'notNull') {
          $query->whereNotNull($field);
        } elseif ($operator === 'interval') {
          $query = $this->applyRangeFilter($query, $value, $field);
        } else {
          $query->where($field, 'like', "%$value%");
        }
      }
    }

    return $query;
  }

  public function applyQFilter(Builder $query, array $fields, $requestName = 'filter.q'): Builder
  {
    $q = request()->input($requestName, '');

    if (!$q) {
      return $query;
    }

    $query->where(function ($query) use ($fields, $q) {
      foreach ($fields as $field) {
        $query->orWhere($field, 'like', "%$q%");
      }
    });

    return $query;
  }

  // public function applyRangeFilter(Builder|\Illuminate\Database\Query\Builder|Relation $query, $date, $fieldName): Builder|\Illuminate\Database\Query\Builder|Relation
  public function applyRangeFilter($query, $date, $fieldName)
  {
    $separator = ' to ';

    if (!$date) {
      return $query;
    }

    list($from, $to) = str_contains($date, $separator) ? explode($separator, $date) : [$date, $date];
    $fromDate = DateTime::createFromFormat('Y-m-d', $from) ?: new DateTime();
    $toDate = DateTime::createFromFormat('Y-m-d', $to) ?: new DateTime();

    $query->whereBetween($fieldName, [
      $fromDate->format('Y-m-d 00:00:00'),
      $toDate->format('Y-m-d 23:59:59')
    ]);

    return $query;
  }
}
