<?php

namespace App\Filters;

use Schema;

class TicketFilters
{
    public function apply($query, $filters)
    {
        $availableFields = Schema::getColumnListing('tickets');

        collect($filters)->filter(function ($value, $filter) use ($availableFields) {
            return in_array($filter, $availableFields);
        })->each(function ($value, $filter) use (&$query) {
            $query = $query->where($filter, $value);
        });

        return $query;
    }

    public static function applyFromRequest($query, $filtersEncoded)
    {
        static::decodeFilters($filtersEncoded)->reject(function ($value) {
            return $value == null || $value == '--';
        })->each(function ($value, $filterClass) use ($query) {
            $filter = new $filterClass;
            $filter->apply(request(), $query, $value);
        });
        return $query;
    }

    public static function decodeFilters($filtersEncoded)
    {
        $filters = explode('&', base64_decode($filtersEncoded));

        $decodedFilters = [];

        foreach ($filters as $filter) {
            $filterAndValue = explode('=', $filter);

            $filterName = urldecode($filterAndValue[0]);
            $filterValue = $filterAndValue[1];

            if (substr($filterName, -2) == '[]') {
                $filterName = substr($filterName, 0, strpos($filterName, '['));

                if (isset($decodedFilters[$filterName])) {
                    $decodedFilters[$filterName][] = $filterValue;
                } else {
                    $decodedFilters[$filterName] = [$filterValue];
                }
            } else {
                $decodedFilters[$filterName] = $filterValue;
            }
        }

        return collect($decodedFilters);
    }
}
