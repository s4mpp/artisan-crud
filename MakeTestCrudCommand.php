<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Console\Commands\Crud;

class MakeTestCrudCommand extends Crud
{
    private $resource;
    
    private $title;

    private $table;

    private $env;
    
    private $folder;
    
    private $with_account = false;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:test-crud
        {resource : Resource to manipulation}
        {--title= : Name of resource}
        {--table= : Name of table}
        {--env= : Environment [admin]}
        {--with-account : With fields account [false]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new crud test';

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

        if(!in_array($this->env, ['admin', 'account']))
        {
            return $this->error('Invalid environment');
        }

        $file_destination = app_path('../tests/Feature/'.$this->folder.'/'.Str::ucfirst($this->resource).'Test.php');

        if(Crud::checkIfFileExists($file_destination))
        {
            $this->error('Test '.$this->folder.'/'.Str::ucfirst($this->resource).'Test already exists!');

            return 0;
        }

        $name_stub = ($this->env == 'admin') ? Crud::getNameFileStub('test-crud', $this->with_account) : 'test-crud-account';

        $content = Crud::getSourceFile(app_path('../stubs/'.$name_stub.'.stub'), $this->_getStubVariables());

        Crud::saveFile($file_destination, $content);

        $this->info('Test '.$this->folder.'/'.Str::ucfirst($this->resource).'Test created successfully!');

        return 0;
    }

    private function _setParams()
    {
        $this->resource = Str::lower($this->argument('resource'));
        
        $this->title = $this->option('title') ?? 'item';
        
        $this->table = $this->option('table') ?? $this->title;

        $this->env = $this->option('env') ?? 'admin';

        $this->with_account = ($this->env == 'admin') && $this->option('with-account');

        $this->folder = Str::ucfirst($this->env);
    }

    private function _getStubVariables()
    {
        return [
            'CLASS' => Str::ucfirst($this->resource),
            'RESOURCE' => Str::lower($this->resource),
            'ROUTE' => Str::lower($this->title),
            'TABLE' => Str::lower($this->table)
        ];
    }
}
