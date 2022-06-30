<?php

namespace Wdelfuego\Nova\Actions;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use Laravel\Nova\Nova;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Actions\Action as NovaAction;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Fields\ActionFields;

abstract class ProgrammaticalAction extends Action
{
    public static function createCustomActionEventsForModels(ActionRequest $request, NovaAction $action, $batchId, Collection $models, $status = 'running')
    {
        $models = $models->map(function ($model) use ($request, $action, $batchId, $status) {
            return array_merge(
                ActionEvent::defaultAttributes($request, $action, $batchId, $status),
                static::customEventAttributes($request, $action, $model)
            );
        });

        $models->chunk(50)->each(function ($models) {
            ActionEvent::insert($models->all());
        });

        ActionEvent::prune($models);
    }
    
    public static function customEventAttributes(ActionRequest $request, NovaAction $action, Model $model)
    {
        return [
            'actionable_id' => $request->actionableKey($model),
            'target_id' => $request->targetKey($model),
            'model_id' => $model->getKey(),
            'fields' => '',
            'original' => json_encode(array_intersect_key($model->getRawOriginal(), $model->getDirty())),
            'changes' => json_encode($model->getDirty()),
        ];
    }
    
    public $withoutActionEvents = true;
    public $withoutCustomActionEvents = false;
    
    protected $request = null;        
    
    abstract public function novaResourceClass() : string;
    
    public function executeOnModel(NovaRequest $request, Model $model)
    {
        return $this->executeOnModels($request, collect([$model]));
    }
    
    public function executeOnModels(NovaRequest $request, Collection $models)
    {
        $this->request = ProgrammaticalActionRequest::createFrom($request);
        $this->request->setAction($this);
        $this->request->setResourceClass($this->novaResourceClass());
        $this->request->setModels($models);

        $this->handleRequest($this->request);
        
        // This *must* happen after handleRequest, otherwise the batch id isn't set
        $batchId = $this->actionBatchId;
        if(!$batchId)
        {
            throw new \Exception("Action did not execute: " .static::class);
        }
        else
        {
            Nova::usingActionEvent(function ($actionEvent) use ($batchId, $models) {
                if (!$this->withoutCustomActionEvents) {
                    $this->createCustomActionEventsForModels(
                        $this->request, $this, $batchId, $models, 'finished'
                    );
                }
            });
        }
        
        return $models;
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        throw new \Exception("You need to implement the handle() method in your ProgrammaticalAction subclass");
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
        ];
    }
}
