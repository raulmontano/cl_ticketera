<?php

namespace App\Kpi;

use App\Ticket;

class PausedKpi extends Kpi
{
    const KPI = Kpi::KPI_PAUSED;

    public static function doesApply($ticket, $user, $previousStatus)
    {
        if ($previousStatus == Ticket::STATUS_PENDING) {
            return true;
        }

        return false;
    }
}
