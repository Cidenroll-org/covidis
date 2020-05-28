<?php


namespace App\Service;


class IntersectionService
{

    private $individualKeys = [];

    private $individualList = [];


    /**
     * @param IndividualLocationService $individualLocation
     */
    public function addToIndividualList(IndividualLocationService $individualLocation): IntersectionService
    {
        $stepHistory =  $individualLocation->getStepHistory();
        foreach ($stepHistory as $step) {
            $this->individualList[] = sprintf("%s-%s-%s-%s",
                $individualLocation->getIdentifier(),
                $step['lat'],
                $step['lon'],
                $step['reportedAt']
            );
        }

        $this->individualKeys[] = $individualLocation->getIdentifier();

        return $this;
    }

    /**
     * @return array
     */
    public function getIndividualList(): array
    {
        return $this->individualList;
    }

    /**
     * @param array $individualList
     */
    public function setIndividualList(array $individualList): void
    {
        $this->individualList = $individualList;
    }

    public function getIntersections()
    {
        $intersections = [];
        if (count($this->individualList)<2) {
            throw new \Exception("There cannot be an intersection if there are no 2 or more individual history tracks added.");
        }

        $matchIntersections = [];

        for ($i=0; $i < count($this->individualList); $i++) {

            $step = $this->individualList[$i];
            $infoArr = explode("-", $step);
            $individual = $infoArr[0];

            $pattern = implode("-",[$infoArr[1], $infoArr[2], $infoArr[3]]);

            for ($j=$i+1; $j< count($this->individualList)-1; $j++) {
                if (strpos($this->individualList[$j], $individual) !== false) {
                    continue;
                }

                preg_match("/^.*$pattern$/", $this->individualList[$j], $matches);
                if ($matches) {
                    $matchArr = explode("-", $matches[0]);
                    $matchIntersections[$individual][$matchArr[0]][] = $matchArr[1]."///".
                        $matchArr[2]."///".\DateTime::createFromFormat('U', $matchArr[3])->format('Y-m-d H:i:s');
                }
            }
        }

        $uniqueArr = [];
        foreach ($matchIntersections as $individual => $indivDetails) {
            foreach ($indivDetails as $indv => $oneIndv) {
                $uniqueArr[$individual][$indv] =array_unique($oneIndv);
            }
        }

        $finalList = [];
        foreach ($uniqueArr as $individual => $indivDetails) {
            foreach ($indivDetails as $indv => $oneIndv) {
                foreach ($oneIndv as $info) {
                    $explodedArr = explode("///", $info);
                    $finalList[$individual][$indv][] = [
                        'lat' => $explodedArr[0],
                        'lon' => $explodedArr[1],
                        'at' => $explodedArr[2]
                    ];
                }
            }
        }

        return $finalList;
    }

    /**
     * @return array
     */
    public function getIndividualKeys(): array
    {
        return $this->individualKeys;
    }


}