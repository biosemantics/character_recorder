@extends('layouts.app')

@section('content')
    <router-view :user="{{ Auth::user()->toJson() }}" :list="{{ json_encode($list) }}"></router-view>
@endsection
