<?php $filters = $resource->filters() ?>

@if ($filters && count($filters) > 0)
    <div class="dropdown inline">
        <button class="secondary"> Buscar @icon(caret-down)</button>
    </div>
    <?php $filtersApplied = $resource->filtersApplied(); ?>
    <ul class="dropdown-container filters" style="right:70px; margin-top:0px;">
      <li>
        <form id="filtersForm">
          @foreach (collect($filters) as $filter)
            {!! $filter->display($filtersApplied) !!}
          @endforeach
            <div class="form-group col-md-12 text-center float-left mt3">
                <button class="btn btn-secondary col-md-5 reset" href="#">Limpiar filtros</button>
                <button class="btn btn-primary ml2 col-md-6">{{ __("thrust::messages.apply") }}</button>
            </div>
        </form>
      </li>
    </ul>
@endif
