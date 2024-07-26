<?php

namespace App;

trait Assignable
{
    public function assignTo($user)
    {
        if (! $user instanceof User) {
            $user = User::findOrFail($user);
        }
        if ($this->user && $this->user->id == $user->id) {
            return;
        }

        //will assign user only if the user is member of the current ticket team
        if ($this->team && !$this->team->members()->where('users.id', $user->id)->first()) {
            \Log::info('Se intentó asignar un usuario que no es del equipo: ticket_id,ticket_team,user_id', [$this->id,$this->team->id,$user->id]);
            return;
        }

        $this->user()->associate($user)->save();
        $user->notify($this->getAssignedNotification());

        $event = TicketEvent::make($this, trans('notification.events.assignedTo').": {$user->name}");
        $event->assigned_to_user_id = $user->id;
        $event->save();
    }

    public function assignToTeam($team)
    {
        if (! $team instanceof Team) {
            $team = Team::findOrFail($team);
        }
        if ($this->team && $this->team->id == $team->id) {
            return;
        }
        $this->team()->associate($team)->save();
        $team->notify($this->getAssignedNotification());

        $event = TicketEvent::make($this, trans('notification.events.assignedToTeam')." : {$team->name}");
        $event->assigned_to_team_id = $team->id;
        $event->save();
    }
}
