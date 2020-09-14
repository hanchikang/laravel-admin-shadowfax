<?php

namespace CKHan\LASAdapter;

use Illuminate\Support\ServiceProvider;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporter;

class LASAdapterServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\AdapterCommand::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $filename = base_path('app/Admin/Controllers/AdminController.php');
        if(file_exists($filename))
            require_once $filename;

        $this->app->register(\Encore\Admin\AdminServiceProvider::class);
        if(!class_exists('Admin', false))
            class_alias(\Encore\Admin\Facades\Admin::class, 'Admin');

        Grid::init(function (Grid $grid) {
            $grid->exporter(new \CKHan\LASAdapter\Grid\Exporters\CsvExporter());
        });

        Grid::macro('exportRequest', function () {
            // clear output buffer.
            if (ob_get_length()) {
                ob_end_clean();
            }

            $this->disablePagination();

            return $this->getExporter(request(Exporter::$queryName))->export();
        });
    }
}
