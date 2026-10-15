<?php
namespace Core;

use PDO, PDOStatement, PDOException;

require_once '../config/database.php';

abstract class Model
{
    protected PDO $pdo;
    protected PDOStatement $stmt;
    protected $dbname = DB_NAME;

    protected const TABLE = [
        'USERS' => 'users',
        'COURSES' => 'courses',
        'REMEMBER_TOKENS' => 'remember_tokens',
        'ENROLLMENTS' => 'enrollments',
        'SUBMITS' => 'submits'
    ];

    protected array $options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    public function __construct(string $host = DB_HOST, string $user = DB_USER, string $password = DB_PASSWORD)
    {
        $dsn = "mysql:host=$host;dbname={$this->dbname}";
        try {
            $this->pdo = new PDO($dsn, $user, $password, $this->options);
            // echo 'connection established';
        } catch (PDOException $e) {
            throw $e;
        }
    }

    protected function paramsConditions(array $params = [], bool $match = false)
    {
        if ($match === false) {
            $conditions = array_map(fn($value) => "$value[0] $value[1] :$value[0]", $params);  // MAKES NEW ARRAY | Array ( [0] => id < :id [1] => id < :id ) 1

            $conditionsString = implode(' AND ', $conditions);  // result = id < :id AND id < :id1

            $conditionsArray = array_column($params, 2, 0);  // array_column takes in nested arrays as input. RESULT: ['id'(0) => 5(2)]
            // array_column() is designed specifically to extract a SINGLE column from nested arrays (or arrays of objects).

            return [$conditionsString, $conditionsArray];
        } else {
            // [[title,description],['query' => $query] ,BOOLEAN]
            $conditionsString = implode(', ', $params[0]);
            $query = $params[1];
            $query = ['query' => $query];
            $mode = $params[2] ?? 'NATURAL';
            if ($mode === 'NATURAL') {
                $string1 = 'IN';
                $string2 = 'LANGUAGE MODE';
            } elseif ($mode === 'BOOLEAN') {
                $string1 = 'IN';
                $string2 = 'MODE';
            } elseif ($mode === 'QUERY') {
                $string1 = 'WITH';
                $string2 = 'EXPANSION';
            }
            $mode = "$string1 $mode $string2";
            return [$conditionsString, $query, $mode];
        }
    }

    /**
     * @param string $table Table to be selected in DB
     * @param array $params WHERE CLAUSE (e.g [['id', '=', $id]])
     * @param string $fields SELECET * etc
     * @param bool $match Should select mode be MATCH (data,...) AGAINST(:query). Param format changes
     * @param array|null $orderBy  SORTCOLUM SORTORDER (ASC|DESC)
     * @param array|null $pagination (LIMIT) | (OFFSET)
     * @return Model
     */
    public function select(string $table, array $params = [], string $fields = '*', ?bool $match = false, ?array $orderBy = [], ?array $pagination = []): Model
    {
        $paramConditions = $this->paramsConditions($params, $match);
        $sql = "SELECT $fields FROM $table";

        if ($params && $match === false) {
            $paramConditions = $this->paramsConditions($params, $match);
            $sql .= " WHERE $paramConditions[0]";
        } elseif ($params && $match === true) {
            $paramConditions = $this->paramsConditions($params, $match);
            $sql .= " WHERE MATCH ($paramConditions[0]) AGAINST (:query $paramConditions[2])";
        }

        if ($orderBy) {
            [$sortColumn, $sortOrder] = $orderBy;
            $sql .= " ORDER BY $sortColumn $sortOrder";
        }

        if ($pagination) {
            $limit = (int) $pagination[0];
            $offset = (int) $pagination[1];
            $sql .= " LIMIT $limit OFFSET $offset";
            // die($sql);  // ? DEBUG
        }
        $this->query($sql, $paramConditions[1]);

        return $this;  // Now we can user arrows to call upon another method if we want
    }

    /**
     * @param string $table
     * @param array $params [e.g. 'id' => $id]
     * @return integer
     */
    public function insert(string $table, array $params): int
    {
        $fields = implode(',', array_keys($params));
        $placeholders = ':' . implode(', :', array_keys($params));
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";

        $this->query($sql, $params);
        $result = $this->pdo->lastInsertId();

        return $result;
    }

    public function delete(string $table, array $params = []): int
    {
        $paramConditions = $this->paramsConditions($params);
        $sql = "DELETE FROM $table";
        if ($params) {
            $sql .= " WHERE $paramConditions[0]";
        }

        $this->query($sql, $paramConditions[1]);

        return $this->stmt->rowCount();
        // return true;
    }

    public function update(string $table, array $set = [], array $params = []): int
    {
        $setString = implode(', ', array_map(fn($field) => "$field=:$field", array_keys($set)));
        // die($setString);
        $paramConditions = $this->paramsConditions($params);
        $sql = "UPDATE $table SET $setString";
        if ($params) {
            $sql .= " WHERE $paramConditions[0]";
        }

        $combinedParams = array_merge($paramConditions[1], $set);
        $this->query($sql, $combinedParams);

        return $this->stmt->rowCount();
    }

    public function query(string $sql, array $paramsExecute = []): bool
    {
        try {
            $this->stmt = $this->pdo->prepare($sql);
            return $this->stmt->execute($paramsExecute);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function get(): array|false|null
    {
        return $this->stmt->fetchall();
    }

    public function column(): int|false|null
    {
        // die($this->stmt->fetchColumn()); // ? DEBUG
        return $this->stmt->fetchColumn();
    }

    public function first(): array|false|null
    {
        return $this->stmt->fetch();
    }
}
