<?php

namespace Oursdreams\Export\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all export commands';

    /**
     * @var string
     */
    public static $logo = <<<LOGO
     ______ __   __   ______    _____    ______    ______
    /_/___/ \ \ / /  | |    \  /     \  | |    \  /_  __/
   / /____   \ \ /   | |____/ /       \ | |____/   / /   
  /_/____/   / \ \   | |     /         \| | \     / /    
 / /____    / / \ \  | |     \         /| |\ \   / /    
/_/____/   /_/   \_\ |_|      \ _____ / |_| \_\ /_/                                                                                     
LOGO;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line(static::$logo);
        $this->line("1.0.0-beta");

        $this->comment('');
        $this->comment('Available commands:');
        $this->comment('    export:start');
        $this->comment('    export:stop');
    }
}
