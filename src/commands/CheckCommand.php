<?php
/**
 *
 */

namespace SouthernIns\EnvManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SouthernIns\EnvManager\Shell\Diff;


/**
 * Class CheckCommand
 * @package SouthernIns\EnvManager\Commands
 */
class CheckCommand extends Command {

    /*
     * Trait with common properties
     */
    use EnvFiles;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:check {file?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check local Environment File(s) against source.';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {

        parent::__construct();

        $this->initConfig();

    } // -END __construct


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        // Hoping with file* that even a single option passed in
        // comes as an array with one value for looping purposes
        $files = $this->argument( 'file' ) ?? $this->all;

        $callback = [ $this, 'checkFile' ];
        $this->processFiles(  $callback, $files );

    } // END function handle()


    public function checkFile( $sourcePath, $localPath, $s3, $disk ){

//        $this->info( $sourcePath );
//        $this->info( $localPath );


        if( !$disk->has( $localPath )){
            $this->error( "File: " . $localPath . " could not be found" );
            return;
        }

        if( !$s3->has( $sourcePath )){
            $this->error( "No Remote file matching " . $localPath . " exists for comparison." );
            return;
        }

        $source = $s3->get( $sourcePath );

        $sourceHash = sha1( $source ) ;

        $localHash = sha1( $disk->get( $localPath) );


//        $this->info($sourceHash );
//        $this->info($localHash );

        if( $sourceHash != $localHash ){
            $this->error( "There are ENV Differences that need to be addressed in: " . $localPath );

            $tmpFile = tmpfile();

            $disk->put( $tmpFile, $source );

            Diff::files( $localPath, stream_get_meta_data($tmpFile)['uri'] );

        }


//            $s3->put( $sourcePath, $fileContent);

    }


} //- END class CheckCommand{}
