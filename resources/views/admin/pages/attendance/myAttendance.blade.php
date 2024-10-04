


@extends('admin.master')

@section('content')
<div class="shadow p-4 d-flex justify-content-between align-items-center ">
    <h4 class="text-uppercase">My Attendance Record</h4>
</div>
<div class="container my-5 py-5">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="input-group rounded w-50">
            <form action="{{ route('searchMyAttendance') }}" method="get">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search..." name="search">
                    <button type="submit" class="input-group-text border-0 bg-transparent" id="search-addon">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <a href="{{ route('myAttendanceReport') }}" class="btn btn-danger text-capitalize border-0"
            data-mdb-ripple-color="dark">Report</a>
    </div>

    <table class="table align-middle mb-4 text-center bg-white">
        <thead class="bg-light">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Month</th>
                <th>Duration</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Late Arrival</th>
                <th>Check Out</th>
                <th>Early Departure</th>
                <th>Overtime</th>
            </tr>
        </thead>
        {{-- <tbody>
            @foreach ($attendances as $key => $attendance)
            <tr>
                <td>
                    <div>
                        <p class="fw-bold mb-1">{{ $key + 1 }}</p>
                    </div>
                </td>
                <td>{{ $attendance->name }}</td>
                <td>{{ $attendance->month }}</td>
                <td>
                    @php
                        $duration_minutes = is_numeric($attendance->duration_minutes) ? $attendance->duration_minutes : 0;
                        $hours = floor($duration_minutes / 60);
                        $minutes = $duration_minutes % 60;
                    @endphp
                    {{ $hours }} hours {{ $minutes }} mins
                </td>
                <td>{{ $attendance->select_date }}</td>
                <td>{{ $attendance->check_in }}</td>
                <td>
                    @php
                        $late_minutes = is_numeric($attendance->late) ? $attendance->late : 0;
                        $lateHours = floor($late_minutes / 60);
                        $lateMinutes = $late_minutes % 60;
                    @endphp
                    @if($late_minutes > 0)
                        {{ $lateHours }} hours {{ $lateMinutes }} mins late
                    @else
                        On time
                    @endif
                </td>
                <td>{{ $attendance->check_out }}</td>
                <td>
                    @php
                        $overtime_minutes = is_numeric($attendance->overtime_minutes) ? $attendance->overtime_minutes : 0;
                        $overtimeHours = floor($overtime_minutes / 60);
                        $overtimeMinutes = $overtime_minutes % 60;
                    @endphp
                    {{ $overtimeHours }} hours {{ $overtimeMinutes }} mins
                </td>

            </tr>
            @endforeach
        </tbody> --}}
        <tbody>
            @foreach ($attendances as $key => $attendance)
            <tr>
                <td>
                    <div>
                        <p class="fw-bold mb-1">{{ $key + 1 }}</p>
                    </div>
                </td>
                <td>{{ $attendance->name }}</td>
                <td>{{ $attendance->month }}</td>
                <td>
                    @php
                        $duration_minutes = is_numeric($attendance->duration_minutes) ? $attendance->duration_minutes : 0;
                        $hours = floor($duration_minutes / 60);
                        $minutes = $duration_minutes % 60;
                    @endphp
                    {{ $hours }} hours {{ $minutes }} mins
                </td>
                <td>{{ $attendance->select_date }}</td>
                <td>{{ $attendance->check_in }}</td>
                <td>
                    @php
                        $late_minutes = is_numeric($attendance->late) ? $attendance->late : 0;
                        $lateHours = floor($late_minutes / 60);
                        $lateMinutes = $late_minutes % 60;
                    @endphp
                    @if($late_minutes > 0)
                        {{ $lateHours }} hours {{ $lateMinutes }} mins late
                    @else
                        On time
                    @endif
                </td>
                <td>{{ $attendance->check_out }}</td>
                <td>
                    @php
                        // Office end time (5:00 PM)
                        $regularEndTime = \Carbon\Carbon::createFromTime(17, 0, 0);
                        $checkOutTime = \Carbon\Carbon::parse($attendance->check_out);

                        // Check if the employee checked out early
                        if ($checkOutTime->lessThan($regularEndTime)) {
                            $earlyDepartureMinutes = $regularEndTime->diffInMinutes($checkOutTime);
                            $earlyHours = floor($earlyDepartureMinutes / 60);
                            $earlyMinutes = $earlyDepartureMinutes % 60;
                            echo "$earlyHours hours $earlyMinutes mins early";
                        } else {
                            echo "N/A";
                        }
                    @endphp
                </td>
                <td>
                    @php
                        $overtime_minutes = is_numeric($attendance->overtime_minutes) ? $attendance->overtime_minutes : 0;
                        $overtimeHours = floor($overtime_minutes / 60);
                        $overtimeMinutes = $overtime_minutes % 60;
                    @endphp
                    {{ $overtimeHours }} hours {{ $overtimeMinutes }} mins
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
    <div class="w-25 mx-auto">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
