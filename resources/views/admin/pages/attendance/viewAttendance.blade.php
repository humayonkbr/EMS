{{-- @extends('admin.master')

@section('content')
<div class="shadow p-4 d-flex justify-content-between align-items-center">
    <h4 class="text-uppercase">View Attendance List</h4>
</div>
<div class="my-5 py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="input-group rounded w-50">
            <form action="{{ route('searchAttendanceReport') }}" method="get">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search..." name="search">
                    <button type="submit" class="input-group-text border-0 bg-transparent" id="search-addon">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <a href="{{ route('attendanceReport') }}" class="btn btn-danger text-capitalize border-0" data-mdb-ripple-color="dark">Report</a>
    </div>

    <table class="table align-middle mb-4 text-center bg-white">
        <thead class="bg-light">
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Duration</th>
                <th>Date</th>
                <th>Month</th>
                <th>Check In</th>
                <th>Late</th>
                <th>Check Out</th>
                <th>Overtime</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $key => $attendance)
            <tr>
                <td>
                    <div>
                        <p class="fw-bold mb-1">{{ $key + 1 }}</p>
                    </div>
                </td>
                <td>{{ $attendance->name }}</td>
                <td>{{ $attendance->department_name }}</td>
                <td>{{ $attendance->designation_name }}</td>
                <td>
                    @php
                        $duration_minutes = is_numeric($attendance->duration_minutes) ? $attendance->duration_minutes : 0;
                        $hours = floor($duration_minutes / 60);
                        $minutes = $duration_minutes % 60;
                    @endphp
                    {{ $hours }} hours {{ $minutes }} mins
                </td>
                <td>{{ \Carbon\Carbon::parse($attendance->select_date)->format('Y-m-d') }}</td>
                <td>{{ $attendance->month }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}</td>
                <td>
                    @php
                        $late_minutes = is_numeric($attendance->late) ? $attendance->late : 0;
                        $lateHours = floor($late_minutes / 60);
                        $lateMinutes = $late_minutes % 60;
                    @endphp
                    {{ $lateHours }} hours {{ $lateMinutes }} mins
                </td>
                <td>{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}</td>
                <td>
                    @php
                        $overtime_minutes = is_numeric($attendance->overtime_minutes) ? $attendance->overtime_minutes : 0;
                        $overtimeHours = floor($overtime_minutes / 60);
                        $overtimeMinutes = $overtime_minutes % 60;
                    @endphp
                    {{ $overtimeHours }} hours {{ $overtimeMinutes }} mins
                </td>
                <td>
                    <a class="btn btn-danger rounded-pill" href="{{ route('attendanceDelete', $attendance->id) }}">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="w-25 mx-auto">
        {{ $attendances->links() }}
    </div>
</div>
@endsection --}}

