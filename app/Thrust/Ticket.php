<?php

namespace App\Thrust;

use Carbon\Carbon;
use App\Ticket as TicketModel;
use App\Repositories\TicketsIndexQuery;
use App\Filters\TicketFilters as Filters;
use App\ThrustHelpers\Actions\ChangePriority;
use App\ThrustHelpers\Actions\ChangeStatus;
use App\ThrustHelpers\Actions\MergeTickets;
use App\ThrustHelpers\Actions\NewTicket;
use App\ThrustHelpers\Actions\ExportTickets;
use App\ThrustHelpers\Fields\Rating;
use App\ThrustHelpers\Fields\TicketStatusField;
use App\ThrustHelpers\Filters\EscalatedFilter;
use App\ThrustHelpers\Filters\InformFilter;
use App\ThrustHelpers\Filters\IdFilter;
use App\ThrustHelpers\Filters\UserEditorFilter;
use App\ThrustHelpers\Filters\CreatedAtFilter;
use App\ThrustHelpers\Filters\UserMcFilter;
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
use BadChoice\Thrust\ResourceFilters\Sort;

class Ticket extends Resource
{
    public static $model        = \App\Ticket::class;
    public static $search       = ['title', 'body'];
    public static $defaultSort  = 'updated_at';
    public static $defaultOrder = 'desc';

