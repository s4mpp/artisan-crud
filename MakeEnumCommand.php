<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Console\Commands\Crud;

class MakeEnumCommand extends Crud
{
    private $name_studly;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum
        {name : Name of enum}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum file';

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

        $file_destination = app_path('Enums/'.$this->name_studly.'.php');

        if(Crud::checkIfFileExists($file_destination))
        {
            $this->error('Enum '.$this->name_studly.' already exists!');

            return 0;
        }

        $content = Crud::getSourceFile(app_path('../stubs/enum.stub'), $this->_getStubVariables());

        Crud::saveFile($file_destination, $content);

        $this->info('Enum '.$this->name_studly.' created successfully!');

        return 0;
    }

    private function _setParams()
    {
        $name = $this->argument('name');

        $this->name_studly = Str::studly($name);
    }

    private function _getStubVariables()
    {
        return [
            'name' => $this->name_studly
        ];
    }
}
