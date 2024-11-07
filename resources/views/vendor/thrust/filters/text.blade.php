@php

  if(method_exists($filter,'getCssDiv')){
    $divCss = $filter->getCssDiv();
  } else {
    $divCss = 'col-md-4';
  }

@endphp

<div class="form-group {{ $divCss }} float-left">
  <div class="form-row">
    <label> {!! $filter->getIcon() !!} {!! $filter->getTitle() !!}</label>
  <input name="{{ $filter->class() }}"
         title="{{$filter->getTitle()}}"
         value="{{$value}}"
         type="text"
         placeholder="{{$filter->getTitle()}}" style="width:100%">
       </div>
</div>
