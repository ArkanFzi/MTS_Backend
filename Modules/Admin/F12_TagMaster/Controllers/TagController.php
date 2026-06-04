<?php

namespace Modules\Admin\F12_TagMaster\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\F12_TagMaster\Services\TagService;
use Modules\Admin\F12_TagMaster\Requests\StoreTagRequest;
use Modules\Admin\F12_TagMaster\Requests\UpdateTagRequest;

class TagController extends Controller
{
    protected TagService $service;

    public function __construct(TagService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $tags = $this->service->getAllPaginated(15, $request->search);
        return view('admin::F12_TagMaster.index', compact('tags'));
    }

    public function create()
    {
        return view('admin::F12_TagMaster.create');
    }

    public function store(StoreTagRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('admin.tags.index')
                         ->with('success', 'Tag berhasil dibuat.');
    }

    public function edit(string $id)
    {
        $tag = $this->service->findById($id);
        return view('admin::F12_TagMaster.edit', compact('tag'));
    }

    public function update(UpdateTagRequest $request, string $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->route('admin.tags.index')
                         ->with('success', 'Tag berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return redirect()->route('admin.tags.index')
                         ->with('success', 'Tag berhasil dihapus.');
    }
}