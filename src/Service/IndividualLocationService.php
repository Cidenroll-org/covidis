<?php


namespace App\Service;


class IndividualLocationService
{
    private $map;

    private $startLat = 0;

    private $startLong = 0;

    private $finalLat = 0;

    private $finalLong = 0;
    private $numberSteps;
    private $identifier;

    private $stepHistory = [];
    private $timeStart;

    /**
     * IndividualLocationService constructor.
     * @param $map
     * @param int $startLat
     * @param int $startLong
     * @param $numberSteps
     * @param $identifier
     * @param $timeStart
     */
    public function __construct($map, $startLat=0, $startLong=0, $numberSteps, $identifier, $timeStart)
    {
        $this->map = $map;
        try {
            $this->startLat = $startLat ?? random_int(0, count($this->map));
            $this->startLong = $startLong ?? random_int(0, count($this->map));
        } catch (\Exception $e) {
        }

        $this->numberSteps = $numberSteps;
        $this->identifier = $identifier;
        $this->timeStart = $timeStart;

        $this->buildPath();
    }

    private function buildPath(): void
    {

       $lat =  $this->startLat;
       $lon = $this->startLong;
       $currentPosition = ['lat' => $lat, 'lon' => $lon, 'reportedAt' => time()];
       $this->stepHistory[] = $currentPosition;
       $map = $this->map;

       $noSteps = $this->numberSteps;
       while ($noSteps >0) {
           $starttime = microtime(true);

           $currentLat = $currentPosition['lat'];
           $currentLon = $currentPosition['lon'];

           $up = $currentLat-1;
           $down = $currentLat+1;
           $left = $currentLon-1;
           $right = $currentLon+1;

           $directionsPosibilities = ["up"=>"","down"=>'',"left"=>'',"right"=>''];

           if (isset($map[$up][$currentLon])) {
               $directionsPosibilities["up"] = 1;
           }
           if (isset($map[$down][$currentLon])) {
               $directionsPosibilities["down"] = 1;
           }
           if (isset($map[$currentLat][$left])) {
               $directionsPosibilities["left"] = 1;
           }
           if (isset($map[$currentLat][$right])) {
               $directionsPosibilities["right"] = 1;
           }

           $directionsPosibilities = array_filter($directionsPosibilities);
           $selectedDirection = array_rand($directionsPosibilities, 1);

           $diff = microtime(true) - $starttime;
           $this->timeStart += $diff;

           switch ($selectedDirection) {
               case 'up':
                   $currentPosition = ['lat' => $currentLat-1,
                       'lon' => $currentLon,
                       'reportedAt' =>  time()];
                   $noSteps--;
                   break;
               case 'down':
                   $currentPosition = ['lat' => $currentLat+1,
                       'lon' => $currentLon,
                       'reportedAt' =>  time()];
                   $noSteps--;
                   break;
               case 'left':
                   $currentPosition = ['lat' => $currentLat,
                       'lon' => $currentLon-1,
                       'reportedAt' =>  time()];
                   $noSteps--;
                   break;
               case 'right':
                   $currentPosition = ['lat' => $currentLat,
                       'lon' => $currentLon+1,
                       'reportedAt' =>  time()];
                   $noSteps--;
                   break;
           }
           $this->stepHistory[] = $currentPosition;

       }
        $this->finalLat = end($this->stepHistory)['lat'];
        $this->finalLong = end($this->stepHistory)['lon'];
    }

    /**
     * @return array
     */
    public function getStepHistory(): array
    {
        return $this->stepHistory;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return int
     */
    public function getFinalLat(): int
    {
        return $this->finalLat;
    }

    /**
     * @param int $finalLat
     */
    public function setFinalLat(int $finalLat): void
    {
        $this->finalLat = $finalLat;
    }

    /**
     * @return int
     */
    public function getFinalLong(): int
    {
        return $this->finalLong;
    }

    /**
     * @param int $finalLong
     */
    public function setFinalLong(int $finalLong): void
    {
        $this->finalLong = $finalLong;
    }


}