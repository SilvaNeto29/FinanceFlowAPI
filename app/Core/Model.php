<?php

namespace App\Core;

use Medoo\Medoo;

abstract class Model
{
    protected string $table;
    protected Medoo $db;

    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }

    public function find(int $id): ?array
    {
        return $this->db->get(static::$table, '*', ['id' => $id]) ?: null;
    }

    public function all(): array
    {
        return $this->db->select(static::$table, '*');
    }

    public function create(array $data): bool
    {
        $this->db->insert(static::$table, $data);
        return $this->db->id() !== 0;
    }

    public function update(int $id, array $data): bool
    {
        $this->db->update(static::$table, $data, ['id' => $id]);
        return $this->db->error === [null, null, null];
    }

    public function delete(int $id): bool
    {
        $this->db->delete(static::$table, ['id' => $id]);
        return $this->db->error === [null, null, null];
    }
}
