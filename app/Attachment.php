<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use PhpImap\IncomingMail;

class Attachment extends BaseModel
{
    public function attachable()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->morphTo();
    }

    public static function storeAttachmentFromRequest($request, $attachable)
    {
        $files = $request->file('attachment');

        $class = (new \ReflectionClass($attachable))->getShortName();

        $start = strpos($attachable->title, 'ID ');
        $idtext = '';

        if ($start !== false) {
            $len = strpos($attachable->title, ' -');
            $idtext = substr($attachable->title, 0, $len);
        }

        $user = \Auth::user();
        $causer = $user ? $user : $attachable->requester;

        foreach ($files as $file) {
            $id = $idtext ? ' - [' . $idtext . ']' : '';
            $path = $attachable->reference_number . $id . ' - ' . str_replace(' ', '_', $file->getClientOriginalName());
            Storage::putFileAs(strtolower($class) . '_'.$attachable->id . '/', $file, $path);
            
            $attachment = $attachable->attachments()->create(['path' => $path]);

            $attachment->causer()->associate($causer)->save();
        }
    }

    /**
     * @param IncomingMail $mail
     * @param $attachable
     */
    public static function storeAttachmentsFromEmail($mail, $attachable)
    {
        foreach ($mail->getAttachments() as $mailAttachment) {
            $path = str_replace(' ', '_', $attachable->id.'_'.$mailAttachment->name);
            Storage::put('public/attachments/'.$path, file_get_contents($mailAttachment->filePath));
            $attachable->attachments()->create(['path' => $path]);
            unlink($mailAttachment->filePath);
        }
    }
}
