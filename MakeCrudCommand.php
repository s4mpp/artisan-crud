<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class MakeCrudCommand extends Command
{
    private $name_studly;

    private $name_plural;

    private $name_plural_snake;
    
    private $title;

    private $with_account = false;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud 
        {name : Name Crud singular}
        {--title= : Title Crud singular}
        {--with-account : With fields account [false]}';
        

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Crud';

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
        $this->_setCasts();

        $this->_createController();
        
        $this->_createModel();
        
        $this->_createMigration();
        
        $this->_createFactory();
        
        $this->_createSeeder();
        
        $this->_createRequest();
        
        $this->_createTest();
        
        $this->_createViews();
        
        $this->_createMenuItem();

        $this->_createRouteGroup();
        
        $this->_runMigration();
    }

    private function _setCasts()
    {
        $name = $this->argument('name');

        $this->name_studly = Str::studly($name);

        $this->name_plural = Str::plural($name);
        
        $this->name_plural_snake = Str::snake($this->name_plural, '_');

        $this->title = $this->option('title') ?? 'item';

        $this->with_account = $this->option('with-account');
    }

    private function _createController()
    {
        $this->call('make:controller-crud', [
            'resource' => $this->name_studly,
            '--title' => $this->title,
            '--folder' => $this->name_plural,
            '--with-account' => $this->with_account,
        ]);
    }

    private function _createModel()
    {
        $this->call('make:model-crud', [
            'name' => $this->name_studly,
            '--with-account' => $this->with_account,
        ]);
    }

    private function _createMigration()
    {
        $this->call('make:migration-crud', [
            'name' => $this->name_plural_snake,
            '--with-account' => $this->with_account,
        ]);
    }

    private function _createFactory()
    {
        $this->call('make:factory-crud', [
            'name' => $this->name_studly,
            '--with-account' => $this->with_account,
        ]);
    }

    private function _createSeeder()
    {
        $this->call('make:seeder', [
            'name' => $this->name_studly.'Seeder',
        ]);
    }

    private function _createRequest()
    {
        $this->call('make:request-crud', [
            'name' => $this->name_studly,
            '--with-account' => $this->with_account,
        ]);

        if($this->with_account)
        {
            $this->call('make:request', [
                'name' => 'My'.$this->name_studly.'Request',
            ]);
        }
    }

    private function _createTest()
    {
        $data = [
            'resource' => $this->name_studly,
            '--title' => Str::plural($this->title),
            '--table' => $this->name_plural_snake,
        ];

        $this->call('make:test-crud', array_merge([
            '--env' => 'admin',
            '--with-account' => $this->with_account,
        ], $data));
        
        if($this->with_account)
        {
            $this->call('make:test-crud', array_merge(['--env' => 'account'], $data));
        }
    }

    private function _createViews()
    {
        $types = [
            'index', 'create', 'edit', 'details', 'form',
        ];

        $all_options = [
            'folder' => $this->name_plural,
            '--resource' => $this->name_studly,
            '--title' => $this->title,
        ];

        $options_admin = array_merge(['--env' => 'admin', '--with-account' => $this->with_account], $all_options);

        foreach($types as $type)
        {
            $this->call('make:view', array_merge(['--type' => $type], $options_admin));
        }

        if($this->with_account)
        {
            $options_account = array_merge(['--env' => 'account'], $all_options);

            foreach($types as $type)
            {
                $this->call('make:view', array_merge(['--type' => $type], $options_account));
            }
        }
    }

    private function _createMenuItem()
    {
        $index = Str::plural($this->title);

        $title = Str::ucfirst(Str::plural($this->title));

        $this->call('make:menu-item', [
            '--env' => 'admin',
            '--index' => $index,
            '--title' => $title,
            '--route' => Str::lower($this->name_studly).'_index',
        ]);

        if($this->with_account)
        {
            $this->call('make:menu-item', [
                '--env' => 'account',
                '--index' => $index,
                '--title' => $title,
                '--route' => Str::lower($this->name_studly).'_index',
            ]);
        }
    }

    private function _createRouteGroup()
    {
        $prefix = Str::plural($this->title);
        $route = $this->name_studly;

        $this->call('make:route-group', [
            '--env' => 'admin',
            '--prefix' => $prefix,
            '--route' => $route
        ]);

        if($this->with_account)
        {
            $this->call('make:route-group', [
                '--env' => 'account',
                '--prefix' => $prefix,
                '--route' => $route
            ]);
        }
    }

    private function _runMigration()
    {
        $this->call('migrate');
    }
}
