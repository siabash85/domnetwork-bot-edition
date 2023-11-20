<?php

namespace Modules\Server\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Server\Entities\Server;
use Modules\Server\Http\Requests\Panel\ServerRequest;

class ServerController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $servers = Server::query()->get();
        return $this->successResponse($servers, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(ServerRequest $request)
    {
        $server =  Server::query()->create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'address' => $request->address,
            'inbound' => $request->inbound,
            'is_active' => $request->is_active,
            'is_default' => $request->is_default,
            'stock' => $request->stock,
        ]);
        return $this->successResponse($server, "ایجاد سرور با موفقیت انجام شد");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $server = Server::query()->find($id);
        return $this->successResponse($server, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(ServerRequest $request, $id)
    {
        $server = Server::query()->find($id);
        $server->update([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'address' => $request->address,
            'inbound' => $request->inbound,
            'is_active' => $request->is_active,
            'is_default' => $request->is_default,
            'stock' => $request->stock,
        ]);
        return $this->successResponse($server, "ویرایش سرور با موفقیت انجام شد");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $server = Server::query()->find($id);
        $server->delete();
        return $this->successResponse($server, "حذف سرور با موفقیت انجام شد");
    }
}
