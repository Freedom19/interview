<?php

require __DIR__.'/../class_docsv.php';

class DocsvTest extends TestCase {

   public function testClassExists()
   {
      $project = new doCSV();
   }

   public function testDownloadCSV()
   {
      if(file_exists('batch_of_urls.csv')){
         unlink('batch_of_urls.csv');
      }
      //Catch Excetion when download fails   
      $project = new doCSV();
      $data = array (
         'url'   => '',
         'client'=> 123
      );
      try{
         $project->runJob(null,$data);
         if($result !== false){
            $this->assertTrue(file_exists('batch_of_urls.csv'));
         }
      }catch(Exception $ex){
         $this->assertNotEquals('Could not pretend to download file', $ex->getMessage());
      }
   }
}
