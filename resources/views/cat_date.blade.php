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
                @foreach($out as $cat => $stats)
                    <tr>
                    <th rowspan="2">{{ $loop->iteration }}</th>
                    <th rowspan="2">{{ $cat }}</th>
                        <td></td>
                        @for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++)
                            <td>{{ isset($stats[$i])?$stats[$i]['posts']:0 }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td></td>
                        @for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++)
                            <td>{{ isset($stats[$i])?$stats[$i]['chars']:0  }}</td>
                        @endfor
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
