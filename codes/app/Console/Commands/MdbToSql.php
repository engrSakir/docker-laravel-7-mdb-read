<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RebaseData\Converter\Converter;
use RebaseData\InputFile\InputFile;
use MDBTools\Facades\Parsers\MDBParser;
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
    protected $description = 'Command description';

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
    
        $this->info('php artisan mdbtosql:generate');

        // $filePath = 'C:\laragon\www\mdb-data-reader\public\mdb\sample.mdb';
        $filePath = public_path('sample.mdb');


        if (!file_exists($filePath)) {
            $this->error('wrong path');
        } else {
            $this->info('ok path');
        }

        $inputFiles = [new InputFile($filePath)];

        $converter = new Converter();
        $database = $converter->convertToDatabase($inputFiles);
        $tables = $database->getTables();

        foreach ($tables as $table) {
            dd($table);cd cd 
            echo "Reading table '".$table->getName()."'\n";

            $rows = $table->getRowsIterator();
            foreach ($rows as $row) {
                echo implode(', ', $row)."\n";
            }
        }

        return 0;
    }
}
