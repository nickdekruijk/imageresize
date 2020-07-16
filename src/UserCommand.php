<?php

namespace NickDeKruijk\ImageResize;

use Illuminate\Console\Command;

class UserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imageresize:delete {template? : A valid template}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->description = 'Delete the resized images from the "' . config('imageresize.route') . '" folder.';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arg = $this->arguments();
        if ($arg['command'] == 'imageresize:delete') {
            ResizeController::delete($arg['template']);
        }
    }
}
