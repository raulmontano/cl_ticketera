<?php

namespace App\Repositories;

class AgentIndexQuery
{
    public static function get(AgentRepository $repository = null)
    {
        if (! $repository) {
            $repository = app(AgentRepository::class);
        }

        $agents = $repository->all();

        return $agents;
    }
}
