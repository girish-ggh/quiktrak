<?php

namespace App\Service;

use App\Entity\Inspector;

class TimeZoneService
{
    public function convertToInspectorTimeZone(\DateTimeInterface $utcDateTime, Inspector $inspector): \DateTimeInterface
    {
        $inspectorTimeZone = new \DateTimeZone($inspector->getTimeZone());
        return (clone $utcDateTime)->setTimezone($inspectorTimeZone);
    }

    public function convertToUtc(\DateTimeInterface $localDateTime, Inspector $inspector): \DateTimeInterface
    {
        $inspectorTimeZone = new \DateTimeZone($inspector->getTimeZone());
        $dateTimeInInspectorZone = new \DateTime($localDateTime->format('Y-m-d H:i:s'), $inspectorTimeZone);
        return $dateTimeInInspectorZone->setTimezone(new \DateTimeZone('UTC'));
    }
}

