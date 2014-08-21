<?php

require __DIR__.'/../class_docsv.php';

class DocsvTest extends TestCase {

   public function testClassExists()
   {
      $project = new doCSV();
   }

   public function testDownloadCSV()
   {
      //Catch Excetion when download fails   
      $project = new doCSV();
      $data = array (
         'url'   => '',
         'client'=> 123
      );
      try{
         $project->runJob(null,$data);
         $project->runJob(null,$data);
         $project->runJob(null,$data);

      }catch(Exception $ex){
         $this->assertNotEquals('Could not pretend to download file', $ex->getMessage());
      }
   }

   public function testExtractGzip()
   {
      if(file_exists('batch_of_urls.csv')){
         unlink('batch_of_urls.csv');
      }

      $project = new doCSV();
      $data = array (
         'url'   => '',
         'client'=> 123
      );
      $result = $project->runJob(null,$data);
      if($result !== false){
         $this->assertTrue(file_exists('batch_of_urls.csv'));
      }
   }
}
