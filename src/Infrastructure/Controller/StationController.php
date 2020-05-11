<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Controller;

use DateTime;
use NsReisplanner\Application\ReadModel\Repository\ArrivalRepository;
use NsReisplanner\Application\ReadModel\Repository\DepartureRepository;
use NsReisplanner\Application\ReadModel\Repository\StationDisruptionRepository;
use NsReisplanner\Application\ReadModel\Repository\StationRepository;
use NsReisplanner\Domain\DateRange;
use NsReisplanner\Infrastructure\Form\StationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StationController extends AbstractController
{
    private ArrivalRepository $arrivalsRepository;
    private DepartureRepository $departuresRepository;
    private StationRepository $stationRepository;
    private StationDisruptionRepository $stationDisruptionRepository;
    private string $defaultStation;

    public function __construct(
        ArrivalRepository $arrivalsRepository,
        DepartureRepository $departuresRepository,
        StationRepository $stationRepository,
        StationDisruptionRepository $stationDisruptionRepository,
        string $defaultStation
    ) {
        $this->arrivalsRepository = $arrivalsRepository;
        $this->departuresRepository = $departuresRepository;
        $this->stationRepository = $stationRepository;
        $this->stationDisruptionRepository = $stationDisruptionRepository;
        $this->defaultStation = $defaultStation;
    }

    public function arrivalsAndDepartures(Request $request): Response
    {
        $stationCode = $request->request->get('station_code', $this->defaultStation);

        $form = $this->createForm(StationType::class, ['station_code' => $stationCode]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $stationCode = $data['station_code'];
        }

        $dateRange = new DateRange(new DateTime('now'), new DateTime('+1 hour'));

        $station = $this->stationRepository->getByCode($stationCode);
        $arrivals = $this->arrivalsRepository->getByStationCodeForDateRange(
            $stationCode,
            new DateRange(new DateTime('now'), new DateTime('+1 hour'))
        );

        $departures = $this->departuresRepository->getByStationCodeForDateRange($stationCode, $dateRange);
        $stationDisruptions = $this->stationDisruptionRepository->findActiveByStationCode($stationCode);

        return $this->render(
            'arrivals_and_departures.html.twig',
            [
                'form' => $form->createView(),
                'station' => $station,
                'arrivals' => $arrivals,
                'departures' => $departures,
                'disruptions' => $stationDisruptions,
            ]
        );
    }
}
