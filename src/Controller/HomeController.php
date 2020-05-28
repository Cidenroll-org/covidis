<?php

namespace App\Controller;

use App\Entity\MapLogs;
use App\Repository\MapLogsRepository;
use App\Service\IndividualLocationService;
use App\Service\IntersectionService;
use App\Service\LocationMatrixService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Routing\Annotation\Route;
use DateInterval;

class HomeController extends AbstractController
{
    /**
     * @var AdapterInterface
     */
    private $cache;
    /**
     * @var LocationMatrixService
     */
    private $locationMatrix;

    /**
     * @var array
     */
    private $stepHistory=[];
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(AdapterInterface $cache, EntityManagerInterface $entityManager)
    {
        $this->cache = $cache;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="home")
     * @throws \Doctrine\ORM\ORMException
     */
    public function index()
    {
        /** @var MapLogsRepository $mapLogsRepo */
        $mapLogsRepo = $this->getDoctrine()->getRepository(MapLogs::class);

        $this->locationMatrix = new LocationMatrixService(10, "0");
        $microTime = microtime(true);

        $indiv1 = 'I'.uniqid(true, true);
        $indiv2 = 'I'.uniqid(true, true);

        $item1 = $this->cache->getItem($indiv1);
        $item2 = $this->cache->getItem($indiv2);


        if (!$item1->isHit()) {
            $indiv1 = new IndividualLocationService($this->locationMatrix->getMap(), null,null, 150, $indiv1, $microTime);
            $item1->set($indiv1);
            $this->cache->save($item1);

            $mapLog = new MapLogs();
            $mapLog->setRedisKey($indiv1->getIdentifier());

            $this->entityManager->persist($mapLog);
            $this->entityManager->flush();
        }

        if (!$item2->isHit()) {
            $indiv2 = new IndividualLocationService($this->locationMatrix->getMap(), null,null, 550, $indiv2, $microTime);
            $item2->set($indiv2);
            $this->cache->save($item2);

            $mapLog = new MapLogs();
            $mapLog->setRedisKey($indiv2->getIdentifier());
            $this->entityManager->persist($mapLog);
            $this->entityManager->flush();
        }

        $intersection = new IntersectionService();
        $mapLogs = $mapLogsRepo->findAll();
        /** @var MapLogs $mapLog */
        foreach ($mapLogs as $mapLog) {

            /** @var CacheItem $cacheItem */
            $cacheItem = $this->cache->getItem($mapLog->getRedisKey());

            /** @var IndividualLocationService $locationObj */
            if ($locationObj = $cacheItem->get()) {
                $intersection->addToIndividualList($locationObj);
            }
        }

        $userIntersections = $intersection->getIntersections();

        return $this->render('home/index.html.twig', [
            'intersectData' => $intersection,
            'userIntersections' => $userIntersections
            ]);
    }
}