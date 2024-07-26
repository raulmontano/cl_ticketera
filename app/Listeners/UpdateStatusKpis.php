<?php

namespace App\Listeners;

use App\Events\TicketStatusUpdated;
use App\Kpi\Kpi;
use App\Kpi\ReopenedKpi;
use App\Kpi\SolveKpi;
use App\Kpi\PausedKpi;
use App\Ticket;
use Carbon\Carbon;

class UpdateStatusKpis
{
    public function handle(TicketStatusUpdated $event)
    {
        $this->calculateSolvedKpi($event);
        $this->calculateReopenedKpi($event);
        $this->calculatePausedKpi($event);
    }

    private function calculatePausedKpi($event)
    {

        //previous status == paused
        //actual status = pending
        if ($event->previousStatus == Ticket::STATUS_PAUSED && $event->ticket->status == Ticket::STATUS_PENDING) {
            $currentPausedTime = \DB::table('ticket_events')
                                ->where('ticket_id', $event->ticket->id)
                                ->where('body', 'Estado actualizado: Pausado')
                                ->orderBy('created_at', 'DESC')
                                ->first();

            $now = Carbon::now();
            $pausedTime = Carbon::parse($currentPausedTime->created_at)->diffInSeconds($now);
            \Log::info('Guardando: ', [$now,$event->ticket->id, Kpi::TYPE_TICKET,$pausedTime]);
            //OBTENER EL TIEMPO CUANDO LLEGÓ A PAUSED
            PausedKpi::obtain($now, $event->ticket->id, Kpi::TYPE_TICKET)->addValue($pausedTime);
        }
    }

    private function calculateSolvedKpi($event)
    {
        if ($event->ticket->status != Ticket::STATUS_SOLVED) {
            return;
        }

        if (! SolveKpi::doesApply($event->ticket, $event->user, $event->previousStatus)) {
            return;
        }
        $time = $event->ticket->created_at->diffInMinutes(Carbon::now());
        SolveKpi::obtain($event->ticket->created_at, $event->user->id, Kpi::TYPE_USER)->addValue($time);

        if (! $event->ticket->team_id) {
            return;
        }
        SolveKpi::obtain($event->ticket->created_at, $event->ticket->team_id, Kpi::TYPE_TEAM)->addValue($time);
    }

    private function calculateReopenedKpi($event)
    {
        $score = ReopenedKpi::score($event->ticket, $event->previousStatus);
        if ($score == 0) {
            return;
        }

        ReopenedKpi::obtain($event->ticket->created_at, $event->ticket->user_id, Kpi::TYPE_USER)->addValue($score);

        if (! $event->ticket->team_id) {
            return;
        }
        ReopenedKpi::obtain($event->ticket->created_at, $event->ticket->team_id, Kpi::TYPE_TEAM)->addValue($score);
    }
}
