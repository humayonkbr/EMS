<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function giveAttendance()
    {

        return view('admin.pages.attendance.attendance');
    }
    // public function attendanceList()
    // {
    //     $attendances = Attendance::paginate(10);
    //     return view('admin.pages.attendance.viewAttendance', compact('attendances'));
    // }

//     public function attendanceList()
// {
//     // Retrieve attendance records for all employees
//     $attendances = Attendance::paginate(10);

//     // Loop through each attendance record and calculate necessary fields
//     foreach ($attendances as $attendance) {
//         // Parse check-in and check-out times using Carbon
//         $checkInTime = Carbon::parse($attendance->check_in);
//         $checkOutTime = Carbon::parse($attendance->check_out);

//         // Calculate the duration in minutes
//         $attendance->duration_minutes = $checkInTime->diffInMinutes($checkOutTime);

//         // Calculate late time if check-in is after 9:00 AM
//         $checkInThreshold = Carbon::createFromTime(9, 0, 0);
//         $attendance->late = $checkInTime->greaterThan($checkInThreshold)
//             ? $checkInTime->diffInMinutes($checkInThreshold)
//             : 0;

//         // Calculate overtime (assuming normal working hours of 8 hours)
//         $normalWorkingHours = 8 * 60; // 8 hours in minutes
//         $attendance->overtime_minutes = $attendance->duration_minutes > $normalWorkingHours
//             ? $attendance->duration_minutes - $normalWorkingHours
//             : 0;
//     }

//     // Pass the attendances to the view
//     return view('admin.pages.attendance.viewAttendance', compact('attendances'));
// }

// public function attendanceList()
// {
//     // Retrieve attendance records for all employees
//     $attendances = Attendance::paginate(10);

//     // Loop through each attendance record and calculate necessary fields
//     foreach ($attendances as $attendance) {
//         // Parse check-in and check-out times using Carbon
//         $checkInTime = Carbon::parse($attendance->check_in);
//         $checkOutTime = Carbon::parse($attendance->check_out);

//         // Calculate the duration in minutes
//         $attendance->duration_minutes = $checkInTime->diffInMinutes($checkOutTime);

//         // Calculate late time if check-in is after 9:00 AM
//         $checkInThreshold = Carbon::createFromTime(9, 0, 0);
//         $attendance->late = $checkInTime->greaterThan($checkInThreshold)
//             ? $checkInTime->diffInMinutes($checkInThreshold)
//             : 0;

//         // Calculate overtime (assuming normal working hours of 8 hours)
//         $normalWorkingHours = 8 * 60; // 8 hours in minutes
//         $attendance->overtime_minutes = $attendance->duration_minutes > $normalWorkingHours
//             ? $attendance->duration_minutes - $normalWorkingHours
//             : 0;

//         // Calculate early departure time (if check-out is before 5:00 PM)
//         $endOfWorkDay = Carbon::createFromTime(17, 0, 0); // Office end time: 5:00 PM
//         $attendance->early_departure_minutes = $checkOutTime->lessThan($endOfWorkDay)
//             ? $endOfWorkDay->diffInMinutes($checkOutTime)
//             : 0;
//     }

//     // Pass the attendances to the view
//     return view('admin.pages.attendance.viewAttendance', compact('attendances'));
// }


