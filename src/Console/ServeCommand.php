<?php


namespace Oursdreams\Export\Console;


use Illuminate\Console\Command;

class ServeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start export serve';

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
                    if (!file_exists(dirname(dirname(__FILE__))."\\Go\\windows.exe")){
                        echo $this->error("Serve file lost");
                    }else{
                        pclose(popen("start /b ".dirname(dirname(__FILE__))."\\Go\\windows.exe",'r'));
                        try{
                            fsockopen('127.0.0.1','9722',$error,$errorstr,2);
                            $this->info("Serve start success！");
                        }catch(\Exception $e){
                            $this->error("Serve start failure！");
                        }
                    }
                    break;
            }
        }
    }

}