<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Places;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Predis\Client;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("default/index.html.twig")
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/api/v1/sectors.json", name="get_sectors")
     *
     * @return JsonResponse
     */
    public function getSectorsAction()
    {
        return new JsonResponse([
            'sectors' => $this->getSectors()
        ]);
    }

    /**
     * @Route(
     *     "/api/v1/reservations/{sectorId}/{rowId}/{placeId}.json",
     *      name="reservations",
     *     requirements={"sectorId": "\d+", "rowId": "\d+", "placeId": "\d+"}
     * )
     *
     * @param int $sectorId
     * @param int $rowId
     * @param int $placeId
     *
     * @return JsonResponse
     */
    public function reservationsAction($sectorId, $rowId, $placeId)
    {
        /** @var Client $redis */
        $redis = $this->container->get('snc_redis.sector');
        $sector = unserialize($redis->get($sectorId));
        if (!$sector['rows'][$rowId][$placeId]) {
            ++$sector['countBooking'];
            --$sector['countFree'];
            $sector['rows'][$rowId][$placeId] = true;
            $redis->set($sectorId, serialize($sector));
        }

        return new JsonResponse(['message' => 'Ok']);
    }


    /**
     * @return array
     * @todo move to service
     */
    private function getSectors()
    {
        /**  @todo it can be transferred to the database */
        $sectors = [
            1 => [
                'countBooking' => 0,
                'countFree' => 0,
                'countRow' => 10,
                'countPlaceInRow' => 10,
                'rows' => []
            ],
            2 => [
                'countBooking' => 0,
                'countFree' => 0,
                'countRow' => 20,
                'countPlaceInRow' => 11,
                'rows' => []
            ],
            3 => [
                'countBooking' => 0,
                'countFree' => 0,
                'countRow' => 50,
                'countPlaceInRow' => 30,
                'rows' => []
            ],
            4 => [
                'countBooking' => 0,
                'countFree' => 0,
                'countRow' => 30,
                'countPlaceInRow' => 30,
                'rows' => []
            ],
        ];
        /** @var Client $redis */
        $redis = $this->container->get('snc_redis.sector');
        $em = $this->getDoctrine()->getManager();

        foreach ($sectors as $sectorKey => $sector) {
            try {
                if ($redis->exists($sectorKey)) {
                    $sectors[$sectorKey] = unserialize($redis->get($sectorKey));
                }
            } catch (\Exception $ex) {
                $redis->del($sectorKey);
                $sectors[$sectorKey] = $sector;
            }

            if (!$redis->exists($sectorKey)) {
                $sectors[$sectorKey]['countFree'] = $sectors[$sectorKey]['countRow']*$sectors[$sectorKey]['countPlaceInRow'];
                $placesBooking = $em->getRepository(Places::class)->findBy(['sector' => $sectorKey]);
                $sectors[$sectorKey]['countBooking'] = count($placesBooking);
                $sectors[$sectorKey]['countFree'] -= $sectors[$sectorKey]['countBooking'];

                for ($row = 1;$row<=$sectors[$sectorKey]['countRow'];$row++) {
                    $sectors[$sectorKey]['rows'][$row] = [];
                    for ($place = 1;$place<=$sectors[$sectorKey]['countPlaceInRow'];$place++) {
                        $sectors[$sectorKey]['rows'][$row][$place] = false;
                    }
                }
                foreach ($placesBooking as $item) {
                    $sectors[$item->getSector()]['rows'][$item->getRow()][$item->getPlace()] = true;
                    ++$sectors[$item->getSector()]['countBooking'];
                    --$sectors[$item->getSector()]['countFree'];
                }
                $redis->append($sectorKey, serialize($sectors[$sectorKey]));
            }
        }

        return $sectors;
    }
}
