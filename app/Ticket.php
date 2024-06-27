<?php

namespace App;

use App\Authenticatable\Admin;
use App\Authenticatable\Assistant;
use App\Events\TicketCommented;
use App\Events\TicketStatusUpdated;
use App\Notifications\RateTicket;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketCreated;
use App\Notifications\TicketEscalated;
use App\Services\IssueCreator;
use App\Services\TicketLanguageDetector;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Ticket extends BaseModel
{
    use SoftDeletes;
    use Taggable;
    use Categorizable;
    use Assignable;
    use Subscribable;
    use Rateable;

    /** ESTATUS MEJORA CONTINUA **/
    //    1=En Espera (por defecto)
    //    2=En Validación.
    //    3=Enviado a Editores. //EVENTO CUANDO SE ASIGNE TEAM SE CAMBIA ESTATUS
    //    4=Solucionado.
    //    5=Rechazado.
    //    7=Cerrado sin gestión.

    /** ESTATUS EDITORES **/
    //    3=Enviado a Editores (por defecto)
    //    4=Resuelto=Solucionado
    //    8=Pausado
    //    9=Error en documento. //EVENTO CUANDO SE PONGA ESTE ESTATUS SE DEBE QUITAR EL TEAM

    public const STATUS_NEW     = 1; //En espera
    public const STATUS_OPEN    = 2; //En Validación.
    public const STATUS_PENDING = 3; //Enviado a Editores. //EVENTO CUANDO SE ASIGNE TEAM SE CAMBIA ESTATUS
    public const STATUS_SOLVED  = 4; //Solucionado.
    public const STATUS_CLOSED  = 5; //Rechazado.
    public const STATUS_MERGED  = 6;
    public const STATUS_SPAM    = 7; //Cerrado sin gestión.
    public const STATUS_PAUSED  = 8; //Pausado
    public const STATUS_ERROR   = 9; //Error en documento. //EVENTO CUANDO SE PONGA ESTE ESTATUS SE DEBE QUITAR EL TEAM Y REGRESAR A MEJORA CONTINUA

    public const PRIORITY_LOW       = 1; //COMUNICAR=NO
    public const PRIORITY_NORMAL    = 2;
    public const PRIORITY_HIGH      = 3; //COMUNICAR=SI
    public const PRIORITY_BLOCKER   = 4;

    public const COMPLEXITY_LOW       = 1;
    public const COMPLEXITY_NORMAL    = 2;
    public const COMPLEXITY_HIGH      = 3;

    public const INFORM_NO    = 0;
    public const INFORM_YES       = 1;


    protected $append = ['user_mc'];

    public static function createAndNotify($requester, $title, $body, $channels, $categories, $type, $company, $post_type, $start_date, $end_date)
    {
        $requester = Requester::findOrCreate($requester['name'] ?? 'Unknown', $requester['email'] ?? null);
        $ticket    = $requester->tickets()->create([
            'title'        => substr($title, 0, 190),
            'body'         => $body,
            'ticket_type_id'         => $type,
            'ticket_company_id'         => $company,
            'ticket_post_type_id'         => $post_type,
            'start_date' => $start_date,
            'end_date'=> $end_date,
            'public_token' => Str::random(24),
            'priority' => self::PRIORITY_LOW,
            'complexity' => self::COMPLEXITY_LOW,
            'inform' => self::INFORM_NO,
        ]);

        $channels[] = 'Solicitante de Contenido'; //FIXME PARA QUE LO QUIEREN HARDCOEADO?

        $ticket->attachTags($channels); //channels
        $ticket->attachCategories($categories); //categories

        $ticket->assignToTeam(Settings::defaultTeamId()); //editores

        tap(new TicketCreated($ticket), function ($newTicketNotification) use ($ticket) {
            //Admin::notifyAll($newTicketNotification);

            if ($ticket->team) {
                //$ticket->team->notify($newTicketNotification);
            }

            $ticket->requester->notify($newTicketNotification);
        });

        return $ticket;
    }

    public function updateWith($title, $body, $channels, $categories, $type, $company, $post_type, $priority, $inform, $complexity, $start_date, $end_date)
    {
        $this->update([
          'title'        => substr($title, 0, 190),
          'body'         => $body,
          'ticket_type_id'         => $type,
          'ticket_company_id'         => $company,
          'ticket_post_type_id'         => $post_type,
          'priority' => $priority,
          'complexity' => $complexity,
          'inform' => $inform,
          'start_date' => $start_date,
          'end_date'=> $end_date,
        ]);

        $channels[] = 'Solicitante de Contenido'; //FIXME PARA QUE LO QUIEREN HARDCOEADO?

        $this->syncTags($channels); //channels

        $this->syncCategories($categories);

        return $this;
    }

    public function updateSummary($subject, $summary)
    {
        $this->update(['subject' => $subject, 'summary' => $summary]);

        return $this;
    }

    public static function findWithPublicToken($public_token)
    {
        return self::where('public_token', $public_token)->firstOrFail();
    }

    public function getUserMcAttribute()
    {
        return $this->events()
                ->select('users.name', 'users.id', 'users.email')
                ->join('users', 'users.id', 'ticket_events.assigned_to_user_id')
                ->join('memberships', function ($join) {
                    $join->on('memberships.user_id', '=', 'ticket_events.user_id');
                    $join->on('memberships.team_id', \DB::raw(2));
                })
                ->whereNotNull('assigned_to_user_id')
                ->orderBy('ticket_events.id', 'DESC')
                ->first();
    }

    public function getTimeInMcAttribute()
    {
        return $this->events()
                ->select('ticket_events.created_at')
                ->where('ticket_events.assigned_to_team_id', \DB::raw(2)) //MC
                ->orderBy('ticket_events.id', 'DESC')
                ->get();
    }

    public function getTimeInEditorAttribute()
    {
        return $this->events()
                ->select('ticket_events.created_at')
                ->where('ticket_events.assigned_to_team_id', \DB::raw(1)) //EDITORES
                ->orderBy('ticket_events.id', 'DESC')
                ->get();
    }

    public function getReferenceNumberAttribute()
    {
        return $this->created_at->format('Ymd_Hi');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requester()
    {
        return $this->belongsTo(Requester::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function comments()
    {
        return $this->commentsAndNotes()->where('private', false);
    }

    public function commentsAndNotes()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function commentsAndNotesAndEvents()
    {
        return $this->commentsAndNotes->toBase()->merge($this->events);
    }

    public function events()
    {
        return $this->hasMany(TicketEvent::class)->latest('ticket_events.created_at');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function categories()
    {
        return $this->morphToMany(Tag::class, 'categorizable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function mergedTickets()
    {
        return $this->belongsToMany(self::class, 'merged_tickets', 'ticket_id', 'merged_ticket_id');
    }

    public function type()
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    public function postType()
    {
        return $this->belongsTo(TicketPostType::class, 'ticket_post_type_id');
    }

    public function company()
    {
        return $this->belongsTo(TicketCompany::class, 'ticket_company_id');
    }

    /**
     * @param $user
     * @param $newStatus
     *
     * @return mixed
     */
    private function updateStatusFromComment($user, $newStatus)
    {
        $previousStatus = $this->status;
        if ($newStatus && $newStatus != $previousStatus) {
            $this->updateStatus($newStatus);
        } elseif (! $user && $this->status != static::STATUS_NEW) {
            //$this->updateStatus(static::STATUS_OPEN);
        } else {
            $this->touch();
        }
        event(new TicketStatusUpdated($this, $user, $previousStatus));

        return $previousStatus;
    }

    private function associateUserIfNecessary($user)
    {
        if (! $this->user && $user) {
            $this->assignTo($user);
        }
    }

    protected function getAssignedNotification()
    {
        return new TicketAssigned($this);
    }

    public function addComment($user, $body, $newStatus = null)
    {
        if ($user && $this->isEscalated()) {
            return $this->addNote($user, $body);
        }
        $previousStatus = $this->updateStatusFromComment($user, $newStatus);
        //$this->associateUserIfNecessary($user); //deleted

        if (! $body || ($user && $body === $user->settings->tickets_signature)) {
            return null;
        }

        $comment = $this->comments()->create([
            'body'       => $body,
            'user_id'    => $user ? $user->id : null,
            'new_status' => $newStatus ?: $this->status,
            'private'    => !request('public'),
        ]);

        if (request('public') && $user) {
            $comment->notifyNewComment();
        }

        event(new TicketCommented($this, $comment, $previousStatus));

        return $comment;
    }

    public function addNote($user, $body)
    {
        if (! $body) {
            return null;
        }
        //if( ! $this->user && $user) { $this->user()->associate($user)->save(); }  //We don't want the notes to automatically assign the user
        else {
            $this->touch();
        }

        return $this->comments()->create([
            'body'       => $body,
            'user_id'    => $user->id,
            'new_status' => $this->status,
            'private'    => !request('public'),
        ]);

        if (request('public')) {
            $comment->notifyNewNote();
        }
    }

    public function merge($user, $tickets)
    {
        collect($tickets)->map(function ($ticket) {
            return is_numeric($ticket) ? Ticket::find($ticket) : $ticket;
        })->reject(function ($ticket) {
            return $ticket->id == $this->id || $ticket->status > Ticket::STATUS_SOLVED;
        })->each(function ($ticket) use ($user) {
            $ticket->addNote($user, "Merged with #{$this->id}");
            $ticket->updateStatus(Ticket::STATUS_MERGED);
            $this->mergedTickets()->attach($ticket);
        });
    }

    public function updateStatus($status)
    {
        $this->update(['status' => $status, 'updated_at' => Carbon::now()]);

        TicketEvent::make($this, trans('notification.events.updateStatus').': '.trans("ticket." . $this->statusName()));
        if ($status == Ticket::STATUS_SOLVED && !$this->rating && config('handesk.sendRatingEmail')) {
            $this->requester->notify((new RateTicket($this))->delay(now()->addMinutes(60)));
        }

        if (in_array($status, [self::STATUS_SOLVED,self::STATUS_CLOSED,self::STATUS_SPAM,self::STATUS_ERROR])) {
            $user = \Auth::user();

            $comment = $this->comments()->create([
              'body'       => 'Ticket cerrado',
              'user_id'    => $user->id,
              'new_status' => $status,
          ])->notifyNewComment();
        }
    }

    public function updatePriority($priority)
    {
        $this->update(['priority' => $priority, 'updated_at' => Carbon::now()]);
        TicketEvent::make($this, 'Priority updated: '.$this->priorityName());
    }

    public function setLevel($level)
    {
        $this->update(['level' => $level]);
        if ($level == 1) {
            TicketEvent::make($this, 'Escalated');

            return Assistant::notifyAll(new TicketEscalated($this));
        }
        TicketEvent::make($this, 'De-Escalated');
    }

    public function isEscalated()
    {
        return $this->level == 1;
    }

    public function hasBeenReplied()
    {
        return $this->comments()->whereNotNull('user_id')->count() > 1;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', '<', self::STATUS_SOLVED);
    }

    public function scopeSolved($query)
    {
        return $query->where('status', '>=', self::STATUS_SOLVED);
    }

    public function canBeEdited()
    {
        $user = auth()->user();

        $isTeamMc = $isAdmin = false;

        if ($user->admin) {
            $isAdmin = true;
        } else {
            $userTeam = $user->teams()->first();

            if ($userTeam) {
                if ($userTeam->id == 2) {
                    $isTeamMc = true;
                }
            }
        }

        return ! in_array($this->status, [self::STATUS_SOLVED,self::STATUS_CLOSED, self::STATUS_MERGED])
              && ($isAdmin || $isTeamMc);
    }

    public static function statusNameFor($status)
    {
        switch ($status) {
            case static::STATUS_NEW: return 'new';
            case static::STATUS_OPEN: return 'open';
            case static::STATUS_PENDING: return 'pending';
            case static::STATUS_SOLVED: return 'solved';
            case static::STATUS_CLOSED: return 'closed';
            case static::STATUS_MERGED: return 'merged';
            case static::STATUS_SPAM: return 'spam';
            case static::STATUS_PAUSED: return 'paused';
            case static::STATUS_ERROR: return 'error';
        }
    }

    public function statusName()
    {
        return static::statusNameFor($this->status);
    }

    public static function priorityNameFor($priority)
    {
        switch ($priority) {
            case static::PRIORITY_LOW: return 'low'; //no
            case static::PRIORITY_NORMAL: return 'normal';
            case static::PRIORITY_HIGH: return 'high'; //sí
            case static::PRIORITY_BLOCKER: return 'blocker';
        }
    }

    public function informName()
    {
        return $this->inform ? 'yes' : 'no';
    }

    public function priorityName()
    {
        return static::priorityNameFor($this->priority);
    }

    public static function complexityNameFor($complexity)
    {
        switch ($complexity) {
            case static::COMPLEXITY_LOW: return 'low'; //no
            case static::COMPLEXITY_NORMAL: return 'normal';
            case static::COMPLEXITY_HIGH: return 'high'; //sí
        }
    }

    public function complexityName()
    {
        return static::complexityNameFor($this->complexity);
    }

    public function getSubscribableEmail()
    {
        return $this->requester->email;
    }

    public function getSubscribableName()
    {
        return $this->requester->name;
    }

    //========================================================
    // ISSUE
    //========================================================
    public function createIssue(IssueCreator $issueCreator, $repository)
    {
        $repo  = explode('/', $repository);
        $issue = $issueCreator->createIssue(
            $repo[0],
            $repo[1],
            $this->subject ?? $this->title,
            'Issue from ticket: '.route('tickets.show', $this)."   \n\r".($this->summary ?? $this->body)
        );
        $issueUrl = "https://bitbucket.org/{$repository}/issues/{$issue->id}";
        $this->addNote(auth()->user(), "Issue created {$issueUrl} with id #{$issue->id}");
        TicketEvent::make($this, "Issue created #{$issue->id} at {$repository}");

        return $issue;
    }

    public function findIssueNote()
    {
        return $this->commentsAndNotes->first(function ($comment) {
            return Str::startsWith($comment->body, 'Issue created');
        });
    }

    public function getIssueId()
    {
        $issueNote = $this->findIssueNote();
        if (! $issueNote) {
            return null;
        }

        return substr($issueNote->body, strpos($issueNote->body, '#') + 1);
    }

    public function issueUrl()
    {
        $issueNote = $this->findIssueNote();
        if (! $issueNote) {
            return null;
        }
        $start  = strpos($issueNote->body, 'https://');
        $end    = strpos($issueNote->body, 'with id');

        return substr($issueNote->body, $start, $end - $start);
    }

    public function createIdea()
    {
        $idea = Idea::create([
            'requester_id' => $this->requester_id,
            'title'        => $this->title,
            'body'         => $this->body,
        ])->attachTags(['ticket']);
        TicketEvent::make($this, "Idea created #{$idea->id}");
        App::setLocale((new TicketLanguageDetector($this))->detect());
        $this->addComment(auth()->user(), __('idea.fromTicket'), self::STATUS_SOLVED);

        return $idea;
    }

    public function getIdeaId()
    {
        $issueEvent = $this->events()->where('body', 'like', '%Idea created%')->first();
        if (! $issueEvent) {
            return null;
        }

        return explode('#', $issueEvent->body)[1];
    }
}

/*
ALTER TABLE `tickets`
ADD `complexity` tinyint(4) NOT NULL AFTER `priority`,
ADD `inform` tinyint(4) NOT NULL AFTER `complexity`;
*/
