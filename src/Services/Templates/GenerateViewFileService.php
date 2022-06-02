<?php

namespace EmizorIpx\ClientFel\Services\Templates;

class GenerateViewFileService {

    public static function generateViewFile($html, $url, $updated_at)
    {
        // Get the Laravel Views path
            $path = \Config::get('view.paths.0');

            // Here we use the date for unique filename - This is the filename for the View
            $viewfilename = $url."-".hash('sha1', $updated_at . md5(rand(1, 1000)));

            // Full path with filename
            $fullfilename = $path."/pdf-designs/".$viewfilename.".blade.php";

            // Write the string into a file
            if (!file_exists($fullfilename))
            {
                file_put_contents($fullfilename, $html);
            }

            // Return the view filename - This could be directly used in View::make
            return $viewfilename;
    }

}