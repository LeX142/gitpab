@extends('partial.table.base')

@php
$columnTitleName = isset($columnTitleName) ? $columnTitleName : 'name';
$columnTitleLabel = isset($columnTitleLabel) ? $columnTitleLabel : __('messages.Title');
$orderLinkParams = $request->all();
unset($orderLinkParams['submit']);
@endphp

@section('tableThead')
    <tr>
        @include('partial.table.thcell', [
            'column' => 'id',
            'label' => __('messages.ID'),
            'order' => $order,
            'orderDirection' => $orderDirection,
            'orderLinkRoute' => $indexRoute,
            'orderLinkParams' => $orderLinkParams,
        ])

        @include('partial.table.thcell', [
            'column' => 'iid',
            'label' => __('messages.Number'),
            'order' => $order,
            'orderDirection' => $orderDirection,
            'orderLinkRoute' => $indexRoute,
            'orderLinkParams' => $orderLinkParams,
        ])

        @include('partial.table.thcell', [
            'column' => $columnTitleName,
            'label' => $columnTitleLabel,
            'order' => $order,
            'orderDirection' => $orderDirection,
            'orderLinkRoute' => $indexRoute,
            'orderLinkParams' => $orderLinkParams,
        ])

        @include('partial.table.thcell', [
            'column' => 'project.path_with_namespace',
            'label' => __('messages.Namespace'),
            'order' => $order,
            'orderDirection' => $orderDirection,
            'orderLinkRoute' => $indexRoute,
            'orderLinkParams' => $orderLinkParams,
        ])

        @include('partial.table.thcell', [
            'column' => 'project.name',
            'label' => __('messages.Project'),
            'order' => $order,
            'orderDirection' => $orderDirection,
            'orderLinkRoute' => $indexRoute,
            'orderLinkParams' => $orderLinkParams,
        ])

        @include('partial.table.thcell', [
            'column' => 'estimate',
            'label' => __('messages.Estimate'),
        ])

        @include('partial.table.thcell', [
            'column' => 'spent',
            'label' => __('messages.Spent time'),
        ])

        @include('partial.table.thcell', [
            'column' => 'gitlab_created_at',
            'label' => __('messages.Created At'),
            'order' => $order,
            'orderDirection' => $orderDirection,
            'orderLinkRoute' => $indexRoute,
            'orderLinkParams' => $orderLinkParams,
        ])
    </tr>
@endsection
@section('tableTbody')
    @forelse ($itemsList->items() as $key => $item)
        <tr>
            <td class="col-md-1">{{ $item->id }}</td>
            <td class="col-md-1">
                <a href="{{ $item->web_url }}">{{ $item->iid }}</a>
            </td>
            <td class="col-md-1">
                <a href="{{ route($showRoute, [$item->id]) }}">
                    {{ (isset($columnTitleName)) ? $item->{$columnTitleName} : $item->title }}
                </a>
            </td>
            <td class="col-md-2">
                {{ $item->project->path_with_namespace ?? $item->group->name ?? null }}
            </td>
            <td class="col-md-2">
                {{ $item->project->name ?? $item->project->path_with_namespace ?? null }}
            </td>
            <td class="col-md-1">
                {{ $item->estimate }}
            </td>
            <td class="col-md-1">
                {{ $item->spent }}
            </td>
            <td class="col-md-2">
                {{ \App\Helper\Date::formatDateTime($item->gitlab_created_at) }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="col-md-12">@lang('messages.Data not found')</td>
        </tr>
    @endforelse
@endsection
