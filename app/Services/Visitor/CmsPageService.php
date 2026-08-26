<?php

namespace App\Services\Visitor;

use App\Models\CmsPage;

class CmsPageService
{
    public function getPage(string $slug): CmsPage
    {
        return CmsPage::findOrCreateBySlug($slug);
    }
}
