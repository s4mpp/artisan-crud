<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Console\Commands\Crud;

class MakeControllerCrudCommand extends Crud
{
    private $resource;
    
    private $title;

    private $folder;

    private $with_account;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:controller-crud
        {resource : Resource to manipulation}
        {--title= : Title of resource}
        {--folder= : Folder views}
        {--with-account : With fields account [false]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new crud controller';

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

        $file_destination = app_path('Http/Controllers/'.Str::ucfirst($this->resource).'Controller.php');

        if(Crud::checkIfFileExists($file_destination))
        {
            $this->error('Controller '.Str::ucfirst($this->resource).'Controller already exists!');

            return 0;
        }

        $name_file = Crud::getNameFileStub('controller-crud', $this->with_account);

        $content = Crud::getSourceFile(app_path('../stubs/'.$name_file.'.stub'), $this->_getStubVariables());

        Crud::saveFile($file_destination, $content);

        $this->info('Controller '.Str::ucfirst($this->resource).'Controller created successfully!');

        return 0;
    }

    private function _setParams()
    {
        $this->resource = Str::lower($this->argument('resource'));
        
        $this->title = $this->option('title') ?? 'item';
        
        $this->folder = Str::lower($this->option('folder') ?? 'xxxx');

        $this->with_account = $this->option('with-account');
    }

    private function _getStubVariables()
    {
        return [
            'CLASS' => Str::ucfirst($this->resource),
            'FOLDER' => $this->folder,
            'RESOURCE_SINGULAR' => Str::snake(Str::lower($this->resource)),
            'RESOURCE_PLURAL' => Str::plural(Str::snake(Str::lower($this->resource))),
            'TITLE_RESOURCE' => Str::lower($this->title),
        ];
    }
}
