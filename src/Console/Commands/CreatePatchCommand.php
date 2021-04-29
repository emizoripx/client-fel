<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use Illuminate\Console\Command;

class CreatePatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emizor:make-patch {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create patch for new implementation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = \Carbon\Carbon::now()->format("Y_m_d_his");

        $new_file = $date . "_" . $this->argument("name") . ".php";

        $name_formatted = $this->dashesToCamelCase($this->argument("name"), true);

        // create patche file
        $file = __DIR__ . '/BasePatch.php';
        $newfile = __DIR__ . '/../../Patches/' . $new_file;

        if (!copy($file, $newfile)) {
            $this->info("Error in creating patch.");
        }
        $content = file_get_contents($newfile);

        file_put_contents($newfile, strtr($content, ["BasePatch" => $name_formatted]));
    }

    function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {

        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
}
