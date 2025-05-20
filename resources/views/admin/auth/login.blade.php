@extends('layouts.app')

@section('title', 'Admin Login')

@section('header-title', 'Admin Login')

@section('content')
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 bg-blue-700 text-white text-center">
                <h2 class="text-2xl font-bold">Admin Login</h2>
                <p class="text-blue-100 mt-1">Enter your credentials to access the admin dashboard</p>
            </div>
            
            <div class="p-6">
                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <a href="{{ route('attendance.index') }}" class="text-blue-600 hover:text-blue-800">
                Go to Employee Attendance
            </a>
        </div>
    </div>
</div>
@endsection 