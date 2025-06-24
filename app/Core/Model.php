<?php

namespace App\Core;

use Medoo\Medoo;

abstract class Model
{
    /**
     * Database table name.
     * @var string
     */
    protected static string $table;

    /**
     * Database connection instance.
     * @var Medoo
     */
    protected Medoo $db;

    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }
    /**
     * @param int $id
     * @return array<string,mixed>|null
    */
    public function find(int $id): ?array
    {
        return $this->db->get(static::$table, '*', ['id' => $id]) ?: null;
    }
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): ?array
    {
        /** @phpstan-ignore-next-line */
        return $this->db->select(static::$table, ['*']);
    }
    /**
     * @return int
    */
    public function create(array $data): int
    {
        $this->db->insert(static::$table, $data);
        return (int) $this->db->id();
    }
    /**
     * @return boolean
    */
    public function update(int $id, array $data): bool
    {
        $this->db->update(static::$table, $data, ['id' => $id]);
        return $this->db->error === null;
    }
    /**
     * @return boolean
    */
    public function updateWhere(array $data, array $where): bool
    {
        $this->db->update(static::$table, $data, $where);
        $error = $this->db->error;
        return $this->db->error === null;
    }
    /**
     * @return boolean
    */

    public function delete(int $id): bool
    {
        $this->db->delete(static::$table, ['id' => $id]);
        return $this->db->error === [null, null, null];
    }

    public function deleteWhere(array $where): bool
    {
        $this->db->delete(static::$table, $where);
        return $this->db->error === [null, null, null];
    }
}
