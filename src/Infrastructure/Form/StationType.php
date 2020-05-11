<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Form;

use NsReisplanner\Application\ReadModel\Repository\StationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class StationType extends AbstractType
{
    private StationRepository $stationRepository;

    public function __construct(StationRepository $stationRepository)
    {
        $this->stationRepository = $stationRepository;
    }

    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('station_code', ChoiceType::class, [
            'required' => true,
            'multiple' => false,
            'expanded' => false,
            'label' => 'Select station',
            'choices' => $this->createOptionsForStations(),
        ]);
    }

    /**
     * @return string[]
     */
    private function createOptionsForStations(): array
    {
        $stations = $this->stationRepository->findAll();

        $options = [];
        foreach ($stations as $station) {
            $options[$station->getName()] = $station->getCode();
        }

        return $options;
    }
}
