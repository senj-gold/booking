<?php

namespace AppBundle\Command;

use AppBundle\Entity\Places;
use Doctrine\ORM\EntityManager;
use Predis\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SectorRedisToDBCommand.
 */
class SectorRedisToDBCommand extends ContainerAwareCommand
{
    const NAME = 'sector:redis:to:db';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Client
     */
    private $redis;

    /**
     * @param EntityManager $em
     * @param Client        $redis
     */
    public function __construct(EntityManager $em, Client $redis)
    {
        parent::__construct();

        $this->em = $em;
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Update places')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sectorIds = $this->redis->keys('*');
        foreach ($sectorIds as $sectorId) {
            $this->processPlaces($sectorId);
        }
        $this->em->flush();
    }

    /**
     * @param string $class
     * @param array  $findBy
     *
     * @return null|object
     */
    private function findEntity($class, array $findBy)
    {
        return $this->em->getRepository($class)->findOneBy($findBy);
    }

    /**
     * @param int $sectorId
     * @todo move to service
     */
    private function processPlaces($sectorId)
    {
        try {
            $sector = unserialize($this->redis->get($sectorId));
            foreach ($sector['rows'] as $rowId => $row) {
                foreach ($row as $placeId => $place) {
                    if ($place) {
                        $this->savePlace($sectorId, $rowId, $placeId);
                    }
                }
            }
        } catch (\Exception $ex) {
            $this->redis->del($sectorId);
        }
    }

    /**
     * @param int $sectorId
     * @param int $rowId
     * @param int $placeId
     */
    protected function savePlace($sectorId, $rowId, $placeId)
    {
        /** @var Places $item */
        $item = $this->findEntity(Places::class, [
            'sector' => $sectorId,
            'row' => $rowId,
            'place' => $placeId
        ]);
        if (!$item) {
            $item = new Places();
            $item
                ->setSector($sectorId)
                ->setRow($rowId)
                ->setPlace($placeId);
            $this->em->persist($item);
        }
    }
}
