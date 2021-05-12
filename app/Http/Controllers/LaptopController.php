<?php

namespace App\Http\Controllers;

use App\Models\Laptop;
use App\Models\Producer;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaptopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $data = [];
        $laptops = Laptop::with('producer');

        //-------------add new param to search
        if (!empty($request->name)) {

            $laptops = $laptops->where('name','like','%'.$request->name.'%');
        
        }
        if (!empty($request->producer_id)) {

            $laptops = $laptops->where('producer_id','like',$request->producer_id);
        
        }
        // nếu như khi ấn search kết quả tìm kiếm chỉ có 1 hoặc 2 dòng thì sẽ kh phân trang, 3 trở lên thì sẽ phân trang
        //paginate(2) là phân trang, mỗi trang có 2 dòng
        Paginator::useBootstrap(); //sử dụng lớp use bootstrap để fix lỗi hiển thị phân trang.
        $laptops = $laptops->paginate(2);
        

        // dd($posts->count());
        // };
        // $data = [];
        // get list data of table posts
        

        // get list data of table categories
        $producers = Producer::pluck('name', 'id')
            ->toArray();
        $data['producers'] = $producers;
        // dd($posts);
        $data['laptops'] = $laptops;
        return view('laptops.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data = [];
        $producers = Producer::pluck('name', 'id')
            ->toArray();
        // dd($categories);
        $data['producers'] = $producers;
        return view('laptops.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $dataInsert = [
            'name' => $request->name,
            'producer_id' => $request->producer_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ];
        // dd($dataInsert);
        DB::beginTransaction();

        try {
            // insert into table posts
            $laptop = Laptop::create($dataInsert);

            DB::commit();

            // success
            return redirect()->route('laptop.index')->with('success', 'Insert successful!');
        } catch (\Exception $ex) {
            DB::rollback();

            return redirect()->back()->with('error', $ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $data = [];
        $laptop = Laptop::findOrFail($id);
        $data['laptop'] = $laptop;
        return view('laptops.detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data = [];
        $producer = Producer::pluck('name', 'id')
            ->toArray();
        // $post = Post::find($id); // case 1
        $laptop = Laptop::findOrFail($id); // case 2
        $data['laptop'] = $laptop;
        $data['producer'] = $producer;
        return view('laptops.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        DB::beginTransaction();
        try{

            $laptop = Laptop::find($id);
            $laptop->name = $request->name;
            $laptop->producer_id = $request->producer_id;
            $laptop->price = $request->price;
            $laptop->quantity = $request->quantity;
            $laptop->save();

            DB::commit();
            return redirect()->route('laptop.index')->with('success','Insert Laptop successful!');
        }catch(\Exception $ex){
            DB::rollback();

            return redirect()->back()->with('error',$ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        DB::beginTransaction();

        try {
            $laptop = Laptop::find($id);
            $laptop->delete();

            DB::commit();

            return redirect()->route('laptop.index')
                ->with('success', 'Delete Laptop successful!');
        }  catch (\Exception $ex) {
            DB::rollBack();
            // have error so will show error message
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }
}
