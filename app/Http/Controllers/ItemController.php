<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Items API",
 *     version="1.0.0",
 *     description="API documentation for Items"
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local Development Server"
 * )
 */
class ItemController extends Controller
{
    private $path;

    public function __construct()
    {
        $this->path = storage_path('app/items.json');
    }

    private function response($status, $message, $data = null, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function index(Request $request)
    {
        $data = json_decode(file_get_contents($this->path), true);

        if ($request->category) {
            $data = array_filter($data, function ($item) use ($request) {
                return strtolower($item['category']) == strtolower($request->category);
            });
        }

        if ($request->search) {
            $data = array_filter($data, function ($item) use ($request) {
                return strpos(strtolower($item['name']), strtolower($request->search)) !== false;
            });
        }

        return $this->response('success', 'Items retrieved successfully', array_values($data));
    }

    public function show($id)
    {
        $data = json_decode(file_get_contents($this->path), true);

        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return $this->response('success', 'Item found', $item);
            }
        }

        return $this->response('error', "Item with ID $id not found", null, 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'price' => 'required|numeric|min:1000',
            'stock' => 'required|numeric|min:0',
            'description' => 'required'
        ]);

        $data = json_decode(file_get_contents($this->path), true);

        $lastId = !empty($data) ? end($data)['id'] : 0;
        $newId = $lastId + 1;

        $newItem = [
            'id' => $newId,
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description
        ];

        $data[] = $newItem;

        file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));

        return $this->response('success', 'Item created successfully', $newItem);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'price' => 'required|numeric|min:1000',
            'stock' => 'required|numeric|min:0',
            'description' => 'required'
        ]);

        $data = json_decode(file_get_contents($this->path), true);

        foreach ($data as $index => $item) {
            if ($item['id'] == $id) {

                $data[$index] = [
                    'id' => $id,
                    'name' => $request->name,
                    'category' => $request->category,
                    'price' => $request->price,
                    'stock' => $request->stock,
                    'description' => $request->description
                ];

                file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));

                return $this->response('success', 'Item updated successfully', $data[$index]);
            }
        }

        return $this->response('error', "Item with ID $id not found", null, 404);
    }

    public function patch(Request $request, $id)
    {
        $data = json_decode(file_get_contents($this->path), true);

        foreach ($data as $index => $item) {
            if ($item['id'] == $id) {

                $data[$index]['name'] = $request->name ?? $item['name'];
                $data[$index]['category'] = $request->category ?? $item['category'];
                $data[$index]['price'] = $request->price ?? $item['price'];
                $data[$index]['stock'] = $request->stock ?? $item['stock'];
                $data[$index]['description'] = $request->description ?? $item['description'];

                file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));

                return $this->response('success', 'Item partially updated', $data[$index]);
            }
        }

        return $this->response('error', "Item with ID $id not found", null, 404);
    }

    public function destroy($id)
    {
        $data = json_decode(file_get_contents($this->path), true);

        foreach ($data as $index => $item) {
            if ($item['id'] == $id) {

                array_splice($data, $index, 1);

                file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));

                return $this->response('success', 'Item deleted successfully');
            }
        }

        return $this->response('error', "Item with ID $id not found", null, 404);
    }
}
