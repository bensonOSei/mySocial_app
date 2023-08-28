<?php

declare(strict_types=1);

namespace Benson\InforSharing\Helpers\Traits;

use Benson\InforSharing\Database\Config;
use Benson\InforSharing\Handlers\ErrorHandler;
use PDOException;
use PDOStatement;

trait CanQuery
{
    private $db;

    public string $query;

    protected string $table;
    private PDOStatement $queryStatement;
    private bool $executed;
    private array $tableColumn;
    private string $newTableName;

    public function __construct()
    {
        $conn = new Config();
        $this->db = $conn->connect();
    }




    /**
     * Run a query
     * 
     * @param array $values The values to be bound to the query
     * @return null|self Returns the current instance of the class
     */
    protected function run(array $values): ?self
    {
        try {
            $this->executed = $this->queryStatement->execute($values);
            return $this;
        } catch (PDOException $e) {
            ErrorHandler::handle($e);
        }
    }

    protected function get(): ?array
    {
        try {
            $this->queryStatement = $this->db->prepare($this->query);
            $this->queryStatement->execute();
            return $this->queryStatement->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::handle($e);
        }
    }


    protected function first(): ?array
    {
        // echo $this->query;
        // exit;
        try {
            $this->queryStatement = $this->db->prepare($this->query);
            $this->queryStatement->execute();
            return $this->queryStatement->fetch();
        } catch (PDOException $e) {
            ErrorHandler::handle($e);
        }
    }


    protected function count(): ?int
    {
        try {
            $this->queryStatement->execute();
            return $this->queryStatement->rowCount();
        } catch (PDOException $e) {
            ErrorHandler::handle($e);
        }
    }



    /**
     * Perform a select query
     * @param string $table - The table to select from
     * @param array $columns - The columns to select
     * @param array $where - The where clause. This is an array of strings of the form 'column = value'
     */
    protected function select(array $columns = ['*'])
    {

        $query = "SELECT ";
        $query .= implode(', ', $columns);
        $query .= " FROM $this->table";
        $this->query = $query;
        return $this;
    }

