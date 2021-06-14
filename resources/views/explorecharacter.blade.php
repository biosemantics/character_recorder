@extends('layouts.app')

@section('content')
    <router-view :user="{{ Auth::user()->toJson() }}"></router-view>
@endsection
