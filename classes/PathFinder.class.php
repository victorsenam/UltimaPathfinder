<?php
/**
 * The PathFinder uses an Algorithm to find the shortest path between two points 
 * in a given map.
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
class PathFinder {
    /**
     * The given Map the PathFinder will play with.
     * @var Map
     */
    private $map = null;
    
    /**
     * The Algorithm 
     * @var Algorithm 
     */
    private $algorithm = null;
    
    //--------------------------------------------------------------------------
    //GETTERS & SETTERS
    /**
     * Sets the Algorithm the Player will use to find the shortest path between
     * to poins on the current map.
     * Note this is not the algorythm name but the Algorithm object itself!
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Algorithm $algorithm 
     */
    public function setAlgorithm($algorithm){
        $this->algorithm = $algorithm;
    }
    
    /**
     * 
     * @return Algorithm
     */
    public function getAlgorithm() {
        return $this->algorithm;
    }
    
    /**
     * Sets the Map where PathFinder will find the shortest path between two
     * points. Those points will be defiend in the Map itself.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Map $map 
     */
    public function setMap($map) {
        $map = 
        $this->map = $map;
    }

    /**
     *
     * @return Map
     */
    public function getMap() {
        return $this->map;
    }
    //--------------------------------------------------------------------------
        
    /**
     * Constructs PathFinder with optional setting of Map and Algorithms.
     * They can be set later as well using the corresponding setters.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public function __construct($map) {
        $this->loadMap($map);
        $this->loadAlgorithm("Astar");
    }
    
    /**
     * Loads a Map from file.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param string $mapName 
     */
    public function loadMap($mapName){
        $this->setMap(new Map($mapName));
    }
    
    /**
     * Loads Algorithm by name.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param string $algorithmName 
     */
    public function loadAlgorithm($algorithmName){
        $algorithmsDir = Config::$algorithmsDir;
        $algorithmSufix = Config::$algorithmSufix;
        $algorithmFile = "$algorithmsDir/$algorithmName.$algorithmSufix";
        if(!file_exists($algorithmFile)) die("Oops! ALGORITHM NOT EXISTS '$algorithmFile'\n");
        
        require_once $algorithmFile;
        if(!class_exists($algorithmName)) die("Oops! Algorithm class should be named as '$algorithmName'");
        $this->algorithm = new $algorithmName($this->map);
    }
    
    /**
     * Uses choosen Algorithm to find the shortest path between the two points.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return Path
     */
    public function find(){
        $s = microtime(true);
        $path = $this->getAlgorithm()->find();
        $e = microtime(true);

        return [
            'time' => ($e - $s),
            'path' => ($path === null ? false : $this->getCoords($path)),
            'map' => ($path === null ? false : $this->draw($path))
        ];
    }
    
    /**
     * Draws given Path on current Map.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Path $path
     */
    public function getCoords(Path $path){
        $directionRepresentation = array(
            'up' => 0,
            'down' => 4,
            'left' => 6,
            'right' => 2,
            'upleft' => 7,
            'upright' => 1,
            'downright' => 3,
            'downleft' => 5
        );
        
        $from = $this->map->source->getKey();
        $to = $this->map->destination->getKey();
        $mapName = $this->map->mapName;
        
        //draw path on map
        $returnData = [];
        for($y = 1; $y <= $this->map->height; $y++){
            for($x = 1; $x <= $this->map->width; $x++){
                $node = $this->map->getNode($x, $y);
                $nodeRepresentation = Config::getNodeRepresentation($node->type);
                if(
                    array_key_exists($node->getKey(), $path->nodes) && 
                    $node->type != NodeType::SOURCE &&
                    $node->type != NodeType::DESTINATION
                ){
                    $node = $path->nodes[$node->getKey()];
                    $nodeRepresentation = $node->parentDirection;
                }

                if (isset($directionRepresentation[$nodeRepresentation]))
                    $returnData[] = $directionRepresentation[$nodeRepresentation];
            }
        }
        // exit();

        return array_reverse($returnData);
    }

     public function draw(Path $path){
        $from = $this->map->source->getKey();
        $to = $this->map->destination->getKey();
        $mapName = $this->map->mapName;
        
        //draw path on map
        $returnData = "";
        for($y = 1; $y <= $this->map->height; $y++){
            for($x = 1; $x <= $this->map->width; $x++){
                $node = $this->map->getNode($x, $y);
                $nodeRepresentation = Config::getNodeRepresentation($node->type);
                if(
                    array_key_exists($node->getKey(), $path->nodes) && 
                    $node->type != NodeType::SOURCE &&
                    $node->type != NodeType::DESTINATION
                ){
                    $node = $path->nodes[$node->getKey()];
                    $nodeRepresentation = Config::getDirectionRepresentation($node->parentDirection);
                }
                $returnData .= "$nodeRepresentation";
            }
            $returnData .= "\n";
        }

        return $returnData;
    }
}