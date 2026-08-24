<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CmsPage\UpdateCmsPageRequest;
use App\Models\CmsPage;
use App\Services\Admin\CmsPageService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CmsPageController extends Controller
{
    public function __construct(
        private CmsPageService $cmsPageService
    ) {}

    public function index()
    {
        $pages = $this->cmsPageService->getAllPages();

        return view('admin.cms.pages.index', compact('pages'));
    }

    public function edit(string $slug)
    {
        abort_unless(in_array($slug, CmsPage::SLUGS), Response::HTTP_NOT_FOUND);

        $page = $this->cmsPageService->findOrCreate($slug);

        return view('admin.cms.pages.edit', compact('page'));
    }

    public function update(UpdateCmsPageRequest $request, string $slug): RedirectResponse
    {
        abort_unless(in_array($slug, CmsPage::SLUGS), Response::HTTP_NOT_FOUND);

        $page = $this->cmsPageService->findOrCreate($slug);
        $this->cmsPageService->update($page, $request->validated());

        return redirect()
            ->route('admin.cms.pages.edit', $slug)
            ->with('success', "\"{$page->title}\" updated successfully.");
    }
}
