<?php


namespace Oursdreams\Export;


use Oursdreams\Export\Console\ExportCommand;
use Oursdreams\Export\Console\ServeStartCommand;
use Oursdreams\Export\Console\ServeStopCommand;

class ExportServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        ExportCommand::class,
        ServeStartCommand::class,
        ServeStopCommand::class,
    ];

    public function register()
    {
        $this->commands($this->commands);
    }
}