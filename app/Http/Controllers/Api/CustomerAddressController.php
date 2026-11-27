<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerAddressRequest;
use App\Repositories\Contracts\CustomerAddressRepositoryInterface;
use App\Services\CustomerAddressService;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    public function __construct(
        private CustomerAddressRepositoryInterface $addresses,
        private CustomerAddressService $service,
    ) {}

    public function index(Request $request)
    {
        return response()->json($this->addresses->forCustomer((int) $request->user()->id));
    }

    public function store(StoreCustomerAddressRequest $request)
    {
        return response()->json(
            $this->service->create((int) $request->user()->id, $request->validated()),
            201
        );
    }
}
