<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel;

final class Train
{
    private string $number;
    private TrainCategory $category;
    private TrainOperator $operator;

    public function __construct(string $number, TrainCategory $category, TrainOperator $operator)
    {
        $this->number = $number;
        $this->category = $category;
        $this->operator = $operator;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getCategory(): TrainCategory
    {
        return $this->category;
    }

    public function getOperator(): TrainOperator
    {
        return $this->operator;
    }
}
