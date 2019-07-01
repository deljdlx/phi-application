<?php

require(__DIR__.'/_autoload.php');



$application = new \Phi\Application\Application();


$module = new \Phi\Application\Module();
$module->getRouter()->get('test', 'subrouting.php', 'module ok');


try {
    $application->registerModule('test', $module, 'exemplee');
    $response = $application->run();
} catch(\Phi\Core\Exception $exception) {
    echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
    echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
    print_r('Module validator failed, as expected');
    echo '</pre>';
}



$module = new \Phi\Application\Module();
$module->getRouter()->get('test', 'subrouting.php', 'module ok');


try {
    $application->registerModule('test', $module, 'exemple');
    $response = $application->run();
    echo $response->getContent();
} catch(\Phi\Core\Exception $exception) {
    echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
    echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
    print_r('Module run ok (validator "exemple" in the URI');
    echo '</pre>';
}







