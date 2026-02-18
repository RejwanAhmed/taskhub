<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository and interface';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        $interfacePath = app_path("Repositories/Contracts/{$name}RepositoryInterface.php");
        $repositoryPath = app_path("Repositories/Eloquent/{$name}Repository.php");

        if(!is_dir(dirname($interfacePath))) {
            mkdir(dirname($interfacePath), 0755, true);
        }

        if(!is_dir(dirname($repositoryPath))) {
            mkdir(dirname($repositoryPath), 0755, true);
        }

        if(!file_exists($interfacePath)) {
            file_put_contents($interfacePath,
                "<?php\n\nnamespace App\Repositories\Contracts;\n\ninterface {$name}RepositoryInterface\n{\n    // Define your methods here\n}\n"
            );
            $this->info("Created: {$interfacePath}");
        }

        if(!file_exists($repositoryPath)) {
            file_put_contents($repositoryPath, 
                "<?php\n\nnamespace App\Repositories\Eloquent;\n\nuse App\Repositories\Contracts\\{$name}RepositoryInterface;\n\nclass {$name}Repository implements {$name}RepositoryInterface\n{\n    // Implement methods here\n}\n"
            );
            $this->info("Created: {$repositoryPath}");
        }
    }
}
