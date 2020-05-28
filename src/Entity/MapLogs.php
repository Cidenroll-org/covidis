<?php

namespace App\Entity;

use App\Repository\MapLogsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MapLogsRepository::class)
 */
class MapLogs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $redisKey;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRedisKey(): ?string
    {
        return $this->redisKey;
    }

    public function setRedisKey(string $redisKey): self
    {
        $this->redisKey = $redisKey;

        return $this;
    }
}