public function attendanceList()
{
    // Retrieve attendance records for all employees
    $attendances = Attendance::paginate(10);

    // Loop through each attendance record and calculate necessary fields
    foreach ($attendances as $attendance) {
        // Parse check-in and check-out times using Carbon
        $checkInTime = Carbon::parse($attendance->check_in);
        $checkOutTime = Carbon::parse($attendance->check_out);

        // Calculate the duration in minutes
        $attendance->duration_minutes = $checkInTime->diffInMinutes($checkOutTime);

        // Calculate late time if check-in is after 9:00 AM
        $checkInThreshold = Carbon::createFromTime(9, 0, 0);
        $attendance->late = $checkInTime->greaterThan($checkInThreshold)
            ? $checkInTime->diffInMinutes($checkInThreshold)
            : 0;

        // Calculate overtime (assuming normal working hours of 8 hours)
        $normalWorkingHours = 8 * 60; // 8 hours in minutes
        $attendance->overtime_minutes = $attendance->duration_minutes > $normalWorkingHours
            ? $attendance->duration_minutes - $normalWorkingHours
            : 0;

        // Calculate early departure time (if check-out is before 5:00 PM)
        $endOfWorkDay = Carbon::createFromTime(17, 0, 0); // Office end time: 5:00 PM
        if ($checkOutTime->lessThan($endOfWorkDay)) {
            $attendance->early_departure_minutes = $endOfWorkDay->diffInMinutes($checkOutTime);
        } else {
            // Set early departure as null if the check-out is at or after 5:00 PM
            $attendance->early_departure_minutes = null;
        }
    }

    // Pass the attendances to the view
    return view('admin.pages.attendance.viewAttendance', compact('attendances'));
}





    public function checkIn(Request $request)
    {
        // Validate the request
        $request->validate([
            'attendance_date' => 'date|before_or_equal:today',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
        ]);

        // Get the date and time from the form
        $selectedDate = $request->input('attendance_date') ?? now()->toDateString();
        $checkInTime = Carbon::createFromFormat('H:i', $request->input('check_in_time'));
        $checkOutTime = $request->input('check_out_time') ? Carbon::createFromFormat('H:i', $request->input('check_out_time')) : null;
        $currentMonth = Carbon::now()->monthName;

        // Check if the attendance for the selected date already exists
        $existingAttendance = Attendance::where('employee_id', auth()->user()->id)
            ->whereDate('select_date', $selectedDate)
            ->first();

        if ($existingAttendance) {
            notify()->error('Attendance already given for the selected date.');
            return redirect()->back();
        }

        // Late time logic
        $lateMinutes = 0;
        $checkInThreshold = Carbon::createFromTime(9, 0, 0); // Threshold at 9:00 AM

        // Calculate late minutes if the check-in is after 9:00 AM
        if ($checkInTime->greaterThan($checkInThreshold)) {
            $lateMinutes = $checkInTime->diffInMinutes($checkInThreshold); // Store the difference in minutes
        }

        // Create the attendance record for the selected date
        Attendance::create([
            'employee_id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'department_name' => optional(auth()->user()->employee->department)->department_name ?? 'Not specified',
            'designation_name' => optional(auth()->user()->employee->designation)->designation_name ?? 'Not specified',
            'check_in' => $checkInTime->format('H:i:s'),
            'check_out' => $checkOutTime ? $checkOutTime->format('H:i:s') : null,
            'select_date' => $selectedDate,
            'month' => $currentMonth,
            'late' => $lateMinutes,


        ]);

        notify()->success('Attendance given successfully for ' . $selectedDate);
        return redirect()->back();
    }



    // updated code ends




    // public function checkOut(Request $request)
    // {
    //     // Validate the request to ensure check_out_time is provided
    //     $request->validate([
    //         'check_out_time' => 'required|date_format:H:i',
    //     ]);

    //     $existingAttendance = Attendance::where('employee_id', auth()->user()->id)
    //         ->whereDate('select_date', now()->toDateString())
    //         ->first();

    //     if ($existingAttendance) {
    //         // Check if already checked out
    //         if ($existingAttendance->check_out !== null) {
    //             notify()->error('You have already checked out for today.');
    //             return redirect()->back();
    //         }

    //         $checkInTime = Carbon::createFromTimeString($existingAttendance->check_in);
    //         $checkOutTime = Carbon::createFromFormat('H:i', $request->input('check_out_time'));
    //         $regularWorkingHours = $checkInTime->copy()->setTime(17, 0, 0);

    //         // Calculate overtime
    //         $overtime = $checkOutTime->diff($regularWorkingHours)->format('%H:%I:%S');

    //         // Update attendance record
    //         $existingAttendance->update([
    //             'check_out' => $checkOutTime->format('H:i:s'),
    //             'overtime' => $checkOutTime->greaterThan($regularWorkingHours) ? $overtime : null,
    //             'duration_minutes' => $checkOutTime->diffInMinutes($checkInTime),
    //         ]);

    //         notify()->success('You have checked out successfully.');
    //         if ($checkOutTime->greaterThan($regularWorkingHours)) {
    //             notify()->info("Overtime: $overtime");
    //         }
    //     } else {
    //         notify()->error('No check-in found for today.');
    //     }

    //     return redirect()->back();
    // }


    // public function checkOut(Request $request)
    // {
    //     // Validate the request to ensure check_out_time is provided
    //     $request->validate([
    //         'check_out_time' => 'required|date_format:H:i',
    //     ]);

    //     $existingAttendance = Attendance::where('employee_id', auth()->user()->id)
    //         ->whereDate('select_date', now()->toDateString())
    //         ->first();

    //     if ($existingAttendance) {
    //         // Check if already checked out
    //         if ($existingAttendance->check_out !== null) {
    //             notify()->error('You have already checked out for today.');
    //             return redirect()->back();
    //         }

    //         $checkInTime = Carbon::createFromTimeString($existingAttendance->check_in);
    //         $checkOutTime = Carbon::createFromFormat('H:i', $request->input('check_out_time'));
    //         $regularWorkingHours = $checkInTime->copy()->setTime(17, 0, 0); // Set 5:00 PM as regular end of day

    //         // Early departure logic
    //         $earlyDepartureMinutes = 0;
    //         if ($checkOutTime->lessThan($regularWorkingHours)) {
    //             $earlyDepartureMinutes = $regularWorkingHours->diffInMinutes($checkOutTime);
    //         }

    //         // Calculate overtime if applicable
    //         $overtime = $checkOutTime->greaterThan($regularWorkingHours)
    //             ? $checkOutTime->diff($regularWorkingHours)->format('%H:%I:%S')
    //             : null;

    //         // Update attendance record
    //         $existingAttendance->update([
    //             'check_out' => $checkOutTime->format('H:i:s'),
    //             'overtime' => $overtime,
    //             'early_departure_minutes' => $earlyDepartureMinutes, // Save early departure
    //             'duration_minutes' => $checkOutTime->diffInMinutes($checkInTime),
    //         ]);

    //         notify()->success('You have checked out successfully.');
    //         if ($overtime) {
    //             notify()->info("Overtime: $overtime");
    //         }
    //     } else {
    //         notify()->error('No check-in found for today.');
    //     }

    //     return redirect()->back();
    // }


    public function checkOut(Request $request)
{
    // Validate the request to ensure check_out_time is provided
    $request->validate([
        'check_out_time' => 'required|date_format:H:i',
    ]);

    // Find the existing attendance record for today
    $existingAttendance = Attendance::where('employee_id', auth()->user()->id)
        ->whereDate('select_date', now()->toDateString())
        ->first();

    if ($existingAttendance) {
        // Check if already checked out
        if ($existingAttendance->check_out !== null) {
            notify()->error('You have already checked out for today.');
            return redirect()->back();
        }

        $checkInTime = Carbon::createFromTimeString($existingAttendance->check_in);
        $checkOutTime = Carbon::createFromFormat('H:i', $request->input('check_out_time'));

        // Set 5:00 PM as regular end of day
        $regularWorkingHours = Carbon::createFromTime(17, 0, 0);

        // Early departure logic
        $earlyDepartureMinutes = 0;
        if ($checkOutTime->lessThan($regularWorkingHours)) {
            // Calculate early departure in minutes if check-out is before 5:00 PM
            $earlyDepartureMinutes = $regularWorkingHours->diffInMinutes($checkOutTime);
        }

        // Calculate overtime if check-out is after 5:00 PM
        $overtime = $checkOutTime->greaterThan($regularWorkingHours)
            ? $checkOutTime->diff($regularWorkingHours)->format('%H:%I:%S')
            : null;

        // Calculate total work duration in minutes
        $durationMinutes = $checkOutTime->diffInMinutes($checkInTime);

        // Update attendance record
        $existingAttendance->update([
            'check_out' => $checkOutTime->format('H:i:s'),
            'overtime' => $overtime, // If overtime exists, update it
            'early_departure_minutes' => $earlyDepartureMinutes, // Save early departure minutes
            'duration_minutes' => $durationMinutes, // Save total duration of the workday
        ]);

        // Success message for check-out
        notify()->success('You have checked out successfully.');
        if ($overtime) {
            notify()->info("Overtime: $overtime");
        }
    } else {
        // If no check-in found for today
        notify()->error('No check-in found for today.');
    }

    return redirect()->back();
}





    // updated check out code ends


    // Delete Attendance
    public function attendanceDelete($id)
    {
        $attendance =  Attendance::find($id);
        if ($attendance) {
            $attendance->delete();
        }
        notify()->success('Deleted Successfully.');
        return redirect()->back();
    }






    public function myAttendance()
    {
        $userId = auth()->user()->id;

        // Retrieve attendance records for the authenticated user and paginate them
        $attendances = Attendance::where('employee_id', $userId)->paginate(10);

        // Standard working hours in minutes (for example, 8 hours)
        $standardWorkingHours = 8 * 60; // 8 hours * 60 minutes

        // Loop through each paginated attendance record and calculate the duration and overtime
        foreach ($attendances as $attendance) {
            // Initialize duration and overtime to 0
            $attendance->duration_minutes = 0;
            $attendance->overtime_minutes = 0;

            // Check if check-in and check-out times exist
            if ($attendance->check_in && $attendance->check_out) {
                // Parse check-in and check-out times using Carbon
                $checkInTime = Carbon::parse($attendance->check_in);
                $checkOutTime = Carbon::parse($attendance->check_out);

                // Calculate the duration in minutes
                $attendance->duration_minutes = $checkInTime->diffInMinutes($checkOutTime);

                // Calculate overtime if duration exceeds standard working hours
                if ($attendance->duration_minutes > $standardWorkingHours) {
                    $attendance->overtime_minutes = $attendance->duration_minutes - $standardWorkingHours;
                }
            }
        }

        return view('admin.pages.attendance.myAttendance', compact('attendances'));
    }




    // report of all attendance record
    public function attendanceReport()
    {
        $attendances = Attendance::paginate(10);
        return view('admin.pages.attendance.attendanceReport', compact('attendances'));
    }

    // report  of my attendance
    public function myAttendanceReport()
    {
        $userId = auth()->user()->id;

        // Retrieve leave records for the authenticated user only
        $attendances = Attendance::where('employee_id', $userId)
            ->paginate(10);
        return view('admin.pages.attendance.myAttendanceReport', compact('attendances'));
    }


    // search for all attendance list
    public function searchAttendanceReport(Request $request)
    {
        $searchTerm = $request->search;

        $query = Attendance::query();

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('department_name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('designation_name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('select_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('month', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $attendances = $query->paginate(10);
        return view('admin.pages.attendance.viewSearchAttendance', compact('attendances'));
    }

    // search  my attendance
    public function searchMyAttendance(Request $request)
    {
        $userId = auth()->user()->id;
        $searchTerm = $request->search;

        $query = Attendance::where('employee_id', $userId);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('department_name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('designation_name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('select_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('month', 'LIKE', '%' . $searchTerm . '%');
                // Add more conditions based on your search requirements
            });
        }

        $attendances = $query->paginate(10);

        return view('admin.pages.attendance.searchMyAttendance', compact('attendances'));
    }
}
