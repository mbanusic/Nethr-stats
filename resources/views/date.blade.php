@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <table class="table table-bordered table-striped table-responsive">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Ime</th>
                    <th>Total</th>
                    @for($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++)
                        <th>{{ $i }}. {{ $month }}. {{ $year }}.</th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                    <th rowspan="2">{{ $loop->iteration }}</th>
                    <th rowspan="2">{{ $user->name }}</th>
                        <td>{{ $user->stats->where('month', $month)->sum('posts') }}</td>
                        @for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++)
                            <td>{{ $user->stats->where('month', $month)->where('day', $i)->sum('posts') }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>{{ $user->stats->where('month', $month)->sum('chars') }}</td>
                        @for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++)
                            <td>{{ $user->stats->where('month', $month)->where('day', $i)->sum('chars') }}</td>
                        @endfor
                    </tr>
                @endforeach
                <tr>
                    <th rowspan="2"></th>
                    <th rowspan="2">Ukupno</th>
                    <td>{{ $total_post->sum(function($item) {
                    return $item->sum();
                    }) }}</td>
                    @for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++)
                        <td>{{ $total_post[$i]->sum() }}</td>
                    @endfor
                </tr>
                <tr>
                    <td>{{ $total_char->sum(function($item) {
                    return $item->sum();
                    }) }}</td>
                    @for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++)
                        <td>{{ $total_char[$i]->sum() }}</td>
                    @endfor
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
