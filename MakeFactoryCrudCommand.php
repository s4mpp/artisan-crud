<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Console\Commands\Crud;

class MakeFactoryCrudCommand extends Crud
{
    private $name_studly;

    private $with_account = false;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:factory-crud
        {name : Resource to manipulation}
        {--with-account : With fields account [false]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new crud Factory';

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
        $this->_setParams();

        $file_destination = app_path('../database/factories/'.$this->name_studly.'Factory.php');

        if(Crud::checkIfFileExists($file_destination))
        {
            $this->error('Factory '.$this->name_studly.'Factory already exists!');

            return 0;
        }

        if(!$this->with_account)
        {
            $this->call('make:factory', ['name' => $this->name_studly.'Factory']);

            return 0;
        }

        $content = Crud::getSourceFile(app_path('../stubs/factory-with-account.stub'), $this->_getStubVariables());

        Crud::saveFile($file_destination, $content);

        $this->info('Factory '.$this->name_studly.'Factory with field account created successfully!');

        return 0;
    }

    private function _setParams()
    {
        $name = $this->argument('name');

        $this->name_studly = Str::studly($name);

        $this->with_account = $this->option('with-account');
    }

    private function _getStubVariables()
    {
        return [
            'factory' => $this->name_studly
        ];
    }
}
