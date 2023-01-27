<?php

namespace App\Model\Repository;

use App\Model\Entity\Spent;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class SpentRepositoryEloquent extends RepositoryAbstractEloquent
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Spent::class;
    }

    public function getListQuery(array $parameters): Builder
    {
        $query = parent::getListQuery($parameters)
            ->select([
                'spent.note_id',
                'spent.hours',
                'note.gitlab_created_at',
                'spent.spent_at',
                'issue.iid as issue',
                DB::raw("issue.title || CASE WHEN spent.description IS NOT NULL THEN ' |\n' || spent.description ELSE '' END as description")
            ])
            ->join('note', 'note.id', '=', 'spent.note_id')
            ->join('contributor', 'contributor.id', '=', 'note.author_id')
            ->join('issue', 'issue.id', '=', 'note.issue_id')
            ->join('project', 'project.id', '=', 'issue.project_id')
            ->join('namespace', 'namespace.id', '=', 'project.namespace_id');

        if ($issueId = Arr::get($parameters, 'issue_id')) {
            $query->where('note.issue_id', '=', $issueId);
        }

        if ($issueIid = Arr::get($parameters, 'issue_iid')) {
            $query->where('issue.iid', '=', $issueIid);
        }

        if ($projectIds = Arr::get($parameters, 'projects')) {
            $query->whereIn('issue.project_id', $projectIds);
        }

        if ($namespaceIds = Arr::get($parameters, 'namespaces')) {
            $query->whereIn('project.namespace_id', $namespaceIds);
        }

        if ($authorIds = Arr::get($parameters, 'authors')) {
            $query->whereIn('note.author_id', $authorIds);
        }

        if ($id = Arr::get($parameters, 'id')) {
            $query->where('note.id', '=', $id);
        }

        if ($dateStart = Arr::get($parameters, 'date_start')) {
            $date = new \DateTime($dateStart);
            //$query->where('note.gitlab_created_at', '>=', $date->format('Y-m-d'));
            $query->where('spent.spent_at', '>=', $date->format('Y-m-d'));
        }

        if ($dateEnd = Arr::get($parameters, 'date_end')) {
            $date = new \DateTime($dateEnd);
            $date->add(new \DateInterval('P1D'));
            //$query->where('note.gitlab_created_at', '<', $date->format('Y-m-d'));
            $query->where('spent.spent_at', '<=', $date->format('Y-m-d'));
        }

        if ($labels = Arr::get($parameters, 'labels')) {
            $labelsString = implode("'::character varying, '", $labels);
            $labelsString = "'$labelsString'::character varying";
            $query->whereRaw("issue.labels @> array[$labelsString]");
        }

        if ($milestoneIds = Arr::get($parameters, 'milestones')) {
            $query->whereIn('issue.milestone_id', $milestoneIds);
        }

        return $query;
    }

    public function getTNMListQuery(array $parameters): Builder
    {
        $query = $this->getListQuery($parameters);
        $query = $query->select([
            DB::raw("date_trunc('second', max(spent.spent_at)) as spent_at"),
            DB::raw("project.path_with_namespace as project"),
            'issue.title as title',
            'issue.closed_at as closed_at',
            DB::raw('sum(spent.hours) as hours'),
        ])->groupBy([
            'issue.iid',
            'issue.title',
            'issue.closed_at',
            'project.path_with_namespace',
            'namespace.name'
        ])->orderBy('issue.iid');
        return $query;
    }

    public function stat($parameters): Collection
    {
        /** @var Builder $query */
        $query = $this->model;

        $query = $query
            ->select([
                'note.gitlab_created_at',
                'project.path_with_namespace as project',
                'issue.iid',
                'issue.title as issue_title',
                'spent.hours',
                'spent.description as note_description',
            ])
            ->join('note', 'note.id', '=', 'spent.note_id')
            ->join('issue', 'issue.id', '=', 'note.issue_id')
            ->join('project', 'project.id', '=', 'issue.project_id');

        if ($dateStart = Arr::get($parameters, 'date_start')) {
            $query->where('note.gitlab_created_at', '>=', $dateStart);
        }

        if ($dateFinish = Arr::get($parameters, 'date_finish')) {
            $query->where('note.gitlab_created_at', '<=', $dateFinish);
        }

        if ($userId = Arr::get($parameters, 'user_id')) {
            $query->where('note.author_id', '=', $userId);
        }

        if ($projectId = Arr::get($parameters, 'project_id')) {
            $query->where('issue.project_id', '=', $projectId);
        }

        if ($issueId = Arr::get($parameters, 'issue_id')) {
            $query->where('note.issue_id', '=', $issueId);
        }

        if ($order = Arr::get($parameters, 'order')) {
            $query->orderBy($order);
        }

        $query
            ->orderBy('issue.iid', 'asc')
            ->orderBy('note.gitlab_created_at', 'asc');

        return $query->get();
    }

    /**
     * @return float hours
     */
    public function sum(): float
    {
        return $this->model
            ->selectRaw('sum(hours) as hours')->get()->first()->hours ?: 0;
    }

    public function getTNMLabelsListQuery($parameters)
    {
        // Apply standard filters
        $query = $this->getListQuery($parameters);

        // Translate labels array into table (like join labels)
        $query = $query->select([
            DB::raw('unnest(issue.labels) as label'),
            DB::raw('sum(spent.hours) as hours'),
        ])
            ->groupBy('label');

        // Wrap $query to join "label" table for access to description
        $query = $this->model
            ->select(['t.*', 'label.description'])
            ->from(DB::raw("({$query->toSql()}) as t"))
            ->mergeBindings($query->getQuery())
            ->join('label', 'label.name', '=', 't.label')
            ->orderBy('label');

        return $query;
    }

}
