<?php

class doCSV {
   // Attempts of downloading the csv file
   private $maxDownloadTries = 10;
   // holds the current attempts to download the csv file
   private $currentTries = 0;
   //Numbers of rows to read before inserting them in database
   private $maxNumberOfRowsToRead = 500;
   private $secBeforeTryDownloadAgain = 3;

   /**
    * This is the job that will be launched by the Laravel queue...
    *
    * @param Illuminate\Queue\Jobs\Job $job class retrieved from Laravel's queue
    * @param array $data the data that is going to be used for this queue call.
    *
    */
   public function runJob($job, $data)
   {
      $downloadUrl = $data['url'];
      $clientID = (int) $data['client'];
      $file = null;
      $tryDownloadFile = true;
      $extractedFilePath = __DIR__.'/batch_of_urls.csv';

      while($tryDownloadFile){
         try{
            $file = $this->downloadCSV($downloadUrl);
            $tryDownloadFile = false; 
         }catch(Exception $ex){
            $this->currentTries++;
            if($this->currentTries >= $this->maxDownloadTries){
               $tryDownloadFile = false; 
            }else{
               sleep($this->secBeforeTryDownloadAgain);
            }
         }
      }
      $this->extractGzipFile($file, $extractedFilePath);
      $success = $this->insertCSVIntoDatabase($clientID, $extractedFilePath);
   }

   /**
    * 
    * Extract a gzip file 
    *
    * @param String $src Destination of gzip file
    * @param String $dst Path to output file
    * @param int    $chunkSize Size of chunks that get read every loop
    *
    */
   private function extractGzipFile($src, $dst, $chunkSize = 20000)
   {
      if($src === '' OR $src === NULL OR $dst === '' OR $dst === NULL)
         return false;

      $srcHandler = gzopen($src, "rb");
      $dstHandler = fopen($dst, "w");

      while (!gzeof($srcHandler)) {
         $string = gzread($srcHandler, $chunkSize);
         fwrite($dstHandler, $string, strlen($string));
      }
      gzclose($srcHandler);
      fclose($dstHandler);
   }


   /**
    * Here we have our CSV file that has been downloaded. We need to do the following:
    *
    *      * uncompress the gzip file
    *      * store that uncompressed file somewhere
    *      * go row by row and insert the data into a database.
    *
    *  Important notes:
    *
    *      * the gzip files could be 250MB big
    *      * uncompressed files more then 2GB big
    *      * we want to use as little RAM as possible ;)
    *      * we deploy code with a memory limit of 512MB ... not enough ram to
    *        store all of the CSV file or rows to insert in the database in one go.
    *
    *  Bonus notes:
    *
    *      * chunking inserts into the database would be nice
    *
    *      * str_getcsv has some bugs. as a hint, check out the PHP documentation,
    *        and read the comment from Ryan Rubley. These bugs will be hit with well-formed
    *        CSV files. The file provided will not expose the bugs.
    *
    *      * fopen, fseek and gzopen will be your friends.
    *
    * @param int $clientID the ID of the client that the CSV is for
    * @param string $file  the file that we are going to be importing.
    *
    */
   private function insertCSVIntoDatabase($clientID, $file)
   {
      $currentRowsRead = 0;
      $rows = array();
      $isFirstRow = true;
      if (($handle = fopen($file, "r")) !== FALSE) {
         while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            //Skip first row
            if($isFirstRow){
               $isFirstRow = false;            
               continue;
            }

            $currentRowsRead++;
            $tableRow = array(
               'clientId' => $clientID,
               'targetURL' => $data[0],
               'sourceURL' => $data[1],
               'anchorText' => $data[2],
               'sourceCrawlDate' => $data[3],
               'sourceFirstFoundDate' => $data[4],
               'flagNoFollow' => $data[5],
               'flagImageLink' => $data[6],
               'flagRedirect' => $data[7],
               'flagFrame' => $data[8],
               'flagOldCrawl' => $data[9],
               'flagAltText' => $data[10],
               'flagMention' => $data[11],
               'sourceCitationFlow' => $data[12],
               'sourceTrustFlow' => $data[13],
               'targetCitationFlow' => $data[14],
               'targetTrustFlow' => $data[15],
               'sourceTopicalTrustFlowTopic0' => $data[16],
               'sourceTopicalTrustFlowValue0' => $data[17],
               'refDomainTopicalTrustFlowTopic0' => $data[18],
               'refDomainTopicalTrustFlowValue0' => $data[19]
            );
            $rows[] = $tableRow;
            if($currentRowsRead >= $this->maxNumberOfRowsToRead){
               DB::table('crawler')->insert(
                  $rows
               );
               $currentRowsRead = 0;
               $rows = array();
            }
         }
         fclose($handle);
      } 
   }

   /**
    * This is a function that will download a file using curl over HTTP.
    *
    * For the moment, we will just return the path of the compressed file.
    * There is no need to download the CSV file at all \o/
    *
    * @param string $downloadUrl the URL to download the file from.
    *
    */
   private function downloadCSV($downloadUrl)
   {
      $test = rand(1, 6);
      echo $test;

      // We are going to throw a random exception sometimes...
      if ($test == 3)
      {
         throw new Exception("Could not pretend to download file");
      }

      $_file = __DIR__ . '/batch_of_urls.csv.gz';
      if (!file_exists($_file)) {
         throw new Exception("Oh noes, it looks like your CSV file is missing");
      }

      return $_file;
   }

}
