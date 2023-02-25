<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Console\Commands\Crud;

class MakeMigrationCrudCommand extends Crud
{
    private $name_plural;
    
    private $name_plural_snake;

    private $with_account = false;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:migration-crud
        {name : Resource to manipulation}
        {--with-account : With fields account [false]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new crud Migration';

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

        if(Crud::checkIfFileExists(app_path('../database/migrations/*create_'.$this->name_plural_snake.'_*.php')))
        {
            $this->error('Migration create_'.$this->name_plural_snake.' already exists!');

            return 0;
        }

        if(!$this->with_account)
        {
            $this->call('make:migration', [
                'name' => 'create_'.$this->name_plural_snake.'_table',
                '--create' => $this->name_plural_snake,
            ]);

            return 0;
        }

        $content = Crud::getSourceFile(app_path('../stubs/migration.create-with-account.stub'), $this->_getStubVariables());

        Crud::saveFile('database/migrations/'.date('Y_m_d_His').'_create_'.$this->name_plural_snake.'_table.php', $content);

        $this->info('Migration create_'.$this->name_plural_snake.'_table with field account created successfully!');

        return 0;
    }

    private function _setParams()
    {
        $name = $this->argument('name');
        
        $this->name_plural = Str::lower(Str::plural($name));

        $this->name_plural_snake = Str::snake($this->name_plural, '_');

        $this->with_account = $this->option('with-account');
    }

    private function _getStubVariables()
    {
        return [
            'table' => $this->name_plural,
            'class' => 'Create'.Str::ucfirst(Str::camel($this->name_plural)).'Table'
        ];
    }
}
