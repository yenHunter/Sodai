<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Services\Visitor\FaqService;

class FaqController extends Controller
{
    public function __construct(
        private FaqService $faqService
    ) {}

    public function index()
    {
        return view('visitor.pages.faq', [
            'categories' => $this->faqService->getPublishedCategories(),
        ]);
    }
}
