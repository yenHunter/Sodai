<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Services\Visitor\CmsPageService;
use Symfony\Component\HttpFoundation\Response;

class CmsPageController extends Controller
{
    public function __construct(
        private CmsPageService $cmsPageService
    ) {}

    public function show(string $slug)
    {
        abort_unless(in_array($slug, CmsPage::SLUGS), Response::HTTP_NOT_FOUND);

        $page = $this->cmsPageService->getPage($slug);

        return view('visitor.pages.cms-page', compact('page'));
    }
}
