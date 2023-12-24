<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $data = Teacher::latest()->get();

        return view('teacher.index', [
            'data' => $data,
        ]);
    }

    // public function getData() {
    //     // Retrieve the data you want to send to the client and paginate it
    //     $data = Teacher::latest()->paginate(5); // Paginate with 10 items per page
    //     // You can adjust the number of items per page as needed

    //     // Return the paginated data as JSON
    //     return response()->json($data);
    // }


    public function store(Request $request)
    {

        $product = new Teacher();
        $product->name = $request->input('name');
        $product->title = $request->input('title');
        $product->institute = $request->input('institute');
        $product->save();


        // You can return a response to the AJAX request
        return response()->json(['message' => 'Product saved successfully']);
    }
    public function editData($id)
    {
       
        $data = Teacher::findOrFail($id);
        return response()->json($data);
    }
    
    public function updateData(Request $request, $id)
    {

        $data = Teacher::findOrFail($id)->update([
            'name' => $request->name,
            'title' => $request->title,
            'institute' => $request->institute,
        ]);
      


        // You can return a response to the AJAX request
        return response()->json($data);
    }
}

