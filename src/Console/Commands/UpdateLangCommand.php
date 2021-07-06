<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use DirectoryIterator;
use Illuminate\Console\Command;

class UpdateLangCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emizor:update-lang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update language translator';

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

        $dir = new DirectoryIterator(resource_path('lang/en/'));
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $filename = $fileinfo->getFilename();

                $dir2 = new DirectoryIterator(resource_path('lang/es/'));

                $new_file = true;

                foreach ($dir2 as $fileinfo2) {
                    if (!$fileinfo2->isDot()) {
                        if ($fileinfo->getFilename() == $fileinfo2->getFilename()) {
                            $new_file = false;
                            break;
                        }
                    }
                }

                $en = include resource_path('lang/en/' . $filename);

                if ($new_file) {
                    file_put_contents(resource_path('lang/es/' . $filename), "");
                    $this->warn("New >lang/es/$filename was created");
                    $es = $en;
                } else {

                    $es = include resource_path('lang/es/' . $filename);
                }
                $es = is_array($es) ? $es : [];

                $es_keys = array_keys($es);
                $new = [];

                foreach ($en as $eng => $vl1) {
                    if (!in_array($eng, $es_keys)) {
                        $new[$eng] = $vl1;
                    }
                }
                if (empty($new)) {
                    $this->info("File >lang/es/$filename already updated");
                    continue;
                }

                $added = array_merge($new, $es);

                $data = "<?php \n return [ \n";

                foreach ($added as $key => $value) {
                    if (is_array($value)) {
                        $v = $this->transform_to_array($value);
                    } else {

                        $v = str_replace("'", "\'", $value);
                    }
                    $k = str_replace("'", "\'", $key);

                    if (is_array($value)) {
                        $data .= "\t'$k'" . "\t=>\t" . "$v" . "\n";
                    } else {
                        $data .= "\t'$k'" . "\t=>\t" . "'$v'" . ",\n";
                    }
                }
                $data .= "]; \n ?>";
                file_put_contents(resource_path('lang/es/' . $filename), $data);
                $this->info("File >lang/es/$filename was updated");
            }
        }
    }

    public function transform_to_array($array)
    {
        $b = "[ \n";
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $v = $this->transform_to_array($value);
            } else {

                $v = str_replace("'", "\'", $value);
            }
            $k = str_replace("'", "\'", $key);
            if (is_array($value)) {
                $b .= "\t\t'$k'" . "\t=>\t" . "$v" . "\n";
            } else {
                $b .= "\t\t'$k'" . "\t=>\t" . "'$v'" . ",\n";
            }
        }
        $b .= "\t],";

        return $b;
    }
}
