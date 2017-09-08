<?php

$contents = file_get_contents(__DIR__."/../tests/FstoreTest.php");
$contents = str_replace("__DIR__","'".realpath(__DIR__.'/../tests/')."'",$contents);
file_put_contents("tmp.php",$contents);

$cmds[] = "textract '".__DIR__."/../tests/FstoreTest.php' > README.mdx";
$cmds[] = "mdx README.mdx tmp.php > '".__DIR__."/../README.md'";
$cmds[] = "rm README.mdx && rm tmp.php";

foreach($cmds as $cmd){
    echo $cmd."\n";
}