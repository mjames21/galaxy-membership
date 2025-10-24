<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeService extends Command
{
    protected $signature = 'make:service {name : Service class name, e.g. ExportService or Imports/PeopleCsvImporter}';
    protected $description = 'Create a Service class in app/Services (PSR-4, nested paths allowed)';

    public function handle(Filesystem $files): int
    {
        $name = trim($this->argument('name'), '/\\');
        $pathInServices = str_replace(['\\','//'], '/', $name);
        $class = class_basename($pathInServices);
        $subpath = trim(Str::beforeLast($pathInServices, '/'), '/');
        $dir = app_path('Services' . ($subpath ? "/$subpath" : ''));

        $namespace = 'App\\Services' . ($subpath ? '\\' . str_replace('/', '\\', $subpath) : '');
        $file = $dir . "/{$class}.php";

        if ($files->exists($file)) {
            $this->error("Service already exists: {$file}");
            return self::FAILURE;
        }

        $files->ensureDirectoryExists($dir);
        $stub = <<<PHP
        <?php

        namespace {$namespace};

        class {$class}
        {
            //
        }
        PHP;

        $files->put($file, $stub);
        $this->info("Service created: " . str_replace(base_path().'/', '', $file));
        return self::SUCCESS;
    }
}
