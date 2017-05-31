<?php
/*
 * @author Alberto Miranda <alberto.php@gmail.com>
 */

$classSufix = 'class.php';
$classDir = dirname(__FILE__) . '/classes';
$dirContent = scandir($classDir); //using scandir to get files and dirs ordered alphabethically
foreach($dirContent as $file){
    //skip unwanted files and dirs
    $omit = array('.', '..', '.svn');
    if(in_array($file, $omit) or is_dir($file)) continue;

    preg_match("/^(.*?)\.$classSufix/", $file, $matches);
    if(empty($matches)) continue; //do nothing with non matching files
    $key = $matches[1];
    
    $classFile = "$classDir/$file";
    require_once $classFile;
}

require_once dirname(__FILE__) . '/algorithms/Algorithm.interface.php';

/* Run */
// $map = "....|...
// .|..|.1.
// .|0.|...
// .|.||...
// .|||....
// ........";

// $map = "|1.....||
// ||||.||||
// |..|...||
// |..|||.||
// |...0|.||
// |....|.||
// |......||
// |||||||||
// .........";

// $map ="1........
// .........
// .........
// .........
// ....0....
// .........
// .........
// .........
// .........";

$map =".|1......
.|||||||.
.........
.........
....0....
.........
.........
.........
.........";

// $map ="..1.....|
// .||||||||
// .||.....|
// .||.|||.|
// .....||.|
// |||||||.|
// ...||||.|
// .|.||||.|
// 0|......|";

$pathFinder = new PathFinder($map);
$mapRepresentation = $pathFinder->getMap()->getRepresentation();
$mapName = $pathFinder->getMap()->mapName;
$path = $pathFinder->find();
// exit();
echo "\n\nMapa:\n$map\n\n";
echo "Caminho: \n" . (!$path['map'] ? "IMPOSSIVEL" : $path['map'])."\n\n";
echo "Tempo de processamento: " . $path['time'] . "s\n\n";

print_r($path['path']);