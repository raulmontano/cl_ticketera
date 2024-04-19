
  <div class="form-row justify-content-md-center">
    <div class="form-group col-md-6">

      <div class="form-row">
        <label for="title">{{ __('ticket.subject') }}</label>
        <input name="title" id="title" class="form-control" required value="{{ old('title') ? old('title') : (request()->has('title') ? request()->get('title') : '' )}}"/>
      </div>

      <div class="form-row">
        <label for="body">{{ __('ticket.body') }}</label>
        <textarea  name="body" class="w100" required>{{ old('body') ? old('body') : (request()->has('body') ? request()->get('body') : '' )}}</textarea>
      </div>
    </div>
  </div>

    <div class="form-row justify-content-md-center">
        <div class="form-group col-md-3">
          <label for="channels">{{ trans_choice('ticket.channels', 1)}}</label>
          @foreach(\App\Channel::all() as $i => $channel)
          <div class="form-check">
              <input name="channels[]" id="channel_{{ $channel->id }}" type="checkbox" @if( (!old('channels') && $i== 0) || (old('channels') && in_array($channel->name,old('channels')))) checked @endif class="form-check-input" value="{{ $channel->name }}" />
               <label class="form-check-label check" for="channel_{{ $channel->id }}">
                 {{ $channel->name }}
              </label>
          </div>
          @endforeach
        </div>

        <div class="form-group col-md-3">
          <label for="categories">{{ trans_choice('ticket.categories', 1)}}</label>
          @foreach(\App\Category::all() as $category)
            <div class="form-check">
              <input name="categories[]" class="form-check-input" @if(old('categories') && in_array($category->name,old('categories'))) checked @endif id="category_{{ $category->id }}" type="checkbox" value="{{ $category->name }}" />
              <label class="form-check-label check" for="category_{{ $category->id }}">
                {{ $category->name }}
              </label>
            </div>
          @endforeach
        </div>

        <div class="form-group col-md-3">

          <select name="company" required class="custom-select">
            <option value='' @if(!old('company')) selected @endif>-- {{ trans_choice('ticket.company', 2)}} --</option>
              @foreach(\App\TicketCompany::all() as $company)
                  <option value="{{$company->id}}" @if(old('company') && old('company') == $company->id) selected @endif>{{ $company->name }}</option>
              @endforeach
          </select>

          <select name="type" required class="custom-select">
            <option value=''>-- {{ trans_choice('ticket.type', 2)}} --</option>
              @foreach(\App\TicketType::all() as $type)
                  <option value="{{$type->id}}" @if(old('type') && old('type') == $type->id) selected @endif>{{ $type->name }}</option>
              @endforeach
          </select>

          <select name="post_type" required class="custom-select">
            <option value=''>-- {{ trans_choice('ticket.postType', 2)}} --</option>
              @foreach(\App\TicketPostType::all() as $postType)
                  <option value="{{$postType->id}}" @if(old('post_type') && old('post_type') == $postType->id) selected @endif>{{ $postType->name }}</option>
              @endforeach
          </select>

        </div>

      </div>

      <div class="form-row justify-content-md-center">
        <div class="form-group col-md-6">


          <div class="form-row">
            @include('components.uploadAttachment', ["type" => "tickets"])
          </div>

          <div class="form-row">
            <button class="ph3 ml1 btn btn-primary">{{ __('ticket.newTicket') }}</button>
          </div>

        </div>
      </div>
