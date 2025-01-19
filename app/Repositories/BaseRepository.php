<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * BaseRepository constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->makeModel();
    }

    /**
     * Define the model class used by the repository.
     *
     * @return string
     */
    abstract public function model(): string;

    /**
     * Define the searchable fields for the repository.
     *
     * @return array
     */
    abstract public function getFieldsSearchable(): array;

    /**
     * Instantiate and assign the model.
     *
     * @throws \Exception
     */
    public function makeModel(): void
    {
        $model = App::make($this->model());

        if (!$model instanceof Model) {
            $class = $this->model();
            throw new \Exception("Class {$class} must be an instance of Illuminate\\Database\\Eloquent\\Model.");
        }

        $this->model = $model;
    }

    /**
     * Retrieve all records with optional filtering, skipping, and limiting.
     *
     * @param array $filters
     * @param int|null $skip
     * @param int|null $limit
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $filters = [], int $skip = null, int $limit = null, array $columns = ['*'])
    {
        $query = $this->allQuery($filters, $skip, $limit);
        return $query->get($columns);
    }

    /**
     * Build a query for retrieving filtered records.
     *
     * @param array $filters
     * @param int|null $skip
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function allQuery(array $filters = [], int $skip = null, int $limit = null)
    {
        $query = $this->model->newQuery();

        // Apply filters on searchable fields
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                if (in_array($field, $this->getFieldsSearchable(), true)) {
                    $query->where($field, $value);
                }
            }
        }

        // Apply skip and limit
        if (!is_null($skip)) {
            $query->skip($skip);
        }
        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Paginate the records.
     *
     * @param int $perPage
     * @param array $filters
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 10, array $filters = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->allQuery($filters);
        return $query->paginate($perPage, $columns);
    }

    /**
     * Find a record by its ID.
     *
     * @param int $id
     * @param array $columns
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find a record by its ID or throw an exception.
     *
     * @param int $id
     * @param array $columns
     * @return Model
     */
    public function findOrFail(int $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Create a new record in the model.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by its ID.
     *
     * @param array $data
     * @param int $id
     * @return Model
     */
    public function update(array $data, int $id): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record;
    }

    /**
     * Delete a record by its ID.
     *
     * @param int $id
     * @return bool|null
     */
    public function delete(int $id): ?bool
    {
        $record = $this->findOrFail($id);
        return $record->delete();
    }
}
