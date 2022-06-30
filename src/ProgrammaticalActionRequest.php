<?php

namespace Wdelfuego\Nova\Actions;

use Closure;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Actions\Action;

class ProgrammaticalActionRequest extends ActionRequest
{
    public static function forActionAndResourceClass(Action $action, string $resourceClass)
    {
        $out = new static();
        $out->setAction($action);
        $out->setResourceClass($resourceClass);
        return $out;
    }

    private $action = null;
    private $resourceClass = null;
    private $models = null;
    
    public function setAction(Action $action)
    {
        $this->action = $action;
    }
    
    public function setResourceClass(string $resourceClass)
    {
        $this->resourceClass = $resourceClass;
    }
    
    public function resource()
    {
        return $this->resourceClass;
    }
    
    public function model()
    {
        return $this->resourceClass::newModel();
    }
    
    public function setModels(Collection $collection)
    {
        $this->models = $collection;
    }
    
    public function action()
    {
        return !is_null($this->action) ? $this->action : abort(500);
    }

    protected function resolveActions()
    {
        return collect([$this->action]);
    }

    protected function availableActions()
    {
        return collect([$this->action]);
    }

    protected function actionExists()
    {
        return $this->action instanceof Action;
    }

    public function isPivotAction()
    {
        return false;
    }

    public function toSelectedResourceQuery()
    {
        throw new \Exception("Not supported");
    }

    protected function modelsViaRelationship()
    {
        throw new \Exception("Not supported");
    }

    public function chunks($count, Closure $callback)
    {
        if(!$this->models instanceof Collection)
        {
            throw new \Exception("Manual action requests require a model collection");
        }
        
        $output = [];

        $this->models
            ->chunk($count)
            ->each(function ($chunk) use ($callback, &$output) {
                $output[] = $callback($this->mapChunk($chunk));
            });

        return $output;
    }

}
