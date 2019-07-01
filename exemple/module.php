<?php

require(__DIR__.'/_autoload.php');



$application = new \Phi\Application\Application();


$module = new \Phi\Application\Module();
$module->getRouter()->get('test', 'module.php', 'module ok');

$application->registerModule('test', $module);


$response = $application->run();

echo $response->getContent();





