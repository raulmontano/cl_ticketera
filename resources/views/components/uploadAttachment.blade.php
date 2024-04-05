
<div class="form-group">
<div class="input-group mb-2 mt-2">
    <div class="custom-file" id="upload-attachment">
        {{ Form::file('attachment[]', ["id" => "attachment", "multiple"]) }}
    </div>
</div>
<small id="attachmentHelpBlock" class="form-text text-muted">
  {{ __('ticket.attachFileLeyend') }}
</small>
</div>
