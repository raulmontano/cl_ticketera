@php

  if(method_exists($filter,'getCssDiv')){
    $divCss = $filter->getCssDiv();
  } else {
    $divCss = 'col-md-3';
  }

@endphp

<div class="form-group {{$divCss}} text-left float-left">
  <label> {!! $filter->getIcon() !!} {!! $filter->getTitle() !!}</label>
    @foreach ($filter->options() as $key => $optionValue)
      <div class="form-check">
        <input type="checkbox" name="{{ $filter->class() }}[]" class="form-check-input" id="{{$filter->class()}}_{{$optionValue}}" value="{{$optionValue}}" @if($value != null && in_array($optionValue,$value)) checked @endif>
        <label class="form-check-label check" for="{{$filter->class()}}_{{$optionValue}}">{{$key}}</label>
      </div>
    @endforeach
</div>
