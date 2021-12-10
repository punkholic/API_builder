
<?php
/**
 * Class: Application Builder
 * Description: Build the complete Project
 * @Author : Samyam Kafle / Niraj Ghimire 
 * Copyright: 2021
 */
class Zip_builder extends Builder
{
    protected $_config;

    /**
     * Constructor
     *
     * @param [type] $config
     * @param [type] $mapping
     */
    public function __construct( $config )
    {
        $this->_config = $config;

    }

    public function create_zip_archieve() {
        // First getting into the folder where we save the zip 
        chdir( "../" );

        $root_dir = getcwd();

        // checking for project folders 
        $main_folder_path = $root_dir . "/PastProjects";
        if( !dir( $main_folder_path ) )
        {
            mkdir( $main_folder_path );
        }
        $language = $this->_config['config']['programming-language'];
        $app_name = $this->_config['config']['app_name'];
        $languageUcase = ucfirst( $language );
        $sub_path = $main_folder_path . "/" . $languageUcase;

        if( !dir( $sub_path ) )
        {
            mkdir( $sub_path );
        }


        // Get real path of release
        $rootPath = $root_dir . "/release";
      
        // Initialize archive object
        $zip = new ZipArchive();
        $file_name = $app_name . time();
        
        $zip->open( $sub_path . "/" . $file_name . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
      
        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
        return $file_name;
    }

    public function download_zip( $project_id ) {
        $language = $this->_config['config']['programming-language'];
        $language_ucase = ucfirst( $language );
       
        $path = getcwd() . "/PastProjects/" . $language_ucase;
        
        $filename = $path . "/" . $project_id . ".zip";

        if (file_exists($filename)) {
           header('Content-Type: application/zip');
           header('Content-Disposition: attachment; filename="'.basename($filename).'"');
           header('Content-Length: ' . filesize($filename));
           flush();
        }
    }


}