    /**
     * Perform an update query
     * @param string $table - The table to update
     * @param array $columns - The columns to update. This is an array of strings of the form 'column = value'
     * @param array $where - The where clause. This is an array of strings of the form 'column = value'
     */
    protected function update(array $columns, array $values)
    {

        $query = "UPDATE $this->table SET ";
        $colSize = count($columns);

        for ($counter = 0; $counter < $colSize; $counter++) {
            if (($counter + 1) === $colSize) {
                $query .= "$columns[$counter] = ?";
                break;
            }
            $query .= "$columns[$counter] = ?,";
        }

        $this->query = $query;
        try {
            $this->queryStatement = $this->db->prepare($query);
            $this->run($values);
            return $this;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        try {
        } catch (PDOException $e) {
            ErrorHandler::handle($e);
        }
    }

    /**
     * Perform an insert query
     * @param string $table - The table to insert into
     * @param array $columns - The columns to insert into. This is an array of strings of the form 'column'
     */
    protected function insert(array $columns, array $values)
    {
        $query = 'INSERT INTO ' . $this->table;
        $columnQuery = '';
        $valuesQuery = '';
        $colSize = count($columns);
        for ($counter = 0; $counter < $colSize; $counter++) {
            if (($counter + 1) === $colSize) {
                $columnQuery .= "$columns[$counter]";
                $valuesQuery .= '?';
                break;
            }
            $columnQuery .= "$columns[$counter],";
            $valuesQuery .= '?,';
        }
        $query .= " ($columnQuery) VALUES($valuesQuery)";
        $this->query = $query;
        try {
            $this->queryStatement = $this->db->prepare($query);
            $this->run($values);

            return $this;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Perform a delete query
     * @param string $table - The table to delete from
     * @param array $where - The where clause. This is an array of strings of the form 'column = value'
     * @return bool - True if the query was successful, false otherwise
     */
    protected function delete(string $table, array $where): ?bool
    {
        try {
            $query = "DELETE FROM $table WHERE ";
            $query .= implode(' AND ', $where);
            $stmt = $this->connect()->prepare($query);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Perform a where clause
     * @param string $column - The column to check
     * @param string $operator - The operator to use
     * @param mixed $value - The value to check against
     * @return $this
     */
    protected function where(string $column, string $operator, mixed $value)
    {
        if (intval($value) === $value)
            $this->query .= " WHERE $column $operator $value";
        else
            $this->query .= " WHERE $column $operator '$value'";

        return $this;
    }

    /**
     * Perform an and where clause
     * @param string $column - The column to check
     * @param string $operator - The operator to use
     * @param string $value - The value to check against
     * @return $this
     */
    protected function and(string $column, string $operator, string $value)
    {
        $this->query .= " AND $column $operator $value";
        return $this;
    }

    /**
     * Perform an or where clause
     * @param string $column - The column to check
     * @param string $operator - The operator to use
     * @param string $value - The value to check against
     * @return $this
     */
    protected function or(string $column, string $operator, string $value)
    {
        $this->query .= " OR $column $operator $value";
        return $this;
    }

    /**
     * Perform an order by clause
     * @param string $column - The column to order by
     * @param string $direction - The direction to order by
     * @return $this
     */
    protected function orderBy(string $column, string $direction = 'ASC')
    {
        $this->query .= " ORDER BY $column $direction";
        return $this;
    }

    /**
     * Perform a limit clause
     * @param int $limit - The limit to use
     * @return $this
     */
    protected function limit(int $limit)
    {
        $this->query .= " LIMIT $limit";
        return $this;
    }

    /**
     * Perform an offset clause
     * @param int $offset - The offset to use
     * @return $this
     */
    protected function offset(int $offset)
    {
        $this->query .= " OFFSET $offset";
        return $this;
    }

    /**
     * Perform a join clause
     * @param string $table - The table to join
     * @param string $first - The first column to join on
     * @param string $second - The second column to join on
     * @return $this
     */
    protected function join(string $type, string $table, string $first,  string $second)
    {
        $type = strtoupper($type);
        $this->query .= " $type JOIN $table ON $this->table.$first = $table.$second";
        return $this;
    }

    /**
     * Perform a left join clause
     * @param string $table - The table to join
     * @param string $first - The first column to join on
     * @param string $second - The second column to join on
     * @return $this
     */
    protected function leftJoin(string $table, string $first,  string $second)
    {
        $this->query .= " LEFT JOIN $table ON $this->table.$first = $table.$second";
        return $this;
    }

    /**
     * Perform a right join clause
     * @param string $table - The table to join
     * @param string $first - The first column to join on
     * @param string $second - The second column to join on
     * @return $this
     */
    protected function rightJoin(string $table, string $first,  string $second)
    {
        $this->query .= " RIGHT JOIN $table ON $this->table.$first = $table.$second";
        return $this;
    }

    /**
     * Perform a full join clause
     * @param string $table - The table to join
     * @param string $first - The first column to join on
     * @param string $second - The second column to join on
     * @return $this
     */
    protected function fullJoin(string $table, string $first,  string $second)
    {
        $this->query .= " FULL JOIN $table ON $this->table.$first = $table.$second";
        return $this;
    }

    /**
     * Perform a union clause
     *
     */
    protected function union()
    {
        $this->query .= " UNION ";
        return $this;
    }


    /**
     * Set the values to be inserted into the table
     * 
     * @param array $values The values to be inserted
     * @return string Returns a string of placeholders for the values to be inserted
     */
    private function setValuePlaceholders(array $values): string
    {
        $valuesQuery = '';
        $colSize = count($values);
        for ($counter = 0; $counter < $colSize; $counter++) {
            if (($counter + 1) === $colSize) {
                $valuesQuery .= '?';
                break;
            }
            $valuesQuery .= '?,';
        }
        return $valuesQuery;
    }



    // public function createTable(string $tableName)
    // {
    //     $this->newTableName = $tableName;

    //     return $this;
    // }

    // public function id(string $columnName = 'id', array $constraints = [
    //     'length' => null,
    //     'primary',
    //     'unique',
    //     'foreign',
    //     'default' => null,
    //     'nullable',
    //     'type' => 'int'
    // ])
    // {
    //     $this->tableColumn = [
    //         $columnName => $constraints
    //     ];

    //     return $this;
    // }

    public function table()
    {

    }

}
