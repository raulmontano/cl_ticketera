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
            Text::make('attachments.created_at', __('ticket.created_at'))->displayWith(function ($attachment) {
                return $attachment->created_at->format('Y-m-d H:i:s');
            }),
            Text::make('path', 'Documento'),

            Text::make('attachments.id', 'Agregado por')->displayWith(function ($attachment) {
                return $attachment->causer ? $attachment->causer->name . ' ('.$attachment->causer->email.')' : '';
            }),

            Link::make('attachments.id', '')->displayCallback(function ($attachment) {
                return '<i class="fa fa-download"></i>';
            })->route('attachments'),

        ];

        $isEditor = false;

        if (auth()->user()->teams()->count()) {
            //
            $isEditor = (auth()->user()->teams()->first()->id == 1);
        } else {
            //
        }

        if ($isEditor) {
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

    public function delete($id)
    {

      //REMOVE FROM STORAGE
        $class = (new \ReflectionClass($id->attachable))->getShortName();
        $pathFile = strtolower($class) . '_'.$id->attachable->id . '/' . $id->path;
        \Storage::delete($pathFile);

        return parent::delete($id);
    }

    public function filters()
    {
        return [
            new TicketAttachmentFilter(),
            new TicketAttachmentReferenceNumberFilter(),
        ];
    }

    public function actions()
    {
        $actions = parent::actions();

        if (auth()->user()->teams()->count()) {
            //auditor cant delete files

            if (auth()->user()->teams()->first()->id == 3) {
                $actions = [];
            }
        } else {
            //
        }

        return $actions;
    }

    public function update($id, $newData)
    {
        return false;
    }

    public function canDelete($object)
    {
        $isAuditor = false;

        if (auth()->user()->teams()->count()) {
            //auditor cant delete files
            $isAuditor = (auth()->user()->teams()->first()->id == 3);
        }

        return $isAuditor ? false :true;
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
