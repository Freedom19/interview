<?php 

require 'load_laravel.php';

Schema::create('crawler', function($table)
   {
      $table->increments('id');
      $table->bigInteger('clientId');
      $table->string('targetURL');
      $table->string('sourceURL');
      $table->string('anchorText')->nullable();
      $table->timestamp('sourceCrawlDate');
      $table->timestamp('sourceFirstFoundDate');
      $table->char('flagNoFollow')->default(0);
      $table->char('flagImageLink')->default(0);
      $table->char('flagRedirect')->default(0);
      $table->char('flagFrame')->default(0);
      $table->char('flagOldCrawl')->default(0);
      $table->char('flagAltText')->default(0);
      $table->char('flagMention')->default(0);
      $table->string('sourceCitationFlow')->nullable();
      $table->string('sourceTrustFlow')->nullable();
      $table->string('targetCitationFlow')->nullable();
      $table->string('targetTrustFlow')->nullable();
      $table->string('sourceTopicalTrustFlowTopic0')->nullable();
      $table->string('sourceTopicalTrustFlowValue0')->nullable();
      $table->string('refDomainTopicalTrustFlowTopic0')->nullable();
      $table->string('refDomainTopicalTrustFlowValue0')->nullable();
   });
