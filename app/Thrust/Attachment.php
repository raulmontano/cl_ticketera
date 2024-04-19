<?php

namespace App\Thrust;

use BadChoice\Thrust\Fields\Text;
use BadChoice\Thrust\Resource;
use BadChoice\Thrust\ResourceGate;

class Attachment extends Resource
{
    public static $model  = \App\Attachment::class;
    public static $search = ['name'];

    public function fields()
    {
        return [
            Text::make('path'),
        ];
    }

    public function delete($id)
    {

      $object = is_numeric($id) ? $this->find($id) : $id;
      app(ResourceGate::class)->check($this, 'delete', $object);
      $this->canBeDeleted($object);

      //REMOVE FROM STORAGE
      $class = (new \ReflectionClass($object->attachable))->getShortName();
      $pathFile = strtolower($class) . '_'.$object->attachable->id . '/' . $object->path;
      \Storage::delete($pathFile);

      $this->prune($object);
      return $object->delete();

    }

}
