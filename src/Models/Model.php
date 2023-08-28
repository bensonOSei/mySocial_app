<?php

namespace Benson\InforSharing\Models;

use Benson\InforSharing\Handlers\ErrorHandler;
use Benson\InforSharing\Helpers\Traits\CanMemoize;
use Benson\InforSharing\Helpers\Traits\CanQuery;
use Exception;

class Model
{
    use CanQuery, CanMemoize;

    /**
     * The table to be queried
     * 
     * @var string
     */
    protected string $table;

    /**
     * The columns in the table
     * 
     * @var array
     */
    protected array $columns;

    /**
     * The sensitive columns in the table
     * 
     * @var array
     */
    protected array $sensitiveColumns = [];

    private array $queryResults = [];


    /**
     * Create a new record
     * 
     * @param array $columnsAndValues The columns and values to be inserted
     * @return bool Returns true if the record was created and false if it wasn't
     */
    public function create(array $columnsAndValues): bool
    {
        // Separate the columns and the values
        $segInputs = $this->segregateColumnsAndValues($columnsAndValues);
        $columns = $segInputs['columns'];
        $values = $segInputs['values'];

        // Check if columns match the columns in the columns in the table
        $this->checkColumnCoherence($columns);

        $this->insert($columns, $values);

        return $this->executed;
    }

    /**
     * Find a record by id
     * 
     * @param int $id The id of the record to be found
     * @return array Returns an array of the record found
     */
    public function find(int $id): array
    {
        return $this->sendResponse($this->select()->where('id', '=', $id)->get()[0]);
    }



    /**
     * Find all records
     * 
     * @return array Returns an array of all the records found
     */
    public function all(): array
    {
        return $this->select()->get();
    }

    
    /**
     * Find the first record
     * 
     * @return array Returns an array of the first record found
     */
    public function findFirst(): array
    {
        $result = $this->memoize(
            'findFirst',
            fn () => $this->select()
                ->orderBy('id', 'DESC')->first()
        );

        return $this->sendResponse($result);
    }


    /**
     * Find a record by email
     * 
     * @param string $email The email of the record to be found
     * @return array Returns an array of the record found
     */
    public function findByEmail(string $email, $allowAll = false): array
    {
        // cache the query results
        $queryResults = $this->memoize('findByEmail', function () use ($email) {
            
            return $this->select()->where('email', '=', $email)->first();
        });

        $this->queryResults = $queryResults;

        if ($allowAll) {
            return $queryResults;
        }

        return $this->sendResponse($queryResults);
    }

    /**
     * Hide sensitive data from response
     * 
     * @param array $data The data to be sent
     * @return array Returns an array of the data to be sent without the sensitive data
     */
    public function sendResponse(array $data): array
    {
        foreach ($data as $key => $value) {
            // var_dump($key);
            // exit;
            if (in_array($key, $this->sensitiveColumns)) {

                unset($data[$key]);
            }
        }


        return count($data) > 0 ? $data : ["message" => 'Record not found'];
    }



    /**
     * Edit a record
     * 
     * @param array $columnsAndValues The columns and values to be updated
     * @param int $id The id of the record to be updated
     * @return bool Returns true if the record was updated and false if it wasn't#
     */
    public function edit(array $columnsAndValues, int $id)
    {
        
        foreach ($columnsAndValues as $column => $value) {
            $this->hasColumn($column);

            // if (in_array($column, $this->sensitiveColumns)) {
            //     throw new Exception(
            //         "You are not allowed to update $column column"
            //     );
            // }

            // check if value is empty
            if (empty($value)) {
                throw new Exception(
                    "$column cannot be empty"
                );
            }
        }
        
        // Separate the columns and the values
        $segInputs = $this->segregateColumnsAndValues($columnsAndValues);
        $columns = $segInputs['columns'];
        $values = $segInputs['values'];

        $this->update($columns, $values)->where('id', '=', $id);


        return $this->executed;
    }




    /**
     * Separate the columns and the values
     * 
     * @param array $columnsAndValues The columns and values to be separated
     * @return array Returns an array of columns and values
     */
    private function segregateColumnsAndValues(array $columnsAndValues): array
    {
        $columns = [];
        $values = [];
        foreach ($columnsAndValues as $column => $value) {
            array_push($columns, $column);
            array_push($values, $value);
        }

        return [
            'columns' => $columns,
            'values' => $values
        ];
    }

    /**
     * Check if columns match the columns in the columns in the table
     * 
     * @param array $inputtedColumns The columns to be checked
     * @return void Throws an exception if the columns do not match
     */
    private function checkColumnCoherence(array $inputtedColumns)
    {
        if (count($inputtedColumns) !== count($this->columns)) {
            throw new Exception(
                "Number of columns do not match with $this->table table columns.  Check your columns and try again."
            );
        };

        for ($count = 0; $count < count($inputtedColumns); $count++) {

            if ($inputtedColumns[$count] !== $this->columns[$count]) {
                throw new Exception(
                    "$inputtedColumns[$count] not found in $this->table table"
                );
            }
        }
    }


    /**
     * Check if column exists in the table
     * 
     * @param string $column The column to be checked
     * @return void Throws an exception if the column does not exist
     */
    private function hasColumn(string $column)
    {
        if(!in_array($column, $this->columns)) {
            throw new Exception(
                "$column not found in $this->table table"
            );
        }
    }
}
