<?php

require __DIR__.'/../class_docsv.php';

class DocsvTest extends TestCase {

   public function testClassExists()
   {
      $project = new doCSV();
   }

   public function testRunJob()
   {
      if(file_exists('batch_of_urls.csv')){
         unlink('batch_of_urls.csv');
      }

      DB::table('crawler')->truncate();
      //Catch Excetion when download fails   
      $project = new doCSV();
      $data = array (
         'url'   => '',
         'client'=> 123
      );
      try{
         $project->runJob(null,$data);
         $this->assertTrue(file_exists('batch_of_urls.csv'));
         $this->assertTrue(0 < DB::table('crawler')->count());
      }catch(Exception $ex){
         $this->assertNotEquals('Could not pretend to download file', $ex->getMessage());
      }
   }
}
