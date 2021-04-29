<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class PatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emizor:patch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Patch values missing or new fields in database';

    protected $files;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->ensurePatchesDoesntAlreadyExist();

        $directory = $this->getPatchesFiles();
        $no_patches = true;

        foreach ($directory as $file) {

            //check file was executed

            $filename = $this->getPatchName($file);

            $patch = \DB::table("fel_patches")->wherePatch($filename)->first();

            if (empty($patch)) {

                $no_patches = false;

                $this->info('Started processing: ' . $filename . ' at ' . now());

                $instance_class = $this->resolve($filename);

                $instance_class->run();

                $this->warn('Finish processed: ' . $filename . ' at ' . now());

                \DB::table("fel_patches")->insert(["patch" => $filename]);
            }
        }

        if ($no_patches) {
            $this->info('All patches are proccessed ');
        }
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

    public function resolve($file)
    {
        $class = Str::studly(implode('_', array_slice(explode('_', $file), 4)));

        return new $class;
    }

    protected function ensurePatchesDoesntAlreadyExist()
    {

        $patchFiles = $this->files->glob(__DIR__ . '/../../Patches/*.php');

        foreach ($patchFiles as $patchFile) {
            $this->files->requireOnce($patchFile);
        }
    }

    protected function getPatchesFiles()
    {
        return $this->files->glob(__DIR__ . '/../../Patches/*.php');
    }

    protected function getPatchName($file)
    {
        return str_replace('.php', '', basename($file));
    }
}
