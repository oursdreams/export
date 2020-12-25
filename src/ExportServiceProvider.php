<?php


namespace Oursdreams\Export;


use Oursdreams\Export\Console\ExportCommand;
use Oursdreams\Export\Console\ServeCommand;

class ExportServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        ExportCommand::class,
        ServeCommand::class,
    ];

    public function register()
    {
        $this->commands($this->commands);
    }
}