{{--

@extends('admin.master')

@section('content')
<div class="shadow p-4 d-flex justify-content-between align-items-center">
    <h4 class="text-uppercase">View Attendance List</h4>
</div>
<div class="my-5 py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="input-group rounded w-50">
            <form action="{{ route('searchAttendanceReport') }}" method="get">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search..." name="search">
                    <button type="submit" class="input-group-text border-0 bg-transparent" id="search-addon">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <a href="{{ route('attendanceReport') }}" class="btn btn-danger text-capitalize border-0" data-mdb-ripple-color="dark">Report</a>
    </div>

    <table class="table align-middle mb-4 text-center bg-white">
        <thead class="bg-light">
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Duration</th>
                <th>Date</th>
                <th>Month</th>
                <th>Check In</th>
                <th>Late Arrival</th>
                <th>Check Out</th>
                <th>Early Departure</th>
                <th>Overtime</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $key => $attendance)
            <tr>
                <td>
                    <div>
                        <p class="fw-bold mb-1">{{ $key + 1 }}</p>
                    </div>
                </td>
                <td>{{ $attendance->name }}</td>
                <td>{{ $attendance->department_name }}</td>
                <td>{{ $attendance->designation_name }}</td>
                <td>
                    @php
                        $duration_minutes = is_numeric($attendance->duration_minutes) ? $attendance->duration_minutes : 0;
                        $hours = floor($duration_minutes / 60);
                        $minutes = $duration_minutes % 60;
                    @endphp
                    {{ $hours }} hours {{ $minutes }} mins
                </td>
                <td>{{ \Carbon\Carbon::parse($attendance->select_date)->format('Y-m-d') }}</td>
                <td>{{ $attendance->month }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}</td>
                <td>
                    @php
                        $late_minutes = is_numeric($attendance->late) ? $attendance->late : 0;
                        $lateHours = floor($late_minutes / 60);
                        $lateMinutes = $late_minutes % 60;
                    @endphp
                    {{ $lateHours }} hours {{ $lateMinutes }} mins
                </td>
                <td>{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}</td>

                <td>{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}</td>
<td>
    @php
        $checkOutTime = \Carbon\Carbon::parse($attendance->check_out);
        $endOfWorkDay = \Carbon\Carbon::createFromTime(17, 0, 0); // Office end time is 5:00 PM

        $earlyDepartureMinutes = 0;
        if ($checkOutTime->lessThan($endOfWorkDay)) {
            // Calculate early departure in minutes
            $earlyDepartureMinutes = $endOfWorkDay->diffInMinutes($checkOutTime);
        }

        // Convert minutes to hours and minutes for display
        $earlyDepartureHours = floor($earlyDepartureMinutes / 60);
        $earlyDepartureMins = $earlyDepartureMinutes % 60;
    @endphp

    {{ $earlyDepartureHours }} hours {{ $earlyDepartureMins }} mins
</td>


                <td>
                    @php
                        $overtime_minutes = is_numeric($attendance->overtime_minutes) ? $attendance->overtime_minutes : 0;
                        $overtimeHours = floor($overtime_minutes / 60);
                        $overtimeMinutes = $overtime_minutes % 60;
                    @endphp
                    {{ $overtimeHours }} hours {{ $overtimeMinutes }} mins
                </td>
                <td>
                    <a class="btn btn-danger rounded-pill" href="{{ route('attendanceDelete', $attendance->id) }}">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="w-25 mx-auto">
        {{ $attendances->links() }}
    </div>
</div>
@endsection --}}




{{--
@extends('admin.master')

@section('content')
<div class="shadow p-4 d-flex justify-content-between align-items-center">
    <h4 class="text-uppercase">View Attendance List</h4>
</div>

<div class="my-5 py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="input-group rounded w-50">
            <form action="{{ route('searchAttendanceReport') }}" method="get">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search..." name="search">
                    <button type="submit" class="input-group-text border-0 bg-transparent" id="search-addon">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <a href="{{ route('attendanceReport') }}" class="btn btn-danger text-capitalize border-0" data-mdb-ripple-color="dark">Report</a>
    </div>

    <table class="table align-middle mb-4 text-center bg-white">
        <thead class="bg-light">
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Duration</th>
                <th>Date</th>
                <th>Month</th>
                <th>Check In</th>
                <th>Late Arrival</th>
                <th>Check Out</th>
                <th>Early Departure</th>
                <th>Overtime</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $key => $attendance)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $attendance->name }}</td>
                <td>{{ $attendance->department_name }}</td>
                <td>{{ $attendance->designation_name }}</td>
                <td>
                    @php
                        $duration_minutes = $attendance->duration_minutes;
                        $hours = floor($duration_minutes / 60);
                        $minutes = $duration_minutes % 60;
                    @endphp
                    {{ $hours }} hours {{ $minutes }} mins
                </td>
                <td>{{ \Carbon\Carbon::parse($attendance->select_date)->format('Y-m-d') }}</td>
                <td>{{ $attendance->month }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}</td>
                <td>
                    @php
                        $late_minutes = $attendance->late;
                        $lateHours = floor($late_minutes / 60);
                        $lateMinutes = $late_minutes % 60;
                    @endphp
                    {{ $lateHours }} hours {{ $lateMinutes }} mins
                </td>
                <td>{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}</td>
                <td>
                    @php
                        $earlyDepartureMinutes = $attendance->early_departure_minutes;
                        $earlyDepartureHours = floor($earlyDepartureMinutes / 60);
                        $earlyDepartureMins = $earlyDepartureMinutes % 60;
                    @endphp
                    {{ $earlyDepartureHours }} hours {{ $earlyDepartureMins }} mins
                </td>
                <td>
                    @php
                        $overtime_minutes = $attendance->overtime_minutes;
                        $overtimeHours = floor($overtime_minutes / 60);
                        $overtimeMinutes = $overtime_minutes % 60;
                    @endphp
                    {{ $overtimeHours }} hours {{ $overtimeMinutes }} mins
                </td>
                <td>
                    <a class="btn btn-danger rounded-pill" href="{{ route('attendanceDelete', $attendance->id) }}">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="w-25 mx-auto">
        {{ $attendances->links() }}
    </div>
</div>
@endsection --}}



@extends('admin.master')

@section('content')
<div class="shadow p-4 d-flex justify-content-between align-items-center">
    <h4 class="text-uppercase">View Attendance List</h4>
</div>

<div class="my-5 py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="input-group rounded w-50">
            <form action="{{ route('searchAttendanceReport') }}" method="get">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search..." name="search">
                    <button type="submit" class="input-group-text border-0 bg-transparent" id="search-addon">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <a href="{{ route('attendanceReport') }}" class="btn btn-danger text-capitalize border-0" data-mdb-ripple-color="dark">Report</a>
    </div>

    <table class="table align-middle mb-4 text-center bg-white">
        <thead class="bg-light">
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Duration</th>
                <th>Date</th>
                <th>Month</th>
                <th>Check In</th>
                <th>Late Arrival</th>
                <th>Check Out</th>
                <th>Early Departure</th>
                <th>Overtime</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $key => $attendance)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $attendance->name }}</td>
                <td>{{ $attendance->department_name }}</td>
                <td>{{ $attendance->designation_name }}</td>
                <td>
                    @php
                        $duration_minutes = $attendance->duration_minutes;
                        $hours = floor($duration_minutes / 60);
                        $minutes = $duration_minutes % 60;
                    @endphp
                    {{ $hours }} hours {{ $minutes }} mins
                </td>
                <td>{{ \Carbon\Carbon::parse($attendance->select_date)->format('Y-m-d') }}</td>
                <td>{{ $attendance->month }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}</td>
                <td>
                    @php
                        $late_minutes = $attendance->late;
                        $lateHours = floor($late_minutes / 60);
                        $lateMinutes = $late_minutes % 60;
                    @endphp
                    {{ $lateHours }} hours {{ $lateMinutes }} mins
                </td>
                <td>{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}</td>
                <td>
                    @if ($attendance->early_departure_minutes === null)
                        N/A
                    @else
                        @php
                            $earlyDepartureMinutes = $attendance->early_departure_minutes;
                            $earlyDepartureHours = floor($earlyDepartureMinutes / 60);
                            $earlyDepartureMins = $earlyDepartureMinutes % 60;
                        @endphp
                        {{ $earlyDepartureHours }} hours {{ $earlyDepartureMins }} mins
                    @endif
                </td>
                <td>
                    @php
                        $overtime_minutes = $attendance->overtime_minutes;
                        $overtimeHours = floor($overtime_minutes / 60);
                        $overtimeMinutes = $overtime_minutes % 60;
                    @endphp
                    {{ $overtimeHours }} hours {{ $overtimeMinutes }} mins
                </td>
                <td>
                    <a class="btn btn-danger rounded-pill" href="{{ route('attendanceDelete', $attendance->id) }}">
                        <i class="fa-solid fa-trash"></i>
                    </a>
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
