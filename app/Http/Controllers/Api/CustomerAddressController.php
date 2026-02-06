<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    public function index(Customer $customer)
    {
        return response()->json($customer->addresses);
    }

    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'title' => 'nullable|string',
            'address_text' => 'required|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'is_default' => 'boolean',
        ]);

        if ($request->is_default) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $address = $customer->addresses()->create($request->all());

        return response()->json(['message' => 'Address created', 'address' => $address], 201);
    }

    public function update(Request $request, Customer $customer, CustomerAddress $address)
    {
        // Ensure address belongs to customer, though route binding might not strictly enforce hierarchy if not scoped
        if ($address->customer_id !== $customer->id) {
            return response()->json(['message' => 'Address not found for this customer'], 404);
        }

        $request->validate([
            'title' => 'nullable|string',
            'address_text' => 'sometimes|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'is_default' => 'boolean',
        ]);

        if ($request->is_default) {
            $customer->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($request->all());

        return response()->json(['message' => 'Address updated', 'address' => $address]);
    }

    public function destroy(Customer $customer, CustomerAddress $address)
    {
        if ($address->customer_id !== $customer->id) {
            return response()->json(['message' => 'Address not found for this customer'], 404);
        }
        $address->delete();
        return response()->json(['message' => 'Address deleted']);
    }
}
