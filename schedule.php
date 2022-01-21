<?php
require_once __DIR__.'/vendor/autoload.php';

use GO\Scheduler;

// Create a new scheduler
$scheduler = new Scheduler();

// ... configure the scheduled jobs (see below) ...
$scheduler->php('generate_roster.php')->daily(00, 00);

// Let the scheduler execute jobs which are due.
$scheduler->run();