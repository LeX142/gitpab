<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TimeListRequest;
use App\Model\Repository\NamespacesRepositoryEloquent;
use App\Model\Repository\ContributorRepositoryEloquent;
use App\Model\Repository\LabelRepositoryEloquent;
use App\Model\Repository\MilestoneRepositoryEloquent;
use App\Model\Repository\ProjectRepositoryEloquent;
use App\Model\Service\Eloquent\EloquentSpentService;
use App\Providers\AppServiceProvider;

class TimeController extends CrudController
{
    protected $requestMap = [
        'index' => TimeListRequest::class,
    ];

    /**
     * @return EloquentSpentService
     */
    protected function getService()
    {
        return app(AppServiceProvider::ELOQUENT_SPENT_SERVICE);
    }

    public function index(FormRequest $request)
    {

        if ($request->get('submit') === 'act_tnm.csv') {
            $data = $this->getService()->getTNMList($request->all());
            return $this->downloadCsv($data);
        }
        if ($request->get('submit') === 'act_tnm_labels.csv') {
            $data = $this->getService()->getTNMLabelsList($request->all());
            return $this->downloadCsv($data);
        }
        return parent::index($request);
    }

    protected function prepareDataForIndex(FormRequest $request, array $data)
    {
        /** @var ContributorRepositoryEloquent $contributorRepository */
        $contributorRepository = app(AppServiceProvider::CONTRIBUTOR_REPOSITORY);

        /** @var ProjectRepositoryEloquent $projectRepository */
        $projectRepository = app(AppServiceProvider::PROJECT_REPOSITORY);

        /** @var LabelRepositoryEloquent $labelRepository */
        $labelRepository = app(AppServiceProvider::LABEL_REPOSITORY);

        /** @var MilestoneRepositoryEloquent $milestoneRepository */
        $milestoneRepository = app(AppServiceProvider::MILESTONE_REPOSITORY);

        /** @var NamespacesRepositoryEloquent $namespacesRepository */
        $namespacesRepository = app(AppServiceProvider::NAMESPACES_REPOSITORY);

        $totalTime = $this->getService()->getTotalTime($request->all());

        return array_merge(
            $data,
            [
                'authorsList' => $contributorRepository->getItemsForSelect(),
                'projectsList' => $projectRepository->getItemsForSelect(),
                'labelList' => $labelRepository->getItemsForSelect(null, null, 'name'),
                'milestoneList' => $milestoneRepository->getItemsForSelect(null, null, 'id', 'title'),
                'namespaceList' => $namespacesRepository->getItemsForSelect(null, null, 'id', 'name'),
                'total' => [
                    'time' => $totalTime,
                ],
            ]
        );
    }
}
