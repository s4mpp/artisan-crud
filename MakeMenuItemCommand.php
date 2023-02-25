<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class MakeMenuItemCommand extends Command
{
    private $index;
    
    private $title;
    
    private $route;
    
    private $env;
    
    private $path;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:menu-item
        {--index= : Index menu}
        {--title= : Title menu}
        {--route= : Route menu}
        {--env= : Environment}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new item of menu';

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

        $items = $this->_getMenuItems();

        if(empty($items))
        {
            $this->error('Falha ao criar o menu '.$this->env.'!');

            return 0;
        }

        if(array_key_exists($this->index, $items))
        {
            $this->error('Menu ['.$this->env.'/'.$this->title.'] already exists!');

            return 0;
        }

        $new_menu = $this->_appendMenuItem($items);

        $this->_saveFile($new_menu);

        $this->info('Menu '.$this->env.'/'.$this->title.' created successfully!');

        return 0;
    }

    private function _setParams()
    {        
        $this->index = Str::lower($this->option('index'));
        
        $this->title = $this->option('title');
        
        $this->route = $this->option('route');
        
        $this->env = $this->option('env');

        $this->path = app_path('../resources/views/'.$this->env.'/menu.json');
    }

    private function _getMenuItems(): array
    {
        return (array)json_decode(file_get_contents($this->path));
    }

    private function _appendMenuItem(array $menu_items): array
    {
        $prefix_route = ($this->env == 'account') ? 'my_' : null;

        $menu_items[$this->index] = [
            'route' => $prefix_route.Str::lower($this->route),
            'title' => Str::ucfirst($this->title),
            'icon' => 'fa-dot-circle'
        ];

        return $menu_items;
    }

    private function _saveFile(array $menu_items)
    {
        file_put_contents($this->path, json_encode($menu_items, JSON_PRETTY_PRINT));
    }
}
