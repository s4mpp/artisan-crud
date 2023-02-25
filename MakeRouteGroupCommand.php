<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Console\Commands\Crud;
use Illuminate\Support\Facades\Route;

class MakeRouteGroupCommand extends Crud
{
    private $prefix;
    
    private $route;
    
    private $env;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:route-group 
        {--prefix= : Prefix of group}
        {--route= : Name of routes}
        {--env= : Environment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new route group menu';

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

        $name_route = Str::lower($this->route);

        if($this->env == 'account')
        {
            $name_route = 'my_'.$name_route;
        }

        $names = [
            'index_name' => $name_route.'_index',
            'create_name' => $name_route.'_create',
            'edit_name' => $name_route.'_edit',
            'view_name' => $name_route.'_view'
        ];

        foreach($names as $name)
        {
            if(Route::has($name))
            {
                $this->error('Route '.$this->env.'/'.$name_route.' already exists!');

                return 0;
            }
        }

        $content = Crud::getSourceFile(app_path('../stubs/route-group.stub'), $this->_getStubVariables($names));

        file_put_contents(app_path('../routes/'.$this->env.'.php'), $content, FILE_APPEND);

        $this->info('Route '.$this->env.'/'.$name_route.' created successfully!');

        return 0;
    }

    private function _setParams()
    {        
        $this->route = $this->option('route');
        
        $this->prefix = $this->option('prefix');

        $this->env = $this->option('env');
    }

    private function _getStubVariables(array $names): array
    {
        $is_admin = ($this->env == 'admin');

        return array_merge([
            'prefix' => Str::lower(Str::plural($this->prefix)),
            'controller' => Str::ucfirst($this->route).'Controller',
            
            'method_index' => ($is_admin) ? 'index' : 'myIndex',
            'method_create' => ($is_admin) ? 'create' : 'myCreate',
            'method_store' => ($is_admin) ? 'store' : 'myStore',
            'method_edit' => ($is_admin) ? 'edit' : 'myEdit',
            'method_update' => ($is_admin) ? 'update' : 'myUpdate',
            'method_view' => ($is_admin) ? 'view' : 'myView',
        ], $names);
    }
}
