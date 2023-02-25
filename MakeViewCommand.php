<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Console\Commands\Crud;

class MakeViewCommand extends Crud
{
    private $folder;
    
    private $env;
    
    private $resource;

    private $type;
    
    private $title;
    
    private $name_file;

    private $with_account = false;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view
        {folder : Folder name}
        {--type= : Type page [index]}
        {--env= : Environment [admin]}
        {--resource= : Resource to manipulation [item]}
        {--title= : Titles of pages [item]}
        {--with-account : With fields account [false]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new view';

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

        if(!in_array($this->type, ['blank', 'index', 'form', 'create', 'edit', 'details']))
        {
            return $this->error('Invalid view type');
        }

        if(!in_array($this->env, ['admin', 'account']))
        {
            return $this->error('Invalid environment');
        }

        $path = '../resources/views/'.$this->folder;

        $file_destination = app_path($path.'/'.$this->name_file.'.blade.php');

        if(Crud::checkIfFileExists($file_destination))
        {
            $this->error('View '.$this->folder.'/'.$this->name_file.' already exists!');

            return 0;
        }

        $content = Crud::getSourceFile(app_path('../stubs/'.$this->_getNameFileStub().'.stub'), $this->_getStubVariables());

        Crud::saveFile(
            $file_destination,
            $content,
            app_path($path)
        );

        $this->info('View '.$this->folder.'/'.$this->name_file.' created successfully!');

        return 0;
    }

    private function _setParams()
    {
        $this->folder = Str::lower($this->argument('folder'));

        $this->env = $this->option('env') ?? 'admin';
        
        $this->resource = $this->option('resource') ?? 'item';
        
        $this->title = $this->option('title') ?? 'item';
        
        $this->type = $this->option('type') ?? 'blank';
        
        $this->name_file = ($this->env == 'admin') ? $this->type : 'my_'.$this->type;

        $this->with_account = ($this->env == 'admin') && $this->option('with-account');
    }

    private function _getStubVariables()
    {
        $resource = Str::snake(Str::lower($this->resource), '_');

        $titles = $this->_getTitlesPage();

        return [
            'FOLDER' => $this->folder,
            'EXTENDS' => $this->env,
            'TITLE' => $titles[$this->type],
            'RESOURCE_PLURAL' => Str::plural($resource),
            'RESOURCE_SINGULAR' => $resource,
            'TITLE_REGISTER' => $titles['create']
        ];
    }

    private function _getNameFileStub(): string
    {
        $env = in_array($this->type, ['blank', 'form']) ? null : $this->env;

        $name_stub = in_array($this->type, ['form', 'index', 'details'])
        ? 
        Crud::getNameFileStub($this->type, $this->with_account)
        :
        $this->type;

        if($env)
        {
            $name_stub = $env.'.'.$name_stub;
        }

        return 'view.'.$name_stub;
    }

    private function _getTitlesPage(): array
    {
        return [
            'blank' => Str::ucfirst($this->title),
            'index' => Str::ucfirst(Str::plural($this->title)),
            'create' => 'Adicionar '.Str::lower($this->title),
            'edit' => 'Editar '.Str::lower($this->title),
            'details' => 'Visualizar '.Str::lower($this->title),
            'form' => null,
        ];
    }
}
