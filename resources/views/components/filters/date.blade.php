<div class="form-group col-md-12 text-left float-left">

  <div class="form-row justify-content-md-center">
    <div class="col-md-6">

      <div class="form-row mr-2">
        <label for="start_date">Creación {{ __('ticket.start_date') }}</label>
        <input type="date" name="{{ $filter->class() }}[]" class="form-control" value="@if(isset($value[0])){{ $value[0] }}@endif">
      </div>
    </div>

    <div class="col-md-6">

      <div class="form-row">
        <label for="end_date">Creación {{ __('ticket.end_date') }}</label>
        <input type="date" name="{{ $filter->class() }}[]" class="form-control" value="@if(isset($value[1])){{ $value[1] }}@endif">
      </div>
    </div>
  </div>

</div>
