<?php
/**
 * Created by PhpStorm.
 * User: dilshod
 * Date: 2019-05-12
 * Time: 16:53
 */

namespace Currency\Model;

use Noodlehaus\Config;
use PDO;

abstract class Model
{
    protected $connection;

    /**
     * @var string;
     */
    protected $database;

    /**
     * @var string;
     */
    protected $user;

    /**
     * @var string;
     */
    protected $password;

    /**
     * @var string;
     */
    protected $host;

    /**
     * @var string;
     */
    protected $table_name;

    /**
     * @var string
     */
    protected $primary_key_column = 'id';

    /**
     * @var array
     */
    protected $table_columns = ['*'];

    public function __construct()
    {
        $config = Config::load(APP_DIR . '/config/database.php');

        $this->host = $config->get('host');
        $this->user = $config->get('user');
        $this->password = $config->get('password');
        $this->database = $config->get('database');

        $this->connector();
    }

    public function getConnection()
    {
        if (!$this->connection) {
            $this->connector();
        }

        return $this->connection;
    }

    protected function connector()
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $this->connection = new PDO(
            "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4", $this->user, $this->password, $options
        );
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $query_sql = "SELECT * FROM {$this->getTableName()}";
        $prepare = $this->getConnection()->prepare($query_sql);
        $prepare->execute();

        return $prepare->fetchAll() ?? [];
    }

    public function pagination(int $page = 1, int $limit = 3)
    {
        $offset = ($page - 1) * $limit;
        if ($offset < 0) {
            $offset = 0;
        }

        $prepare_data = [
            ':offset' => $offset,
            ':limit' => $limit
        ];

        $query_sql = "SELECT *
                            FROM {$this->getTableName()} LIMIT :offset, :limit";

        try {
            $prepare = $this->getConnection()->prepare($query_sql);
            $prepare->execute($prepare_data);

            return $prepare->fetchAll();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        $query_sql = "SELECT COUNT(id) FROM {$this->getTableName()}";
        $query = $this->getConnection()->query($query_sql);
        $query->execute();

        $count = $query->fetchColumn();

        return $count;
    }

    /**
     * @param $id
     * @return array
     */
    public function getById(int $id)
    {
        $columns = implode(',', $this->getTableColumns());

        $select_sql = "SELECT {$columns} FROM {$this->getTableName()} WHERE {$this->primary_key_column} = :id";

        $prepare = $this->getConnection()->prepare($select_sql);
        $prepare->execute([':id' => $id]);

        return $prepare->fetch() ?? [];
    }

    public function getByColumn(string $column, $value)
    {
        $columns = implode(',', $this->getTableColumns());

        $select_sql = "SELECT {$columns} FROM {$this->getTableName()} WHERE {$column} = :{$column}";

        $prepare = $this->getConnection()->prepare($select_sql);
        $prepare->execute([":{$column}" => $value]);

        return $prepare->fetch() ?? [];
    }

    public function insert(array $data)
    {
        $columns_names = $this->getColumnsName($data);
        $columns_data = $this->getColumnsReplaceKeys($data);
        $columns_prepare_names = implode(',', array_keys($columns_data));

        $insert_sql = "INSERT INTO {$this->getTableName()} ({$columns_names}) VALUES ({$columns_prepare_names})";
        $prepare = $this->getConnection()->prepare($insert_sql);

        return $prepare->execute($columns_data);
    }

    public function update(array $data, int $task_id)
    {
        $columns_names = implode(',', $this->getUpdateColumnsReplaceMark($data));

        $update_sql = "UPDATE {$this->getTableName()} SET {$columns_names} WHERE {$this->primary_key_column} = {$task_id}";
        $prepare = $this->getConnection()->prepare($update_sql);

        return $prepare->execute(array_values($data));
    }

    public function delete()
    {
        $delete_sql = "DELETE FROM {$this->getTableName()}";
        $this->getConnection()->exec($delete_sql);
    }

    protected function getColumnsName(array $data)
    {
        return implode(',', array_keys($data));
    }

    protected function getUpdateColumnsReplaceMark(array $data)
    {
        $columns_prepare = [];
        foreach ($data as $key => $column) {
            $columns_prepare[$key] = $key . " = " . "?";
        }

        return $columns_prepare;
    }

    protected function getColumnsReplaceKeys(array $data)
    {
        $columns = [];
        foreach ($data as $key => $column) {
            $columns[':' . $key] = $column;
        }

        return $columns;
    }

    protected function getTableColumns()
    {
        return $this->table_columns;
    }

    protected function getTableName()
    {
        return $this->table_name;
    }
}
