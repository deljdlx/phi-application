<?php

require(__DIR__.'/_autoload.php');



$application = new \Phi\Application\Application();



$module = new \Phi\Application\Module();
$module->getRouter()->get('test', 'controller.php', function(\Phi\Routing\Response $response) use ($application) {

    $controller = new \Phi\Application\Controller($application);
    $controller->setContent('Controller content');

    $response->setContent(
       $controller->getContent()
    );
});


$application->registerModule('test', $module);


$response = $application->run();

echo $response->getContent();





