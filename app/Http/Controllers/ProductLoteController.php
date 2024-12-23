<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductLoteController extends Controller
{
    public function destroy($id)
    {
        // Delete the record from the product_lote table
        $deleted = DB::table('product_lote')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => false], 404);
    }
}
