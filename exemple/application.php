<?php

require(__DIR__.'/_autoload.php');


echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r('Construct application');
echo '</pre>';

$application = new \Phi\Application\Application();


echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r('Application filepath root : '.$application->getFilepathRoot());
echo '</pre>';



$application->getRouter()->get('test', true, function(\Phi\Routing\Response $response) {
    $response->setContent('hello world');
})->html();


$response = $application->run(
    new \Phi\Routing\Request()
);

$response->sendHeaders();
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($response->getContent());
echo '</pre>';

