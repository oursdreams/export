<?php

namespace Oursdreams\Export\Console;


use Illuminate\Console\Command;

class ServeStopCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop export serve';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try{
            fsockopen('127.0.0.1','9722',$error,$errorstr,2);
            $this->error("The port is Occupied!");
        }catch(\Exception $e){
            switch (PHP_OS){
                case "WINNT":
                    $this->error("Does not support window！");
                    break;
                //case "Linux"
                default:
                    exec("fuser -k -n tcp 9722",$output,$return);
                    $this->info("Serve stop success！");
            }
        }
    }

}