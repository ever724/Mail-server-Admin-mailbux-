<?php

namespace App\Services\QueryBuilder\Concerns;

use App\Services\QueryBuilder\Exceptions\AllowedFieldsMustBeCalledBeforeAllowedIncludes;
use App\Services\QueryBuilder\Exceptions\InvalidFieldQuery;
use App\Services\QueryBuilder\Exceptions\UnknownIncludedFieldsQuery;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait AddsFieldsToQuery
{
    /** @var \Illuminate\Support\Collection */
    protected $allowedFields;

    public function allowedFields($fields): self
    {
        if ($this->allowedIncludes instanceof Collection) {
            throw new AllowedFieldsMustBeCalledBeforeAllowedIncludes();
        }

        $fields = is_array($fields) ? $fields : func_get_args();

        $this->allowedFields = collect($fields)
            ->map(function (string $fieldName) {
                return $this->prependField($fieldName);
            });

        $this->ensureAllFieldsExist();

        $this->addRequestedModelFieldsToQuery();

        return $this;
    }

    protected function addRequestedModelFieldsToQuery()
    {
        $modelTableName = $this->getModel()->getTable();

        $modelFields = $this->request->fields()->get($modelTableName);

        if (empty($modelFields)) {
            return;
        }

        $prependedFields = $this->prependFieldsWithTableName($modelFields, $modelTableName);

        $this->select($prependedFields);
    }

    public function getRequestedFieldsForRelatedTable(string $relation): array
    {
        $fields = $this->request->fields()->mapWithKeys(function ($fields, $relation) {
            return [Str::camel($relation) => $fields];
        })->get($relation);

        if (!$fields) {
            return [];
        }

        if (!$this->allowedFields instanceof Collection) {
            // We have requested fields but no allowed fields (yet?)

            throw new UnknownIncludedFieldsQuery($fields);
        }

        return $fields;
    }

    protected function ensureAllFieldsExist()
    {
        $requestedFields = $this->request->fields()
            ->map(function ($fields, $model) {
                $tableName = Str::camel(preg_replace('/-/', '_', $model));

                $fields = array_map([Str::class, 'camel'], $fields);

                return $this->prependFieldsWithTableName($fields, $tableName);
            })
            ->flatten()
            ->unique();

        $unknownFields = $requestedFields->diff($this->allowedFields);

        if ($unknownFields->isNotEmpty()) {
            throw InvalidFieldQuery::fieldsNotAllowed($unknownFields, $this->allowedFields);
        }
    }

    protected function prependFieldsWithTableName(array $fields, string $tableName): array
    {
        return array_map(function ($field) use ($tableName) {
            return $this->prependField($field, $tableName);
        }, $fields);
    }

    protected function prependField(string $field, ?string $table = null): string
    {
        if (!$table) {
            $table = $this->getModel()->getTable();
        }

        if (Str::contains($field, '.')) {
            // Already prepended

            return $field;
        }

        return "{$table}.{$field}";
    }
}
