<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

abstract class Crud extends Command
{
    protected static function checkIfFileExists(string $file_path): bool
    {
        return !empty(glob($file_path));
    }

    protected static function getSourceFile(string $stub_path, array $stub_variables)
    {
        return self::_getStubContents($stub_path, $stub_variables);
    }

    private static function _getStubContents($stub , $stub_variables = [])
    {
        $contents = file_get_contents($stub);

        foreach($stub_variables as $search => $replace)
        {
            $contents = str_replace('{{ '.$search.' }}' , $replace, $contents);
        }

        return $contents;
    }

    protected static function saveFile(string $destination, string $content, string $create_path_if_not_exist = null)
    {
        $filesystem = new Filesystem;

        if($create_path_if_not_exist && !$filesystem->isDirectory($create_path_if_not_exist))
        {
            $filesystem->makeDirectory($create_path_if_not_exist, 0777, true, true);
        }
        
        $filesystem->put($destination, $content);
    }

    protected function getNameFileStub(string $name_file, bool $with_account)
    {
        if($with_account)
        {
            $name_file .= '-with-account';
        }

        return $name_file;
    }
}
