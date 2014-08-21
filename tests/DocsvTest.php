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
         $project->runJob(null,$data);
         $project->runJob(null,$data);
         $project->runJob(null,$data);
         $project->runJob(null,$data);
         $project->runJob(null,$data);
         $project->runJob(null,$data);
      
      }catch(Exception $ex){
         $this->assertNotEquals('Could not pretend to download file', $ex->getMessage());
      }
   }
}
