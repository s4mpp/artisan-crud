<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Console\Commands\Crud;

class MakeRequestCrudCommand extends Crud
{
    private $name_studly;

    private $with_account = false;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:request-crud
        {name : Resource to manipulation}
        {--with-account : With fields account [false]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new crud Request';

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

        $file_destination = app_path('Http/Requests/'.$this->name_studly.'Request.php');

        if(Crud::checkIfFileExists($file_destination))
        {
            $this->error('Request '.$this->name_studly.' already exists!');

            return 0;
        }

        if(!$this->with_account)
        {
            $this->call('make:request', ['name' => $this->name_studly.'Request']);

            return 0;
        }

        $content = Crud::getSourceFile(app_path('../stubs/request-with-account.stub'), $this->_getStubVariables());

        Crud::saveFile($file_destination, $content);

        $this->info('Request '.$this->name_studly.'Request with field account created successfully!');

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
            'class' => $this->name_studly
        ];
    }
}
