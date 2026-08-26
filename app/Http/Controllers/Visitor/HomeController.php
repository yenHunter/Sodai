<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Services\Visitor\HomeService;

class HomeController extends Controller
{
    public function __construct(
        private HomeService $homeService
    ) {}

    public function index()
    {
        return view('visitor.pages.index', [
            'sliderBanners' => $this->homeService->getBanners('home_slider'),
            'promoBanners' => $this->homeService->getBanners('home_promo', 2),
            'featuredProducts' => $this->homeService->getFeaturedProducts(),
            'newArrivals' => $this->homeService->getNewArrivals(),
            'topRatedProducts' => $this->homeService->getTopRatedProducts(),
            'categories' => $this->homeService->getTopCategories(),
        ]);
    }
}
