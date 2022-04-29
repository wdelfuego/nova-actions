<?php

namespace Wdelfuego\Nova\Actions;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Actions\ActionResource as NovaActionResource;
use Laravel\Nova\Fields\DateTime as NovaDateTimeField;
use Wdelfuego\Nova\DateTime\Fields\DateTime as CustomDateTimeField;

class ActionResource extends NovaActionResource
{
    public function fields(NovaRequest $request)
    {
        return array_map(function ($field) {
            return (CustomDateTimeField::$globalFormat && $field instanceof NovaDateTimeField)
                ? $field->withDateFormat(CustomDateTimeField::$globalFormat)
                : $field;
        }, parent::fields($request));
    }
}
