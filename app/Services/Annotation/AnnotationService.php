<?php namespace Jakten\Services\Annotation;

use Auth;
use Jakten\Models\Annotation;
use Jakten\Repositories\Contracts\AnnotationRepositoryContract;
use Jakten\Services\ModelService;

/**
 * Class AnnotationService
 * @package Jakten\Services\Annotation
 */
class AnnotationService
{
    /**
     * @var AnnotationRepositoryContract
     */
    private $annotations;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * AnnotationService constructor.
     *
     * @param AnnotationRepositoryContract $annotations
     * @param ModelService $modelService
     */
    public function __construct(AnnotationRepositoryContract $annotations, ModelService $modelService)
    {
        $this->annotations = $annotations;
        $this->modelService = $modelService;
    }

    /**
     * Creates a new annotation.
     *
     * @return Annotation
     **/
    public function create($message, $type = null, $data = null)
    {
        $annotation = $this->modelService->createModel(Annotation::class, [
            'user_id' => Auth::user()->id,
            'type' => $type,
            'message' => $message,
            'data' => $data
        ]);

        $annotation->save();
        return $annotation;
    }
}
