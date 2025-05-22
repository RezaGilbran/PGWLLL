<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolygonsModel;

class PolygonsController extends Controller
{
    public function __construct()
    {
        $this->polygons = new PolygonsModel();
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
            'name' => 'required|unique:polygons,name',
            'description' => 'required',
            'geom_polygon' => 'required',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ],
        [
            'name.required' => 'Name is required',
            'name.unique' => 'Name already exists',
            'description.required' => 'Description is required',
            'geom_polygon.required' => 'Geometry is required',
        ]);

        // upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
            $image->storeAs('images', $name_image, 'public');
        } else {
            $name_image = null;
        }

        $data = [
            'geom' => $request->geom_polygon,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image
        ];

        // create data
        if (!$this->polygons->create($data)) {
            return redirect()->route('map')->with('error', 'Polygon failed to add!');
        }

        return redirect()->route('map')->with('success', 'Polygon has been added!');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Polygon',
            'id' => $id,
        ];

        return view('edit-polygon', $data);
    }

    public function update(Request $request, string $id)
    {
        // validate data
        $request->validate([
            'name' => 'required|unique:polygons,name,' . $id,
            'description' => 'required',
            'geom_polygon' => 'required',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2408',
        ],
        [
            'name.required' => 'Name is required',
            'name.unique' => 'Name already exist',
            'description.required' => 'Description is required',
            'geom_polygon.required' => 'Polygon is required',
        ]);

        // Get old image
        $old_image = $this->polygons->find($id)->image;

        // Get image File
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
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
            'geom' => $request->geom_polygon,
            'description' => $request->description,
            'image' => $name_image,
        ];

        // Update data
        if (!$this->polygons->find($id)->update($data)) {
            return redirect()->route('map')->with('error', 'Polygon failed to update');
        }

        //redirect to map
        return redirect()->route('map')->with('success', 'Polygon has been updated');
    }

    public function destroy(string $id)
    {
        $imagefile = $this->polygons->find($id)->image;

        if (!$this->polygons->destroy($id)) {
            return redirect()->route('map')->with('error', 'Polygon failed to delete!');
        }

        // delete image
        if ($imagefile && file_exists(storage_path('app/public/images/' . $imagefile))) {
            unlink(storage_path('app/public/images/' . $imagefile));
        }

        return redirect()->route('map')->with('success', 'Polygon has been deleted!');
    }
}