    public function fields()
    {

      //dump();

        $fields = [];

        if (request()->path() == 'tickets/export') {
            $fields[] = Text::make('tickets.id', "ID")->displayWith(function ($ticket) {
                return $ticket->id;
            });

            $fields[] = Link::make('tickets.id', __('ticket.reference_number'))->displayCallback(function ($ticket) {
                return $ticket->reference_number;
            });

            $fields[] = Text::make('tickets.title', __('ticket.subject'))->displayWith(function ($ticket) {
                return $ticket->title;
            });

            $fields[] = Text::make('tickets.body', __('ticket.body'))->displayWith(function ($ticket) {
                return $ticket->body;
            });
        } else {
            $fields[] = Text::make('tickets.id', "ID")->sortable()->displayWith(function ($ticket) {
                return $ticket->id;
            });

            $fields[] = Link::make('tickets.id', __('ticket.reference_number'))->sortable()->displayCallback(function ($ticket) {
                return $ticket->reference_number;
            })->route('tickets.show');

            $fields[] = Text::make('tickets.subject', __('ticket.subject'))->sortable()->displayWith(function ($ticket) {
                return Str::limit($ticket->title, 25);
            });
        }

        /*
        Número de referencia.
        Estado.
        Solicitante.
        Agente MC. FIX. SORT BY RELATION NAME
        Tipo de solicitud.
        Comunicar.
        Solicitado.
        */

        $fields[] = Text::make('tickets.status', __('ticket.status'))->sortable()->displayWith(function ($ticket) {
            return __("ticket." . $ticket->statusName());
        });

        $isEditor = false;

        if (auth()->user()->teams()->count()) {
            //
            $isEditor = (auth()->user()->teams()->first()->id == 1);
        } else {
            $fields[] = Text::make('team.name', __('ticket.team'));
        }

        if (!$isEditor) {
            $fields[] = Text::make('requester.name', trans_choice('ticket.requester', 2));
        }

        if (request()->has('assigned') && !$isEditor) {
            //
        } else {
            $fields[] = Text::make('tickets.user_id', trans_choice('ticket.user', 1) . ' MC')->displayWith(function ($ticket) {
                return $ticket->user_mc ? $ticket->user_mc->name : '<span class="text-nowrap">-- Sin asignar --</span>';
            });
        }


        if (request()->has('assigned') && $isEditor) {
            //dont show editor when is self assigned
        } elseif (request()->has('pending') || $isEditor) {
            $fields[] = Text::make('user.name', 'Editor')->displayWith(function ($ticket) {
                return $ticket->user ? $ticket->user->name : '<span class="text-nowrap">-- Sin asignar --</span>';
            });
        }

        $fields[] = Text::make('tickets.ticket_type_id', __('ticket.type'))->sortable()->displayWith(function ($ticket) {
            return '<span class="text-nowrap">'.$ticket->type->name .' '.  $ticket->postType->name .' '.  $ticket->company->name . '</span>';
        });

        $fields[] = Text::make('tickets.inform', __('ticket.inform'))->sortable()->displayWith(function ($ticket) {
            return __("ticket.inform-label." . $ticket->informName());
        });

        /*
                $fields[] = Text::make('tickets.id', __('ticket.requested'))->displayWith(function ($ticket) {
                    $timeInMc = $ticket->time_in_mc->first();
                    return $timeInMc ? $timeInMc->created_at->diffInDays() .' días' : '-';
                });
        */

        if (!$isEditor) {
            if (request()->has('pending')) {
                $fields[] = Text::make('tickets.id', 'Complejidad / Prioridad')->displayWith(function ($ticket) {
                    //return $ticket->complexityName();
                    return '<span class="label ticket-complexity-'.$ticket->complexityName().'">'. trans("ticket.complexity-label." . $ticket->complexityName()) .'</span>'
                    .' / <span class="label ticket-priority-'.$ticket->priorityName().'">'. trans("ticket." . $ticket->priorityName()) .'</span>';
                });
            }

            if (request()->path() != 'tickets/export') {
                $fields[] = Date::make('created_at', __('ticket.requested'))->sortable()->displayWith(function ($ticket) {
                    $days = $ticket->created_at->diffInDays();

                    if ($days <= 1) {
                        $color = 'green';
                    } elseif ($days > 1 && $days <=6) {
                        $color = 'orange';
                    } else {
                        $color = 'red';
                    }

                    return '<span style="color:'.$color.';" class="text-nowrap" title="'.$ticket->created_at->format('H:i:s').'">'. $ticket->created_at->format('Y-m-d') . '</span>';
                });
            }
        }

        if (request()->has('pending')) {
            if (request()->path() != 'tickets/export') {
                $fields[] = Text::make('tickets.id', 'Enviado a Editores')->displayWith(function ($ticket) {
                    $timeInEditor = $ticket->time_in_editor->first();

                    if ($timeInEditor) {
                        return '<span class="text-nowrap" title="'.$timeInEditor->created_at->format('H:i:s').'">'.$timeInEditor->created_at->format('Y-m-d').'</span>';
                    } else {
                        return '-';
                    }
                });
            }
        }

        if ($isEditor) {
            $fields[] = Text::make('tickets.id', 'Complejidad / Prioridad')->displayWith(function ($ticket) {
                //return $ticket->complexityName();
                return '<span class="text-nowrap"><span class="label ticket-complexity-'.$ticket->complexityName().'">'. trans("ticket.complexity-label." . $ticket->complexityName()) .'</span>'
                        .' / <span class="label ticket-priority-'.$ticket->priorityName().'">'. trans("ticket." . $ticket->priorityName()) .'</span></span>';
            });

            if (request()->path() != 'tickets/export') {
                $fields[] = Text::make('tickets.id', 'Enviado a Editores')->displayWith(function ($ticket) {
                    $timeInEditor = $ticket->time_in_editor->first();

                    if ($timeInEditor) {
                        return '<span class="text-nowrap" title="'.$timeInEditor->created_at->format('H:i:s').'">'.$timeInEditor->created_at->format('Y-m-d').'</span>';
                    } else {
                        return '-';
                    }
                });

                $fields[] = Text::make('tickets.id', 'Fecha compromiso')->displayWith(function ($ticket) {
                    $timeInEditor = $ticket->time_in_editor->first();

                    if ($timeInEditor) {
                        $dueDate = Carbon::parse(calcularFechaSolucion($timeInEditor->created_at));

                        $days = $dueDate->diffInDays(now(), false);

                        if ($days <= -2) {
                            $color = 'green';
                        } elseif ($days <= 0) {
                            $color = 'orange';
                        } else {
                            $color = 'red';
                        }
                        return '<span style="color:'.$color.';" class="text-nowrap" title="'.$dueDate->format('H:i:s').'" >' . $dueDate->format('Y-m-d') . '</span>';
                    } else {
                        return "-";
                    }
                });
            } else {
                $fields[] = Text::make('tickets.id', 'Fecha compromiso')->displayWith(function ($ticket) {
                    $timeInEditor = $ticket->time_in_editor->first();

                    if ($timeInEditor) {
                        $dueDate = Carbon::parse(calcularFechaSolucion($timeInEditor->created_at));

                        return $dueDate;
                    } else {
                        return "-";
                    }
                });
            }

            $fields[] = Text::make('tickets.id', 'Tiempo editores')->displayWith(function ($ticket) {
                $timeInEditor = $ticket->time_in_editor->first();
                if ($timeInEditor) {
                    $solvedDate = false;
                    $pausedTime = 0;

                    if ($ticket->status == TicketModel::STATUS_PAUSED) {
                        $currentPausedTime = \DB::table('ticket_events')
                                          ->where('ticket_id', $ticket->id)
                                          ->where('body', 'Estado actualizado: Pausado')
                                          ->orderBy('created_at', 'DESC')
                                          ->first();

                        $pausedTime = Carbon::parse($currentPausedTime->created_at)->diffInSeconds(Carbon::now());
                    } elseif ($ticket->status == TicketModel::STATUS_SOLVED) {
                        $solvedDate = $ticket->solved_date ? $ticket->solved_date->created_at : false;
                    } else {
                        //
                    }

                    //GET TIEMPO PAUSADO
                    if ($kpt = $ticket->kpi_paused_time) {
                        $pausedTime += $kpt->time;
                    }

                    return DiferenciaTiempoTranscurrido($timeInEditor->created_at, $pausedTime, $solvedDate);
                } else {
                    return "-";
                }
            });
        }

        if (request()->path() == 'tickets/export') {
            $fields[] = Date::make('created_at', __('ticket.requested'))->displayWith(function ($ticket) {
                return $ticket->created_at;
            });

            $fields[] = Date::make('created_at', 'Asignado a agente MC')->displayWith(function ($ticket) {
                return $ticket->user_mc ? $ticket->user_mc->created_at : null;
            });

            $fields[] = Text::make('tickets.id', 'Enviado a editores')->displayWith(function ($ticket) {
                $timeInEditor = $ticket->time_in_editor->first();
                if ($timeInEditor) {
                    return $timeInEditor->created_at;
                }
            });

            $fields[] = Text::make('tickets.id', 'Fecha cerrado')->displayWith(function ($ticket) {
                return $ticket->closed_date ? $ticket->closed_date->created_at : null;
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
            new ReferenceNumberFilter(),
            new IdFilter(),
            new TitleFilter(),
            new CreatedAtFilter(),
            new CompanyFilter(),
            new InformFilter(),
            new TicketTypeFilter(),
            new TicketPostTypeFilter(),
            new StatusFilter(),
            new UserEditorFilter(),
            new UserMcFilter(),
            //new PriorityFilter(),
//            new EscalatedFilter(),
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

    /**
     * @return Builder
     */
    public function query()
    {
        $query = $this->getBaseQuery();

        $this->applySearch($query);

        $this->applySort($query);

        if (request('filters')) {
            Filters::applyFromRequest($query, request('filters'));
        }
        return $query;
    }

    private function applySort(&$query)
    {
        if (request('sort') && $this->sortFieldIsValid(request('sort'))) {
            return Sort::apply($query, request('sort'), request('sort_order'));
        }

        if (static::$sortable) {
            return Sort::apply($query, static::$sortField, 'ASC');
        }

        return Sort::apply($query, static::$defaultSort, static::$defaultOrder);
    }

    public function filtersApplied()
    {
        if (! request()->has('filters')) {
            return collect();
        }
        return Filters::decodeFilters(request('filters'));
    }
}
