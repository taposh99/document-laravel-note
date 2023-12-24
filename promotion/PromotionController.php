<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Models\PromotionBrand;
use App\Models\PromotionCatagory;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
    {
        return view('promotion.index', [
            // 'promotions' => Promotion::all(),
            // 'promotionValus' =>Promotion::paginate(10),
            'promotionValus' => Promotion::orderBy('created_at', 'desc')->get(),

            // dd($promotions);
        ]);
    }
    public function create()
    {
        return view('promotion.create', [
            'proCatagory' => PromotionCatagory::orderby('id', 'desc')->get(),
            'proBrand' => PromotionBrand::orderby('id', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        //  dd($request);
        $validatedData = $request->validate([
            'category_id' => ['required', 'max:255'],
            'brand_id' => ['required', 'max:255'],

            'banner' => 'dimensions:max_width=1450px,max_height=340px',


        ]);


        $imageName = time() . '.' . $request->banner->extension();
        $request->banner->move(public_path('images/banner'), $imageName);
        $status = ($request->input('status') === 'active') ? true : false;


        Promotion::create([

            'title' => $request->title,
            'banner' => $imageName,
            'discount' => $request->discount,
            'slug' => $this->makeSlug($request),
            'desceription' => $request->desceription,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'status' =>  $status,

        ]);



        // toastr()->success('Data has been saved successfully!');
        return redirect()->route('promotion.index')->with('message', 'Create Successfully');
    }
    private function makeSlug($request)
    {
        if ($request->slug) {
            $str = $request->slug;
            return preg_replace('/\s+/u', '-', trim($str));
        }
        $str = $request->title;
        return preg_replace('/\s+/u', '-', trim($str));
    }

    public function edit($id)
    {
        return view('promotion.edit', [
            'promotion' => Promotion::find($id)
        ]);
    }
    public function update(Request $request, Promotion $promotion)
    {
        $validatedData = $request->validate([


            'banner' => 'dimensions:max_width=1450px,max_height=340px',



        ]);

        // dd($request);
        // dd($request->toArray());
        $status = ($request->input('status') === 'active') ? true : false;

        $filename = '';
        if ($request->hasfile('banner')) {
            $file = $request->file('banner');
            $filename = date('Ymdmhs') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/banner'), $filename);
        }


        // $imageName = time() . '.' . $request->banner->extension();
        // $request->banner->move(public_path('images/banner'), $imageName);




        $status = ($request->input('status') === 'active') ? true : false;

        $promotion = Promotion::find($request->promotion_id);
        $promotion->title = $request->title;
        $promotion->discount = $request->discount;
        $promotion->slug = $request->slug;
        $promotion->desceription = $request->desceription;
        $promotion->status = $status;
        $promotion->banner = $filename;


        $promotion->save();
        // toastr()->success('Update successfully!');
        return back()->with('message', 'Update Successfully');
    }

    public function delete(Request $request)
    {
        $promotion = Promotion::find($request->promotion_id);

        $promotion->delete();
        // toastr()->success('Update successfully!');
        return back()->with('message', 'Delete Successfully');
    }

    //change status
    public function changeStatusPromotion($id)
    {
        $getStatus = Promotion::select('status')->where('id', $id)->first();
        if ($getStatus->status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }
        Promotion::where('id', $id)->update(['status' => $status]);
        // toastr()->success('status Update successfully!');
        return redirect()->back();
    }


    //promotion catagory
    public function catagoryIndex()
    {
        return view('promotion.catagory.index', [
            'promotionCatagory' => PromotionCatagory::orderBy('created_at', 'desc')->get(),
        ]);
    }
    public function catagoryCreate()
    {
        return view('promotion.catagory.create');
    }

    public function catagoryStore(Request $request)
    {
        // dd($request);
        //  $validator = Validator::make($input, $rules, $messages = [
        //     'required' => 'The :attribute field is required.',
        // ]);
        $validatedData = $request->validate([
            'catagory' => ['required', 'unique:promotion_catagories', 'max:255'],

        ]);
        PromotionCatagory::create([

            'catagory' => $request->catagory,

        ]);


        return back()->with('message', 'Create Successfully');
    }
    public function catagoryEdit($id)
    {
        return view('promotion.catagory.edit', [
            'catagoryEdit' => PromotionCatagory::find($id)
        ]);
    }
    public function catagoryUpdate(Request $request)
    {
        // dd($request);
        // dd($request->toArray());


        $catagorydata = PromotionCatagory::find($request->catagoryId);
        $catagorydata->catagory = $request->catagory;

        $catagorydata->save();

        return back()->with('message', 'Update Successfully');
    }

    public function catagoryDelete(Request $request)
    {
        $promotion = PromotionCatagory::find($request->catagory_id);

        $promotion->delete();
        // toastr()->success('Update successfully!');
        return back()->with('message', 'Delete Successfully');
    }

    //promotion brand

    public function brandIndex()
    {
        return view('promotion.brand.index', [
            'brands' => PromotionBrand::orderBy('created_at', 'desc')->get(),
        ]);
    }
    public function brandCreate()
    {
        return view('promotion.brand.create');
    }
    public function brandStore(Request $request)
    {
        //dd($request);
        $validatedData = $request->validate([
            'brand' => ['required', 'unique:promotion_brands', 'max:255'],

        ]);
        PromotionBrand::create([

            'brand' => $request->brand,
        ]);
        return back()->with('message', 'Create Successfully');
    }

    public function brandEdit($id)
    {
        return view('promotion.brand.edit', [
            'brandEdit' => PromotionBrand::find($id)
        ]);
    }

    public function brandUpdate(Request $request)
    {
        // dd($request);
        // dd($request->toArray());


        $brand = PromotionBrand::find($request->brandId);
        $brand->brand = $request->brand;

        $brand->save();

        return back()->with('message', 'Update Successfully');
    }



    public function brandDelete(Request $request)
    {
        $brand = PromotionBrand::find($request->brand_id);

        $brand->delete();

        return back()->with('message', 'Delete Successfully');
    }



    public function explore()
    {
        $explore = Promotion::where('status', 1)->get();
        $promotionCatagory = PromotionCatagory::orderBy('created_at', 'desc')->get();


        return view('promotion.explore.allpromotion', compact('explore', 'promotionCatagory'));
    }


    public function promotionDetails($slug)
    {
        $promotionDetails = Promotion::where('slug', $slug)->firstOrFail();

        return view('promotion.details.description', compact('promotionDetails'));
    }



    public function promotionSearch(Request $request)
    {
        // dd($request->catrgory);

        $query = $request->input('query');
        // dd($query);
        if (empty($query)) {
            $results = collect();
        } else {
            $results = Promotion::where('status', 1)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder
                        ->where('title', 'LIKE', '%' . $query . '%')
                        ->orWhere('discount', 'LIKE', '%' . $query . '%')

                        ->orWhereHas('promotionCatagory', function ($categoryQuery) use ($query) {
                            $categoryQuery->where('catagory', 'LIKE', '%' . $query . '%');
                        })

                        ->orWhereHas('promotionBrand', function ($categoryQuery) use ($query) {
                            $categoryQuery->where('brand', 'LIKE', '%' . $query . '%');
                        });
                })
                ->get();
        }

        return view('promotion.explore.search', ['results' => $results]);
    }

    public function showDataByCategory(Request $request, $id = null)
    {


        // dd($query);
            $category = Promotion::where('category_id', $request->category_id)->where('status', 1)->get();
        // Fetch the category with the given ID
        return view('promotion.catagory.search', compact('category',));
    }
    public function updateState(Request $request)
    {
        // Retrieve the updated state from the request
        $isChecked = $request->input('isChecked');

        // Perform any necessary logic to update the state
        // For example, you can store it in a database or session

        // Return a response, if needed
        return response()->json(['success' => true]);
    }
}
