<?php

namespace App\Repositories;

use App\Models\Campus;

class CampusRepository extends BaseRepository
{
    /**
     * Define the model associated with the repository.
     *
     * @return string
     */
    public function model(): string
    {
        return Campus::class;
    }

    /**
     * Define the searchable fields for the repository.
     *
     * @return array
     */
    public function getFieldsSearchable(): array
    {
        return ['name', 'code'];
    }

    /**
     * Fetch campuses with their associated librarians.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWithLibrarians(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with('librarians')->get();
    }
}
