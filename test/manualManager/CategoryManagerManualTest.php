<?php

require_once "../../config/config.php";

spl_autoload_register(
    function ($className) {
        require "../../model/" . $className . ".php";
    }
);

$DB = MyPDO::getInstance(DB_TYPE . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD, ENV_DEV);

$categoryManager = new CategoryManager($DB);

$outputs = $categoryManager->selectAll();



if(empty($outputs)){
    echo "<h1>No data!!</h1>";
}else{
    foreach ($outputs as $output){

        $category = new Category($output);
        echo "<p>{$category->getIdCategory()}</p>";
        echo "<p>{$category->getNameCategory()}</p>";
    }
}
