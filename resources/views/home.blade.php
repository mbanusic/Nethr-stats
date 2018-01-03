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
                @foreach([2017,2018] as $year)
                    @foreach($months as $month)
                        <th><a href="{{ url(sprintf('%02d%d', $month, $year)) }}">{{ date('M', mktime(0, 0, 0, $month)) }} {{ $year }}</a> / <a href="{{ url('cat/'.sprintf('%02d%d', $month, $year)) }}">Cat</a></th>
                    @endforeach
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
                    @foreach([2017,2018] as $year)
                        @foreach($months as $month)
                            <td>{{ $user->stats->where('month', $month)->where('year', $year)->sum('posts') }}</td>
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <td>{{ $user->stats->sum('chars') }}</td>
                    @foreach([2017,2018] as $year)
                        @foreach($months as $month)
                            <td>{{ $user->stats->where('month', $month)->where('year', $year)->sum('chars') }}</td>
                        @endforeach
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection