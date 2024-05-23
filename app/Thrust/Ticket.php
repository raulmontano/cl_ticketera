<?php

namespace App\Thrust;

use Carbon\Carbon;
use App\Repositories\TicketsIndexQuery;
use App\ThrustHelpers\Actions\ChangePriority;
use App\ThrustHelpers\Actions\ChangeStatus;
use App\ThrustHelpers\Actions\MergeTickets;
use App\ThrustHelpers\Actions\NewTicket;
use App\ThrustHelpers\Actions\ExportTickets;
use App\ThrustHelpers\Fields\Rating;
use App\ThrustHelpers\Fields\TicketStatusField;
use App\ThrustHelpers\Filters\EscalatedFilter;
use App\ThrustHelpers\Filters\PriorityFilter;
use App\ThrustHelpers\Filters\StatusFilter;
use App\ThrustHelpers\Filters\TicketTypeFilter;
use App\ThrustHelpers\Filters\TicketPostTypeFilter;
use App\ThrustHelpers\Filters\TitleFilter;
use App\ThrustHelpers\Filters\ReferenceNumberFilter;
use App\ThrustHelpers\Filters\CompanyFilter;
use BadChoice\Thrust\Fields\Date;
use BadChoice\Thrust\Fields\Gravatar;
use BadChoice\Thrust\Fields\Link;
use BadChoice\Thrust\Fields\Text;
use BadChoice\Thrust\Resource;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Ticket extends Resource
{
    public static $model        = \App\Ticket::class;
    public static $search       = ['title', 'body'];
    public static $defaultSort  = 'updated_at';
    public static $defaultOrder = 'desc';

    public function fields()
    {

      //dump();

      $fields = [

        /*  Text::make('tickets.id', 'ID')->displayWith(function ($ticket) {
              return $ticket->id;
          }),*/

          Link::make('tickets.id', __('ticket.reference_number'))->displayCallback(function ($ticket) {
              return $ticket->reference_number . '- '. Str::limit($ticket->subject ?? $ticket->title, 25);
          })->route('tickets.show'),

          Text::make('tickets.status', __('ticket.status'))->displayWith(function ($ticket) {
              return __("ticket." . $ticket->statusName() );
          }),

        ];

        $isEditor = false;

        if(auth()->user()->teams()->count()){
          //
          $isEditor = (auth()->user()->teams()->first()->id == 1);
        } else {
          $fields[] = Text::make('team.name', __('ticket.team'));
        }

        if(!$isEditor){
          $fields[] = Text::make('requester.name', trans_choice('ticket.requester', 2));
        }

        if(request()->has('assigned') && !$isEditor){
          //
        } else {
          $fields[] = Text::make('tickets.id', trans_choice('ticket.user', 1) . ' MC')->displayWith(function ($ticket) {
            return $ticket->user_mc ? $ticket->user_mc->name : '<span class="text-nowrap">-- Sin asignar --</span>';
          });
        }


        if(request()->has('assigned') && $isEditor){
          //dont show editor when is self assigned
        }else if(request()->has('pending') || $isEditor){
          $fields[] = Text::make('user.name', 'Editor')->displayWith(function ($ticket) {
              return $ticket->user ? $ticket->user->name : '<span class="text-nowrap">-- Sin asignar --</span>';
          });
        }

        $fields[] = Text::make('tickets.type', __('ticket.type'))->displayWith(function ($ticket) {
            return '<span class="text-nowrap">'.$ticket->type->name .' '.  $ticket->postType->name .' '.  $ticket->company->name . '</span>';
        });

        $fields[] = Text::make('tickets.priority', __('ticket.priority'))->displayWith(function ($ticket) {
            return __("ticket." . $ticket->priorityName() );
        });

/*
        $fields[] = Text::make('tickets.id', __('ticket.requested'))->displayWith(function ($ticket) {
            $timeInMc = $ticket->time_in_mc->first();
            return $timeInMc ? $timeInMc->created_at->diffInDays() .' días' : '-';
        });
*/

    if(!$isEditor){
        $fields[] = Date::make('created_at', __('ticket.requested'))->displayWith(function ($ticket) {


              $days = $ticket->created_at->diffInDays();

              if($days <= 1){
                $color = 'green';
              } elseif( $days > 1 && $days <=6 ){
                $color = 'orange';
              } else {
                $color = 'red';
              }

              return "<span style='color:$color;'>" . $ticket->created_at->diffForHumans() . '</span>';

            });
          }

            if(request()->has('pending') || $isEditor){
              $fields[] = Text::make('tickets.id', 'Enviado a Editores')->displayWith(function ($ticket) {

                $timeInEditor = $ticket->time_in_editor->first();



                if($timeInEditor){

                  return $timeInEditor->created_at->diffForHumans();

                  /* FIXME
                $days = $timeInEditor->created_at->diffInDays();

                if($days <= 1){
                  $color = 'green';
                } elseif( $days > 1 && $days <=6 ){
                  $color = 'orange';
                } else {
                  $color = 'red';
                }

                return "<span style='color:$color;'>" . $timeInEditor->created_at->diffForHumans() . '</span>';
                */
              } else {
                return '-';
              }

              });

            }

            if($isEditor){

              $fields[] = Text::make('tickets.id', 'Fecha compromiso')->displayWith(function ($ticket) {

                $timeInEditor = $ticket->time_in_editor->first();

                $dueDate = Carbon::parse(calcularFechaSolucion($timeInEditor->created_at));

                $days = $dueDate->diffInDays(now(), false);

                if($days <= -2){
                  $color = 'green';
                } elseif( $days <= 0 ){
                  $color = 'orange';
                } else {
                  $color = 'red';
                }

                return "<span style='color:$color;'>" . $dueDate->diffForHumans(). '</span>';

              });

              $fields[] = Text::make('tickets.id', 'Tiempo editores')->displayWith(function ($ticket) {
              $timeInEditor = $ticket->time_in_editor->first();

              return DiferenciaTiempoTranscurrido($timeInEditor->created_at);


                            });
            }

        return $fields;

    }

    public function getFields()
    {
        return $this->fields();
    }

    protected function getBaseQuery()
    {
        return TicketsIndexQuery::get()->with($this->getWithFields());
    }

    public function update($id, $newData)
    {
        return false; //return parent::update($id, Arr::except($newData, ['created_at', 'updated_at']));
    }

    public function mainActions()
    {
        return [
            new NewTicket(),
            new ExportTickets(),
        ];
    }

    public function actions()
    {
        return [];
    }

    public function filters()
    {
        //return [];

        return [
            new TitleFilter(),
            new ReferenceNumberFilter(),
            new CompanyFilter(),
            new StatusFilter(),
            //new PriorityFilter(),
//            new EscalatedFilter(),
            new TicketTypeFilter(),
            new TicketPostTypeFilter(),
        ];
    }

    public function canDelete($object)
    {
        return false;
    }

    public function canEdit($object)
    {
        return false;
    }
}
