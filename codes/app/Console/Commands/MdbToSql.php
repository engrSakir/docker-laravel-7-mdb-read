<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use RebaseData\Converter\Converter;
use RebaseData\InputFile\InputFile;
use RebaseData\Exceptions\ConversionException;
use Illuminate\Support\Facades\DB;

class MdbToSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mdbtosql:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert MDB to SQL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting conversion...');

        $filePath = public_path('data.MDB');

        if (!file_exists($filePath)) {
            $this->error('File not found.');
            return 1;
        }

        try {
            $inputFiles = [new InputFile($filePath)];
            $converter = new Converter();
            $database = $converter->convertToDatabase($inputFiles);
            $tables = $database->getTables();

            foreach ($tables as $table) {
                $this->processTable($table);
            }

            $this->info('Conversion completed successfully.');
            return 0;

        } catch (Exception $e) {
            $this->error('Conversion failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Process each table and insert data into MySQL using chunking.
     *
     * @param \RebaseData\Database\Table $table
     */
    protected function processTable($table)
    {
        $tableName = $table->getName();
        $columns = $table->getColumns();
        $rowCount = $table->getRowCount();

        $batchSize = 10000; // Adjust this value based on your needs

        $this->info("Processing table {$tableName} with {$rowCount} rows...");

        $table->getRowsIterator()->chunk($batchSize)->each(function ($chunk) use ($tableName, $columns) {
            $this->insertBatch($tableName, $columns, $chunk);
        });
    }

    /**
     * Perform batch insert into MySQL.
     *
     * @param string $tableName
     * @param array $columns
     * @param \RebaseData\Database\Row[] $rows
     */
    protected function insertBatch($tableName, $columns, $rows)
    {
        dd($tableName, $columns, $rows);

        $placeholders = implode(',', array_fill(0, count($columns), '?'));

        $query = "INSERT INTO {$tableName} (" . implode(',', $columns) . ") VALUES ";

        $valuePlaceholder = '(' . $placeholders . ')';
        $values = array_fill(0, count($rows), $valuePlaceholder);

        $query .= implode(',', $values);

        $flattenedData = [];

        foreach ($rows as $row) {
            $flattenedData = array_merge($flattenedData, $row->toArray());
        }
        
        try {
            DB::insert($query, $flattenedData);
        } catch (\Exception $e) {
            $this->error("Error inserting data into {$tableName}: " . $e->getMessage());
        }
    }
}
