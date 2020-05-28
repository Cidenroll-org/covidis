<?php


namespace App\Service;


class LocationMatrixService
{
    private $map;

    public function __construct(int $mapLimit, $mapPixel = 'x')
    {
        if ($mapLimit > 1) {
            for ($i=0;$i<$mapLimit;$i++) {
                for ($j=0;$j<$mapLimit; $j++) {
                    $this->map[$i][$j] = $mapPixel;
                }
            }
        }
        else {
            throw new \RuntimeException("You must input a value greater than 1 for it to form a matrix map.");
        }

    }

    public function getMap()
    {
        return $this->map;
    }

    public function setMap($map): void
    {
        $this->map = $map;
    }

    public function printMap()
    {
        for($i=0, $iMax = count($this->map); $i < $iMax; $i++) {
            for ($j=0, $jMax = count($this->map); $j< $jMax; $j++) {
                echo $this->map[$i][$j]." ";
            }
            echo "<br />";
        }
    }


}