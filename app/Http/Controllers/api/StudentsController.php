<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentsResource;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Students::all();

        return new StudentsResource(true, 'Data Students !', $students);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idnumber' => 'required|unique:students,idnumber',
            'fullname' => 'required',
            'gender' => 'required',
            'phone' => 'required|numeric|unique:students,phone',
            'address' => 'required',
            'emailaddress' => 'required|email|unique:students,emailaddress'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            $students = Students::create([
                'idnumber' => $request->idnumber,
                'fullname' => $request->fullname,
                'gender' => $request->gender,
                'address' => $request->address,
                'emailaddress' => $request->emailaddress,
                'phone' => $request->phone,
                'photo' => '',
                'photo_thumb' => '',
            ]);

            return new StudentsResource(true, 'Data berhasil tersimpan !', $students);
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
        $students = Students::find($id);

        if ($students) {
            return new StudentsResource(true, 'Data Ditemukan !', $students);
        } else {
            return response()->json([
                'message' => 'Data not found !'
            ], 422);
        }
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
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'gender' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'emailaddress' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            $students = Students::find($id);

            if ($students) {
                $students->fullname = $request->fullname;
                $students->gender = $request->gender;
                $students->phone = $request->phone;
                $students->address = $request->address;
                $students->emailaddress = $request->emailaddress;
                $students->save();

                return new StudentsResource(true, 'Data berhasil di-Update !', $students);
            } else {
                return response()->json([
                    'message' => 'Data not Found !'
                ]);
            }
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
        $students = Students::find($id);

        if ($students) {
            $students->delete();

            return new StudentsResource(true, 'Data berhasil di-Hapus', '');
        } else {
            return response()->json([
                'message' => 'Data not Found !'
            ]);
        }
    }

    public function doupload(Request $request, $id)
    {
        $validate = $request->validate([
            'photo' => 'image|mimes:png,jpg,jpeg|max:4096'
        ]);

        if ($request->hasFile('photo')) {
            $imageName = time() . '.' .  $request->file('photo')->extension();
            // Resize File Photo
            $file_name_thumb = time() . '_thumb.' .  $request->file('photo')->extension();
            $destinationPath = public_path('images/thumb') . "/" . $file_name_thumb;

            $imgFile = Image::make($request->file('photo')->path());
            $imgFile->resize(150, 150, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath);

            $request->file('photo')->move(public_path('images'), $imageName);
            // $path = $imageName; //Yang bagian ini dihapus saja
        }
        $students = Students::find($id);

        // Hapus foto lama, jika ada
        $photo_lama = $students->photo;
        $photo_lama_thumb = $students->photo_thumb;

        if ($photo_lama != '' || $photo_lama != null) {
            unlink(public_path('images/' . $photo_lama));
            unlink(public_path('images/thumb/' . $photo_lama_thumb));
        }

        if ($students) {
            $students->photo = $imageName;
            $students->photo_thumb = $file_name_thumb;
            $students->save();

            return new StudentsResource(true, 'Upload Berhasil !', '');
        } else {
            return response()->json([
                'message' => 'Data not Found !'
            ]);
        }
    }
}