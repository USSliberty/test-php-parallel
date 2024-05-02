<?php
use parallel\Runtime;
use parallel\Channel;

$test = "this var is not accesible in a thread";

// this function will be the threads
$thread_function = function (int $id, Channel $ch) {
    echo "Inizio".$id;
    $filename = 'test.jpg';
    $percent = 0.5;
    
    list($width, $height) = getimagesize($filename);
    $newwidth = $width * $percent;
    $newheight = $height * $percent;
    sleep(5);
    // Load
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    $source = imagecreatefromjpeg($filename);
    // Resize
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    // Output
    imagejpeg($thumb, "./images/".$id.$filename);
    
    // the only way to share data is between channels
    $ch->send("Finito: ".$id);
};

try {
    $t = 30;
    $a = array();
    // channel where the date will be sharead
    $ch = new Channel();
    for ($i = 0; $i<$t; $i ++) {
        $r = new Runtime();
        $r->run($thread_function, [$i, $ch]);
        array_push($a, $r);
    }
    echo "Finito run";
    array_map(function ($r) use ($ch) {
        $x = $ch->recv();
        echo $x;  
    }, $a);

    $ch->close();

} catch (Error $err) {
    echo "\nError:", $err->getMessage();
} catch (Exception $e) {
    echo "\nException:", $e->getMessage();
}