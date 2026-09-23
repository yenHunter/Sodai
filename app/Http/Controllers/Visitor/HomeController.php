<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Services\Visitor\CmsPageService;
use App\Services\Visitor\HomeService;

class HomeController extends Controller
{
    public function __construct(
        private HomeService $homeService,
        private CmsPageService $cmsPageService
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
            'featureItems' => $this->homeService->getFeatureItems(),
            'limitedTimeOffers' => $this->homeService->getLimitedTimeOffers(),
        ]);
    }

    public function about()
    {
        $page = $this->cmsPageService->getPage('about');

        return view('visitor.pages.about', compact('page'));
    }

    public function offer()
    {
        return view('visitor.pages.offers', [
            'offers' => $this->homeService->getOfferList(),
        ]);
    }
}
