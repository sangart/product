<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProducerReques;
use App\Models\Producer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;

class ProducerController extends Controller
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
        $producers = Producer::with('laptops');

        if (!empty($request->name)) {

            $producers = $producers->where('name','like','%'.$request->name.'%');
        
        }
        // nếu như khi ấn search kết quả tìm kiếm chỉ có 1 hoặc 2 dòng thì sẽ kh phân trang, 3 trở lên thì sẽ phân trang
        //paginate(2) là phân trang, mỗi trang có 2 dòng
        Paginator::useBootstrap(); //sử dụng lớp use bootstrap để fix lỗi hiển thị phân trang.
        $producers = $producers->paginate(2);

        $data['producers'] = $producers;
        return view('producers.index', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('producers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // $dataInsert = [
        //     'name' => $request->name,
        //     'address' => $request->address,
        // ];
        // // dd($dataInsert);
        // DB::beginTransaction();

        // try{

        //     Producer::create($dataInsert);

        //     DB::commit();

        //     return redirect()->route('producer.index')->with('sucess', 'Insert into data to Producer sucessfull');
        // }catch(\Exception $ex)  {

        //     DB::rollBack();
        //     Log::error($ex->getMessage());
        //     return redirect()->back()->with('error', $ex->getMessage());

        // }



        $dataInsert = [
            'name' => $request->name,
            'address' => $request->address,
        ];
        // dd($dataInsert);
        DB::beginTransaction();

        try {
            Producer::create($dataInsert);

            // insert into data to table category (successful)
            DB::commit();

            return redirect()->route('producer.index')->with('sucess', 'Insert into data to Category Sucessful.');
        } catch (\Exception $ex) {
            // insert into data to table category (fail)
            DB::rollBack();
            Log::error($ex->getMessage());
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
        $producer = Producer::findOrFail($id);
        $data['producer'] = $producer;
        return view('producers.detail', $data);
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
        $producer = Producer::findOrFail($id);
        $data['producer'] = $producer;
        return view('producers.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProducerReques $request, $id)
    {
        //
        DB::beginTransaction();
        try{

            $producer = Producer::find($id);
            $producer->name = $request->name;
            $producer->address = $request->address;
            $producer->save();

            DB::commit();
            return redirect()->route('producer.index')->with('success','Insert Producer successful!');
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
            $producer = Producer::find($id);
            $producer->delete();

            DB::commit();

            return redirect()->route('producer.index')
                ->with('success', 'Delete producer successful!');
        }  catch (\Exception $ex) {
            DB::rollBack();
            // have error so will show error message
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }
}
