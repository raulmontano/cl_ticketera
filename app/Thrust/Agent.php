<?php

namespace App\Thrust;

use App\ThrustHelpers\Actions\NewUser;
use BadChoice\Thrust\Fields\Date;
use BadChoice\Thrust\Fields\Email;
use BadChoice\Thrust\Fields\HasMany;
use BadChoice\Thrust\Fields\Link;
use BadChoice\Thrust\Fields\Text;
use BadChoice\Thrust\Resource;
use App\Repositories\AgentIndexQuery;

use App\ThrustHelpers\Fields\Delete;

class Agent extends Resource
{
    public static $model        = \App\User::class;
    public static $search       = ['name', 'email'];
    public static $defaultSort  = 'updated_at';
    public static $defaultOrder = 'desc';

    public function fields()
    {
        return [
            Text::make('name', __('user.name'))->sortable(),
            Email::make('email', __('user.email'))->sortable(),
            HasMany::make('teams', __('user.teams')),
            Date::make('created_at', __('user.created_at'))->sortable(),
            Date::make('updated_at', __('user.updated_at'))->sortable(),
            Delete::make('delete'),
            //Link::make('id', 'impersonate')->route('users.impersonate')->icon('key'),
        ];
    }

    public function getFields()
    {
        return $this->fields(); //override to remove default row actions "edit","delete"
    }

    public function mainActions()
    {
        return [
            //new NewUser, //Not working yet all new agents are created with invitation link
        ];
    }

    public function actions()
    {
        return [];
    }

    public function canDelete($object)
    {
        return false;
    }

    public function canEdit($object)
    {
        return false;
    }

    protected function getBaseQuery()
    {
        return AgentIndexQuery::get()->with($this->getWithFields());
    }
}
