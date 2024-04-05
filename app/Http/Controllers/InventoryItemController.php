<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\InventoryUserPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;

class InventoryItemController extends Controller
{
    /**
     * show all inventory items
     */
    public function getAll(Request $request)
    {
        // $userId = auth()->id();
        $userId = $request->userId;
        $inventoryUserPivotData = InventoryUserPivot::where('user_id', $userId)->get();
        $isValid = false;

        if (count($inventoryUserPivotData) > 0) {
            foreach ($inventoryUserPivotData as $value) {
                if ($value->inventory_id == $request->inventoryId) {
                    $isValid = true;
                }
            }
        }

        if ($isValid == false) {
            return response()->json([
                "status_code" => 401,
                "message" => "Unauthorized access"
            ]);
        }

        $inventoryItemList = InventoryItem::where('inventory_id', $request->inventoryId)->get();
        $inventoryItemData = [];

        if (count($inventoryItemList) > 0) {
            foreach ($inventoryItemList as $value) {
                $object = [
                    "id" => $value->id,
                    "inventoryId" => $value->inventory_id,
                    "name" => $value->name,
                    "description" => $value->description,
                    "image" => $value->image,
                    "quantity" => $value->quantity,
                ];
                array_push($inventoryItemData, $object);
            }
            return response()->json([
                "status_code" => 200,
                "results" => $inventoryItemData
            ]);
        } else {
            return response()->json([
                "status_code" => 500,
                "message" => "No records to show!"
            ]);
        }
    }

    /**
     * Create a new inventory Item
     */
    public function create(Request $request)
    {
        $validatedData = validator(
            $request->only(
                'inventoryId',
                'name',
                'description',
                'image',
                'quantity'
            ),
            [
                'inventoryId' => 'required',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable',
                'quantity' => 'required'
            ]
        );

        if ($validatedData->fails()) {
            return response()->json([
                "status_code" => 400,
                "message" => $validatedData->errors()->all()
            ]);
        }


        $productImage = $request->file('image');
        $productImagePath = null;
        if (!empty($productImage)) {
            $path = public_path() . '/media/images/';
            if (!FacadesFile::isDirectory($path)) {
                Storage::makeDirectory($path, 0777, true, true);
            } else {
                // FacadesFile::chmod($path, 0777); // Set the permissions of the directory to 0777
            }
            $imageName = time() . '_'  . $request->name . '.' . $productImage->extension();
            $productImage->move($path, $imageName);
            $productImagePath = env('APP_URL', 'http://localhost:8070') . '/media/images/' . $imageName;
        }

        $inventoryItemData = [
            'inventory_id' => $request->inventoryId,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $productImagePath,
            'quantity' => $request->quantity
        ];

        $inventoryItem = InventoryItem::create($inventoryItemData);

        if ($inventoryItem) {

            return response()->json([
                "status_code" => 201,
                "message" => "Inventory item successfully created!"
            ]);
        } else {
            return response()->json([
                "status_code" => 400,
                "message" => "Failed to create inventory item!"
            ]);
        }
    }

    /**
     * Display a specified inventory item.
     */
    public function show(Request $request)
    {
        // $userId = auth()->id();
        $userId = $request->userId;
        $inventoryItem = InventoryItem::where('id', $request->id)->first();
        $inventoryUserPivotData = InventoryUserPivot::where('user_id', $userId)->get();
        $isValid = false;

        if (count($inventoryUserPivotData) > 0) {
            foreach ($inventoryUserPivotData as $value) {
                if ($value->inventory_id == $inventoryItem->inventory_id) {
                    $isValid = true;
                }
            }
        }

        if ($isValid == false) {
            return response()->json([
                "status_code" => 401,
                "message" => "Unauthorized access"
            ]);
        }

        return response()->json([
            "status_code" => 200,
            "results" => [
                "id" => $inventoryItem->id,
                "inventoryId" => $inventoryItem->inventory_id,
                "name" => $inventoryItem->name,
                "description" => $inventoryItem->description,
                "quantity" => $inventoryItem->quantity,
                "image" => $inventoryItem->image
            ]
        ]);
    }

    /**
     * Update a specified inventory item.
     */
    public function update(Request $request)
    {
        $validatedData = validator(
            $request->only(
                'inventoryId',
                'name',
                'description',
                'image',
                'quantity'
            ),
            [
                'inventoryId' => 'required',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable',
                'quantity' => 'required'
            ]
        );

        if ($validatedData->fails()) {
            return response()->json([
                "status_code" => 400,
                "message" => $validatedData->errors()->all()
            ]);
        }

        $productImage = $request->file('image');
        $productImagePath = null;
        if (!empty($productImage)) {
            $path = public_path() . '/media/images/';
            if (!FacadesFile::isDirectory($path)) {
                Storage::makeDirectory($path, 0777, true, true);
            } else {
                // FacadesFile::chmod($path, 0777); // Set the permissions of the directory to 0777
            }
            $imageName = time() . '_'  . $request->name . '.' . $productImage->extension();
            $productImage->move($path, $imageName);
            $productImagePath = env('APP_URL', 'http://localhost:8070') . '/media/images/' . $imageName;
        }

        $inventoryItem = InventoryItem::where('id', $request->id)->first();

        if (isset($request->name)) {
            $inventoryItem->name = $request->name;
        }

        if (isset($request->description)) {
            $inventoryItem->description = $request->description;
        }

        if (isset($request->quantity)) {
            $inventoryItem->quantity = $request->quantity;
        }

        if (!empty($request->file('image'))) {
            $inventoryItem->image = $productImagePath;
        }

        if ($inventoryItem->update()) {

            return response()->json([
                "status_code" => 200,
                "message" => "Inventory item successfully updated!"
            ]);
        } else {
            return response()->json([
                "status_code" => 400,
                "message" => "Failed to update inventory item!"
            ]);
        }
    }

    /**
     * Remove a specified inventory item.
     */
    public function delete(Request $request)
    {
        $inventoryItem = InventoryItem::where('id', $request->id)->first();

        if ($inventoryItem->delete()) {

            return response()->json([
                "status_code" => 200,
                "message" => "Inventory item successfully deleted!"
            ]);
        } else {

            return response()->json([
                "status_code" => 400,
                "message" => "Failed to delete inventory item!"
            ]);
        }
    }
}
