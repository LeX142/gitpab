<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormRequest;
use App\Model\Service\Eloquent\EloquentProjectService;
use App\Providers\AppServiceProvider;
use App\Model\Repository\NamespacesRepositoryEloquent;

class ProjectController extends CrudController
{
    /**
     * @return EloquentProjectService
     */
    protected function getService()
    {
        return app(AppServiceProvider::ELOQUENT_PROJECT_SERVICE);
    }

    protected function prepareDataForShow(FormRequest $request, array $data)
    {
        /** @var EloquentProjectService $service */
        $service = app(AppServiceProvider::ELOQUENT_PROJECT_SERVICE);
        /** @var NamespacesRepositoryEloquent $namespacesRepository */
        $namespacesRepository = app(AppServiceProvider::NAMESPACES_REPOSITORY);

        $id = $this->getRouteParamId($request);
        return array_merge(
            $data,
            [
                'contributorAmountList' => $service->getContributorsAmounts($id),
                'namespaceList' => $namespacesRepository->getItemsForSelect(null, null, 'id', 'name'),
            ]
        );
    }

}
