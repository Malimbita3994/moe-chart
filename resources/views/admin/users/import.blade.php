@extends('layouts.admin')

@section('title', 'Upload Users')
@section('page-title', 'Upload Users')

@section('content')
<div class="min-w-0 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-blue-100 border-2 border-blue-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.96 6h.04a5 5 0 011 9.903M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Upload Users</h2>
                    <p class="text-sm text-gray-600">Import users from CSV and bind each to a unit/department/section</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 border border-green-300 text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="mb-6 p-4 rounded-lg bg-amber-100 border border-amber-300 text-amber-800">
            {{ session('warning') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-300 text-red-800">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Template download -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <h3 class="text-lg font-bold text-gray-800 mb-2">1. Download template</h3>
        <p class="text-sm text-gray-600 mb-4">Use the CSV template with the correct columns. Each user will be linked to a unit (department/section) and optionally to a position within that unit.</p>
        <a href="{{ route('admin.users.import.template') }}" class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white transition-colors" style="background-color: #D4AF37; color: #1F2937;" download onmouseover="this.style.backgroundColor='#C4A027'" onmouseout="this.style.backgroundColor='#D4AF37'">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download CSV template
        </a>
    </div>

    <!-- CSV columns -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-6 mb-6 border-2 border-gray-300">
        <h3 class="text-lg font-bold text-gray-800 mb-2">CSV columns</h3>
        <p class="text-sm text-gray-600 mb-3">You can use either the standard names below or your Excel column names. Save your Excel as CSV (UTF-8) and upload.</p>
        <ul class="text-sm text-gray-700 space-y-1 list-disc list-inside mb-4">
            <li><strong>full_name</strong> or <strong>fullName</strong>, <strong>email</strong> — required</li>
            <li><strong>phone</strong> or <strong>mobileNumber</strong>, <strong>employee_number</strong> or <strong>checkNumber</strong> — optional</li>
            <li><strong>designation</strong> — optional; job title. If it contains Director/Head/Commissioner etc., the user is assigned to the <strong>head position</strong> of the unit; otherwise to a staff position.</li>
            <li><strong>unit_code</strong>, <strong>subvote</strong>, or <strong>unit_name</strong> / <strong>department</strong> — required to bind user to a unit. <strong>department</strong> = Directorate, Division, Unit or Section name. Values like <em>Basic Education (2022)</em>, <em>LEGAL SERVICES UNIT (1009)</em>: the number in parentheses is the <strong>subvote</strong>; set each unit Code in the org structure to that subvote (e.g. 2022, 1009, 1001) for matching.</li>
            <li><strong>vote</strong> — optional but recommended. Only rows with <strong>vote = 46</strong> (Ministry of Education, Science and Technology) are imported; others are skipped.</li>
            <li><strong>position_name</strong> — optional; position within that unit (if empty, head or first position is chosen by designation)</li>
            <li><strong>role_slug</strong> — optional; e.g. viewer (default)</li>
            <li><strong>employment_status</strong> / <strong>employmentStatus</strong> — optional; "Terminated" → user INACTIVE</li>
            <li><strong>hire_date</strong> / <strong>hireDate</strong> — optional; DD-MM-YYYY as assignment start date</li>
        </ul>
        <p class="text-xs text-gray-500">Ministry = vote 46. The number in parentheses in the department (e.g. 2022, 1009, 1001, 2424) is the subvote; set the same value in the unit’s <strong>Code</strong> in the org structure for reliable matching. Columns like <strong>sex</strong>, <strong>birthDate</strong>, <strong>workstation</strong> are ignored.</p>
    </div>

    <!-- Upload form -->
    <div class="animated-card card-hover bg-white rounded-xl shadow-lg p-8 border-2 border-gray-300">
        <h3 class="text-lg font-bold text-gray-800 mb-4">2. Upload your CSV</h3>
        <form action="{{ route('admin.users.import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">CSV file</label>
                <input type="file" name="file" id="file" accept=".csv,.txt" required
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Max 2 MB. File must have a header row with column names.</p>
            </div>
            <div class="flex items-center">
                <input type="hidden" name="skip_existing" value="0">
                <input type="checkbox" name="skip_existing" id="skip_existing" value="1" checked
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="skip_existing" class="ml-2 text-sm text-gray-700">Skip rows with existing email (do not update)</label>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 rounded-lg font-semibold text-white transition-colors" style="background-color: #D4AF37; color: #1F2937;" onmouseover="this.style.backgroundColor='#C4A027'" onmouseout="this.style.backgroundColor='#D4AF37'">
                    Upload and import
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 rounded-lg font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @if (session('import_result') && count(session('import_result.errors', [])) > 0)
        <div class="animated-card bg-white rounded-xl shadow-lg p-6 mt-6 border-2 border-amber-300">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Row errors</h3>
            <p class="text-sm text-gray-600 mb-4">Fix these rows in your CSV and re-upload if needed.</p>
            <ul class="text-sm text-gray-700 space-y-1 max-h-64 overflow-y-auto">
                @foreach (session('import_result.errors', []) as $row => $msg)
                    <li><strong>Row {{ $row }}</strong>: {{ $msg }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
