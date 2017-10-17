@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <table class="table table-bordered table-striped table-responsive">
            <thead>
            <tr>
                <th>#</th>
                <th>Ime</th>
                <th>Email</th>
                <th>Objave / Znakovi</th>
                @foreach($months as $month)
                    <th><a href="{{ url(sprintf('%02d%d', $month, 2017)) }}">{{ date('M', mktime(0, 0, 0, $month)) }}</a> / <a href="{{ url('cat/'.sprintf('%02d%d', $month, 2017)) }}">Cat</a></th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td rowspan="2">{{ $loop->iteration }}</td>
                    <td rowspan="2">{{ $user->name }}</td>
                    <td rowspan="2">{{ $user->email }}</td>
                    <td>{{ $user->stats->sum('posts') }}</td>

                    @foreach($months as $month)
                        <td>{{ $user->stats->where('month', $month)->sum('posts') }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td>{{ $user->stats->sum('chars') }}</td>
                    @foreach($months as $month)
                        <td>{{ $user->stats->where('month', $month)->sum('chars') }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection