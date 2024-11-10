<?php

$shortopts = "a:f:";
$longopts  = array(
    "action:",
    "file:",
);

$options = getopt($shortopts, $longopts);

if(isset($options['a'])) {
    $action = $options['a'];
} elseif(isset($options['action'])) {
    $action = $options['action'];
} else {
    $action = "xyz";
}

if(isset($options['f'])) {
    $file = $options['f'];
} elseif(isset($options['file'])) {
    $file = $options['file'];
} else {
    $file = "notexists.csv";
}

try {
    if ($action == "plus") {
        include 'Classes/Addition.php';
        $classOne = new Addition($file);
    } elseif ($action == "minus") {
        include 'Classes/Subtraction.php';
        $classTwo = new Subtraction($file, "minus");
        $classTwo->start();
    } elseif ($action == "multiply") {
        include 'Classes/Multiplication.php';
        $classThree = new Multiplication();
        $classThree->setFile($file);
        $classThree->execute();
    } elseif ($action == "division") {
        include 'Classes/Division.php';
        $classFouyr = new Division($file);
    } else {
        throw new \Exception("Wrong action is selected");
    }
} catch (\Exception $exception) {}