<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolylinesModel;

class PolylinesController extends Controller
{
    public function __construct()
    {
        $this->polylines = new PolylinesModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Map',
        ];

        return view('map', $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // validate data
        $request->validate([
            'name' => 'required|unique:polylines,name',
            'description' => 'required',
            'geom_polyline' => 'required',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ],
        [
            'name.required' => 'Name is required',
            'name.unique' => 'Name already exists',
            'description.required' => 'Description is required',
            'geom_polyline.required' => 'Geometry is required',
        ]);

        // upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polyline." . strtolower($image->getClientOriginalExtension());
            $image->storeAs('images', $name_image, 'public');
        } else {
            $name_image = null;
        }

        $data = [
            'geom' => $request->geom_polyline,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image
        ];

        // create data
        if (!$this->polylines->create($data)) {
            return redirect()->route('map')->with('error', 'Polyline failed to add!');
        }

        return redirect()->route('map')->with('success', 'Polyline has been added!');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Polyline',
            'id' => $id,
        ];

        return view('edit-polyline', $data);
    }

    public function update(Request $request, string $id)
    {
        // validate data
        $request->validate([
            'name' => 'required|unique:polylines,name,' . $id,
            'description' => 'required',
            'geom_polyline' => 'required',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2408',
        ],
        [
            'name.required' => 'Name is required',
            'name.unique' => 'Name already exist',
            'description.required' => 'Description is required',
            'geom_polyline.required' => 'Polyline is required',
        ]);

        // Get old image
        $old_image = $this->polylines->find($id)->image;

        // Get image File
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polyline." . strtolower($image->getClientOriginalExtension());
            $image->storeAs('images', $name_image, 'public');

            // Delete old image if exists
            if ($old_image && file_exists(storage_path('app/public/images/' . $old_image))) {
                unlink(storage_path('app/public/images/' . $old_image));
            }
        } else {
            $name_image = $old_image;
        }

        $data = [
            'name' => $request->name,
            'geom' => $request->geom_polyline,
            'description' => $request->description,
            'image' => $name_image,
        ];

        // Update data
        if (!$this->polylines->find($id)->update($data)) {
            return redirect()->route('map')->with('error', 'Polyline failed to update');
        }

        //redirect to map
        return redirect()->route('map')->with('success', 'Polyline has been updated');
    }

    public function destroy(string $id)
    {
        $imagefile = $this->polylines->find($id)->image;

        if (!$this->polylines->destroy($id)) {
            return redirect()->route('map')->with('error', 'Polyline failed to delete!');
        }

        // delete image
        if ($imagefile && file_exists(storage_path('app/public/images/' . $imagefile))) {
            unlink(storage_path('app/public/images/' . $imagefile));
        }

        return redirect()->route('map')->with('success', 'Polyline has been deleted!');
    }
}
