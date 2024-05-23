<?php

namespace App\Thrust;

use App\Repositories\TicketAttachmentsIndexQuery;
use BadChoice\Thrust\Fields\Link;
use BadChoice\Thrust\Fields\Color;
use BadChoice\Thrust\Fields\Text;
use BadChoice\Thrust\Fields\Delete;
use BadChoice\Thrust\Resource;
use App\ThrustHelpers\Filters\TicketAttachmentFilter;
use App\ThrustHelpers\Filters\TicketAttachmentReferenceNumberFilter;
use App\ThrustHelpers\Actions\TicketAttachmentsSearchInfo;

class TicketAttachment extends Resource
{
    public static $model  = \App\Attachment::class;
    public static $search = ['path'];

    public function fields()
    {
        $fields = [
            Text::make('attachments.id', 'Solicitud')->displayWith(function ($attachment) {
              return '<a href="'.route('tickets.show',$attachment->attachable_id).'">'.$attachment->attachable->reference_number . ' - '. $attachment->attachable->title.'</a>';
            }),

            Text::make('attachments.created_at', __('ticket.created_at'))->displayWith(function ($attachment) {
                return $attachment->created_at->format('Ymd_Hi');
            }),
            Text::make('path','Documento'),

            Link::make('attachments.id','')->displayCallback(function ($attachment) {
                return '<i class="fa fa-download"></i>';
            })->route('attachments'),

        ];

        $isEditor = false;

        if(auth()->user()->teams()->count()){
          //
          $isEditor = (auth()->user()->teams()->first()->id == 1);
        } else {
          //
        }

        if($isEditor){
          $fields[] = Delete::make('delete');
        }

        return $fields;
    }

    public function getFields()
    {
        return $this->fields();
    }

    public function mainActions()
    {
        return [new TicketAttachmentsSearchInfo()];
    }

    public function delete($id){

      //REMOVE FROM STORAGE
      $class = (new \ReflectionClass($id->attachable))->getShortName();
      $pathFile = strtolower($class) . '_'.$id->attachable->id . '/' . $id->path;
      \Storage::delete($pathFile);

      return parent::delete($id);
    }

    public function filters()
    {
        //return [];

        return [
            new TicketAttachmentFilter(),
            new TicketAttachmentReferenceNumberFilter(),
        ];
    }

    public function update($id, $newData)
    {
        return false;
    }

    public function canDelete($object)
    {
        return true;
    }

    public function canEdit($object)
    {
        return false;
    }

    protected function getBaseQuery()
    {
        return TicketAttachmentsIndexQuery::get()->with($this->getWithFields());
    }
}
