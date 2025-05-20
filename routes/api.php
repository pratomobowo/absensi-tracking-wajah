use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Employee;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoint for face recognition data - gunakan data dari database
Route::get('/employees-face-data', function() {
    try {
        // Ambil semua karyawan aktif dengan data wajah 
        $employees = Employee::where('is_active', true)
            ->whereNotNull('face_data')
            ->get(['id', 'employee_id', 'name', 'face_data', 'photo']);
        
        // Jika tidak ada karyawan di database, berikan data dummy agar tidak error
        if ($employees->isEmpty()) {
            return [
                [
                    'id' => 1,
                    'employee_id' => 'EMP001',
                    'name' => 'John Doe',
                    'face_data' => '', // Data wajah kosong
                    'photo' => null
                ],
                [
                    'id' => 2,
                    'employee_id' => 'EMP002',
                    'name' => 'Jane Smith',
                    'face_data' => '', // Data wajah kosong
                    'photo' => null
                ]
            ];
        }
        
        return $employees;
    } catch (\Exception $e) {
        \Log::error('Error fetching employee face data: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch employee data'], 500);
    }
}); 