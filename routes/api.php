<?php   

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/update-status', function (Request $request) {
    \App\Models\SeatStatus::create([
        'status' => $request->input('status')
    ]);

    return response()->json(['message' => 'Stored']);
});


?>