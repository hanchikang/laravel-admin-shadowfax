<?php

namespace CKHan\LASAdapter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class AdapterCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'las:adapter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the compatible files for shadowfax';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Execute the console command.
     * @param Filesystem $filesystem
     */
    public function handle(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        $this->createEncoreLaravelAdminCleaner();

        $this->createAdminController();

        $this->info("The compatible files generated successfully. \r\n");
        $this->showTree();
    }

    /**
     * Create the AdminController.php file
     */
    protected function createAdminController()
    {
        $filename = base_path('app/Admin/Controllers/AdminController.php');

        $search = <<<'SEARCH'
        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($this->grid());
SEARCH;

        $replace = <<<'REPLACE'
        $grid = $this->grid();
        $exporter = new \ReflectionClass(\Encore\Admin\Grid\Exporter::class);
        if(in_array(request($exporter->getStaticPropertyValue('queryName')), [$exporter->getConstant('SCOPE_ALL'), $exporter->getConstant('SCOPE_CURRENT_PAGE'), $exporter->getConstant('SCOPE_SELECTED_ROWS')]))
            return $grid->exportRequest();

        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($grid);
REPLACE;

        file_put_contents($filename, str_replace(
            $search,
            $replace,
            file_get_contents(base_path('vendor/encore/laravel-admin/src/Controllers/AdminController.php'))
        ));
    }

    /**
     * Create the EncoreLaravelAdminCleaner.php file
     */
    protected function createEncoreLaravelAdminCleaner()
    {
        $cleaner_dir = base_path('app/Cleaners');
        $this->filesystem->makeDirectory($cleaner_dir, 0755, true, true);
        file_put_contents($cleaner_dir . '/EncoreLaravelAdminCleaner.php', file_get_contents(__DIR__ . '/../../stubs/EncoreLaravelAdminCleaner.stub'));
    }

    /**
     * Show the tree of the compatible files
     */
    protected function showTree()
    {
        $tree = <<<TREE
    app
    ├── Cleaners
    │   ├── EncoreLaravelAdminCleaner.php
    ├── Admin
    │   ├── Controllers
    │   │   ├── AdminController.php
TREE;

        $this->info($tree);
    }
}
