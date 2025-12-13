<?php


namespace Winter\Mall\Classes\Index;

use Illuminate\Support\Collection;
use Winter\Mall\Classes\CategoryFilter\SortOrder\SortOrder;

interface Index
{
    public function insert(string $index, Entry $data);

    public function update(string $index, $id, Entry $data);

    public function delete(string $index, $id);

    public function create(string $index);

    public function drop(string $index);

    public function fetch(
        string $index,
        Collection $filters,
        SortOrder $order,
        int $perPage,
        int $forPage
    ): IndexResult;
}
