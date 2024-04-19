<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Repositories\TicketsRepository;
use App\Repositories\TicketsIndexQuery;
use BadChoice\Thrust\Facades\Thrust;
use BadChoice\Thrust\ResourceFilters\Filters;

class TicketExporterController extends Controller
{

  protected $output = '';
  protected $indexFields;

    public function export()
    {
        $resource = Thrust::make('tickets');
          $repository = new TicketsRepository();
          $ticketsQuery = TicketsIndexQuery::get($repository)->with($resource->getWithFields());



          if (request('filters')) {
              Filters::applyFromRequest($ticketsQuery, request('filters'));
          }

          $this->generate($ticketsQuery,$resource);

          return response(rtrim($this->output, "\n"), 200, $this->getHeaders('tickets ' . now()->format('Ymd_His')));

    }

    public function generate($ticketsQuery,$resource) {

        $this->indexFields = $resource->fieldsFlattened()->where('showInIndex', true);

        $this->writeHeader();
        $ticketsQuery->chunk(200, function ($rows) use (&$output) {
            $rows->each(function ($row) {
                $this->writeRow($row);
            });
        });
        return $this->output;
    }

    private function getHeaders($title)
    {
        return [
            'Content-Type'        => 'application/csv; charset=UTF-8',
            'Content-Encoding'    => 'UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $title . '.csv"',  // Safari filename must be between commas
        ];
    }

    private function writeHeader()
    {
        $this->indexFields->each(function ($field) {
            $this->output .= $field->getTitle() . ';';
        });
        $this->output .= PHP_EOL;
    }

    private function writeRow($row)
    {

        $this->indexFields->each(function ($field) use ($row) {
            $this->output .= strip_tags($field->displayInIndex($row)) .';';
        });
        $this->output .= PHP_EOL;
    }

}
