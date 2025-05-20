@extends('layouts.app')

@section('title', 'Attendance Success')

@section('header-title', 'Attendance Success')

@section('content')
<div class="flex items-center justify-center min-h-[65vh]">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-green-600 text-white p-6 text-center">
            <div class="mb-4">
                <svg class="h-16 w-16 mx-auto text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold">{{ $type == 'clock-in' ? 'Clock In Success' : 'Clock Out Success' }}</h2>
            <p class="mt-2">{{ now()->format('l, F d, Y') }}</p>
        </div>
        
        <div class="p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Attendance Details</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-100 rounded">
                        <p class="text-sm text-gray-500">Employee</p>
                        <p class="font-semibold">{{ $employee->name }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded">
                        <p class="text-sm text-gray-500">Department</p>
                        <p class="font-semibold">{{ $employee->department->name }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded">
                        <p class="text-sm text-gray-500">Clock In</p>
                        <p class="font-semibold">
                            @if($attendance && $attendance->clock_in)
                                {{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded">
                        <p class="text-sm text-gray-500">Clock Out</p>
                        <p class="font-semibold">
                            @if($attendance && $attendance->clock_out)
                                {{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i:s') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <p class="text-green-600 font-semibold mb-4">
                    {{ $type == 'clock-in' ? 'You have successfully clocked in.' : 'You have successfully clocked out.' }}
                </p>
                
                <a href="{{ route('attendance.index') }}" class="inline-block bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto redirect after some time
    setTimeout(() => {
        window.location.href = "{{ route('attendance.index') }}";
    }, 10000); // 10 seconds
</script>
@endsection 