@extends('partial.crud.index', [
    'pageTitle' => __('messages.Issues')
])

@section('contentTableControl')
@endsection

@section('contentTableFilter')
    @include('gitpab.issue.index_filter_form')
@endsection

@section('contentTable')
    @include('gitpab.issue.index_table', [
        'columnTitleName' => 'title'
    ])
@endsection

