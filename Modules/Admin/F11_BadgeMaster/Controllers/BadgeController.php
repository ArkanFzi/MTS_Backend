<?php

namespace Modules\Admin\F11_BadgeMaster\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\F11_BadgeMaster\Services\BadgeService;
use Modules\Admin\F11_BadgeMaster\Requests\StoreBadgeRequest;
use Modules\Admin\F11_BadgeMaster\Requests\UpdateBadgeRequest;

class BadgeController extends Controller
{
    protected BadgeService $service;

    public function __construct(BadgeService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $badges = $this->service->getAllPaginated(15, $request->search);
        return view('admin::F11_BadgeMaster.index', compact('badges'));
    }

    public function create()
    {
        return view('admin::F11_BadgeMaster.create');
    }

    public function store(StoreBadgeRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('admin.badges.index')
                         ->with('success', 'Badge berhasil dibuat.');
    }

    public function edit(string $id)
    {
        $badge = $this->service->findById($id);
        return view('admin::F11_BadgeMaster.edit', compact('badge'));
    }

    public function update(UpdateBadgeRequest $request, string $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->route('admin.badges.index')
                         ->with('success', 'Badge berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return redirect()->route('admin.badges.index')
                         ->with('success', 'Badge berhasil dihapus.');
    }
}