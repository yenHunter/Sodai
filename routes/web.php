<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('visitor.pages.index');
})->name('visitor.index');

// ── Auth Guest Routes (coming soon) ───────────────────────
// Route::middleware('guest:customer')->group(function () {
//     Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthController::class, 'login'])->name('login.post');
//     Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
//     Route::post('/register', [AuthController::class, 'register'])->name('register.post');
// });

// ── Authenticated Customer Routes (coming soon) ────────────
// Route::middleware('auth.customer')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// });

// ── Products (coming soon) ────────────────────────────────
// Route::get('/products', [ProductController::class, 'index'])->name('products.index');
// Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// ── Cart (coming soon) ────────────────────────────────────
// Route::prefix('cart')->name('cart.')->group(function () {});

// ── Checkout (coming soon) ────────────────────────────────
// Route::prefix('checkout')->name('checkout.')->group(function () {});

// ── Orders (coming soon) ──────────────────────────────────
// Route::prefix('orders')->name('orders.')->group(function () {});

// ── Wishlist (coming soon) ────────────────────────────────
// Route::prefix('wishlist')->name('wishlist.')->group(function () {});



Route::get('/widgets', function () {
    return view('admin.widgets');
});

Route::get('/index', function () {
    return view('admin.index');
});

Route::get('/landing', function () {
    return view('admin.landing');
});

Route::get('/auth/lock-screen', function () {
    return view('admin.auth.lock-screen');
});

Route::get('/auth/sign-in', function () {
    return view('admin.auth.sign-in');
});

Route::get('/auth/new-pass', function () {
    return view('admin.auth.new-pass');
});

Route::get('/auth/delete-account', function () {
    return view('admin.auth.delete-account');
});

Route::get('/auth/sign-up', function () {
    return view('admin.auth.sign-up');
});

Route::get('/auth/reset-pass', function () {
    return view('admin.auth.reset-pass');
});

Route::get('/auth/success-mail', function () {
    return view('admin.auth.success-mail');
});

Route::get('/auth/two-factor', function () {
    return view('admin.auth.two-factor');
});

Route::get('/auth/login-pin', function () {
    return view('admin.auth.login-pin');
});

Route::get('/form/wizard', function () {
    return view('admin.form.wizard');
});

Route::get('/form/fileuploads', function () {
    return view('admin.form.fileuploads');
});

Route::get('/form/validation', function () {
    return view('admin.form.validation');
});

Route::get('/form/elements', function () {
    return view('admin.form.elements');
});

Route::get('/form/text-editors', function () {
    return view('admin.form.text-editors');
});

Route::get('/form/layout', function () {
    return view('admin.form.layout');
});

Route::get('/form/select', function () {
    return view('admin.form.select');
});

Route::get('/form/other-plugin', function () {
    return view('admin.form.other-plugin');
});

Route::get('/form/pickers', function () {
    return view('admin.form.pickers');
});

Route::get('/form/range-slider', function () {
    return view('admin.form.range-slider');
});

Route::get('/ui/tooltips', function () {
    return view('admin.ui.tooltips');
});

Route::get('/ui/notifications', function () {
    return view('admin.ui.notifications');
});

Route::get('/ui/typography', function () {
    return view('admin.ui.typography');
});

Route::get('/ui/scrollspy', function () {
    return view('admin.ui.scrollspy');
});

Route::get('/ui/offcanvas', function () {
    return view('admin.ui.offcanvas');
});

Route::get('/ui/tabs', function () {
    return view('admin.ui.tabs');
});

Route::get('/ui/breadcrumb', function () {
    return view('admin.ui.breadcrumb');
});

Route::get('/ui/buttons', function () {
    return view('admin.ui.buttons');
});

Route::get('/ui/placeholders', function () {
    return view('admin.ui.placeholders');
});

Route::get('/ui/videos', function () {
    return view('admin.ui.videos');
});

Route::get('/ui/collapse', function () {
    return view('admin.ui.collapse');
});

Route::get('/ui/images', function () {
    return view('admin.ui.images');
});

Route::get('/ui/dropdowns', function () {
    return view('admin.ui.dropdowns');
});

Route::get('/ui/utilities', function () {
    return view('admin.ui.utilities');
});

Route::get('/ui/carousel', function () {
    return view('admin.ui.carousel');
});

Route::get('/ui/colors', function () {
    return view('admin.ui.colors');
});

Route::get('/ui/cards', function () {
    return view('admin.ui.cards');
});

Route::get('/ui/grid', function () {
    return view('admin.ui.grid');
});

Route::get('/ui/accordions', function () {
    return view('admin.ui.accordions');
});

Route::get('/ui/alerts', function () {
    return view('admin.ui.alerts');
});

Route::get('/ui/spinners', function () {
    return view('admin.ui.spinners');
});

Route::get('/ui/pagination', function () {
    return view('admin.ui.pagination');
});

Route::get('/ui/modals', function () {
    return view('admin.ui.modals');
});

Route::get('/ui/popovers', function () {
    return view('admin.ui.popovers');
});

Route::get('/ui/links', function () {
    return view('admin.ui.links');
});

Route::get('/ui/progress', function () {
    return view('admin.ui.progress');
});

Route::get('/ui/badges', function () {
    return view('admin.ui.badges');
});

Route::get('/ui/list-group', function () {
    return view('admin.ui.list-group');
});

Route::get('/apps/calendar', function () {
    return view('admin.apps.calendar');
});

Route::get('/apps/outlook', function () {
    return view('admin.apps.outlook');
});

Route::get('/apps/chat', function () {
    return view('admin.apps.chat');
});

Route::get('/apps/api-keys', function () {
    return view('admin.apps.api-keys');
});

Route::get('/apps/social-feed', function () {
    return view('admin.apps.social-feed');
});

Route::get('/apps/file-manager', function () {
    return view('admin.apps.file-manager');
});

Route::get('/dashboard/projects', function () {
    return view('admin.dashboard.projects');
});

Route::get('/tables/custom', function () {
    return view('admin.tables.custom');
});

Route::get('/tables/static', function () {
    return view('admin.tables.static');
});

Route::get('/layouts/compact', function () {
    return view('admin.layouts.compact');
});

Route::get('/layouts/boxed', function () {
    return view('admin.layouts.boxed');
});

Route::get('/layouts/horizontal', function () {
    return view('admin.layouts.horizontal');
});

Route::get('/layouts/preloader', function () {
    return view('admin.layouts.preloader');
});

Route::get('/layouts/scrollable', function () {
    return view('admin.layouts.scrollable');
});

Route::get('/error/400', function () {
    return view('admin.error.400');
});

Route::get('/error/500', function () {
    return view('admin.error.500');
});

Route::get('/error/408', function () {
    return view('admin.error.408');
});

Route::get('/error/404', function () {
    return view('admin.error.404');
});

Route::get('/error/401', function () {
    return view('admin.error.401');
});

Route::get('/error/maintenance', function () {
    return view('admin.error.maintenance');
});

Route::get('/error/403', function () {
    return view('admin.error.403');
});

Route::get('/icons/flags', function () {
    return view('admin.icons.flags');
});

Route::get('/icons/tabler', function () {
    return view('admin.icons.tabler');
});

Route::get('/icons/lucide', function () {
    return view('admin.icons.lucide');
});

Route::get('/auth-split/lock-screen', function () {
    return view('admin.auth-split.lock-screen');
});

Route::get('/auth-split/sign-in', function () {
    return view('admin.auth-split.sign-in');
});

Route::get('/auth-split/new-pass', function () {
    return view('admin.auth-split.new-pass');
});

Route::get('/auth-split/delete-account', function () {
    return view('admin.auth-split.delete-account');
});

Route::get('/auth-split/sign-up', function () {
    return view('admin.auth-split.sign-up');
});

Route::get('/auth-split/reset-pass', function () {
    return view('admin.auth-split.reset-pass');
});

Route::get('/auth-split/success-mail', function () {
    return view('admin.auth-split.success-mail');
});

Route::get('/auth-split/two-factor', function () {
    return view('admin.auth-split.two-factor');
});

Route::get('/auth-split/login-pin', function () {
    return view('admin.auth-split.login-pin');
});

Route::get('/pages/search-results', function () {
    return view('admin.pages.search-results');
});

Route::get('/pages/coming-soon', function () {
    return view('admin.pages.coming-soon');
});

Route::get('/pages/pricing', function () {
    return view('admin.pages.pricing');
});

Route::get('/pages/faq', function () {
    return view('admin.pages.faq');
});

Route::get('/pages/terms-conditions', function () {
    return view('admin.pages.terms-conditions');
});

Route::get('/pages/timeline', function () {
    return view('admin.pages.timeline');
});

Route::get('/pages/empty', function () {
    return view('admin.pages.empty');
});

Route::get('/pages/privacy-policy', function () {
    return view('admin.pages.privacy-policy');
});

Route::get('/maps/leaflet', function () {
    return view('admin.maps.leaflet');
});

Route::get('/maps/vector', function () {
    return view('admin.maps.vector');
});

Route::get('/plugins/sortable', function () {
    return view('admin.plugins.sortable');
});

Route::get('/plugins/pdf-viewer', function () {
    return view('admin.plugins.pdf-viewer');
});

Route::get('/plugins/tree-view', function () {
    return view('admin.plugins.tree-view');
});

Route::get('/plugins/i18', function () {
    return view('admin.plugins.i18');
});

Route::get('/plugins/tour', function () {
    return view('admin.plugins.tour');
});

Route::get('/plugins/sweet-alerts', function () {
    return view('admin.plugins.sweet-alerts');
});

Route::get('/plugins/pass-meter', function () {
    return view('admin.plugins.pass-meter');
});

Route::get('/plugins/clipboard', function () {
    return view('admin.plugins.clipboard');
});

Route::get('/plugins/video-player', function () {
    return view('admin.plugins.video-player');
});

Route::get('/plugins/animation', function () {
    return view('admin.plugins.animation');
});

Route::get('/apps/email/compose', function () {
    return view('admin.apps.email.compose');
});

Route::get('/apps/email/inbox', function () {
    return view('admin.apps.email.inbox');
});

Route::get('/apps/email/details', function () {
    return view('admin.apps.email.details');
});

Route::get('/apps/ticket/list', function () {
    return view('admin.apps.ticket.list');
});

Route::get('/apps/ticket/create', function () {
    return view('admin.apps.ticket.create');
});

Route::get('/apps/ticket/details', function () {
    return view('admin.apps.ticket.details');
});

Route::get('/apps/crm/deals', function () {
    return view('admin.apps.crm.deals');
});

Route::get('/apps/crm/activities', function () {
    return view('admin.apps.crm.activities');
});

Route::get('/apps/crm/campaign', function () {
    return view('admin.apps.crm.campaign');
});

Route::get('/apps/crm/opportunities', function () {
    return view('admin.apps.crm.opportunities');
});

Route::get('/apps/crm/customers', function () {
    return view('admin.apps.crm.customers');
});

Route::get('/apps/crm/proposals', function () {
    return view('admin.apps.crm.proposals');
});

Route::get('/apps/crm/contacts', function () {
    return view('admin.apps.crm.contacts');
});

Route::get('/apps/crm/pipeline', function () {
    return view('admin.apps.crm.pipeline');
});

Route::get('/apps/crm/leads', function () {
    return view('admin.apps.crm.leads');
});

Route::get('/apps/crm/estimations', function () {
    return view('admin.apps.crm.estimations');
});

Route::get('/apps/users/permissions', function () {
    return view('admin.apps.users.permissions');
});

Route::get('/apps/users/roles', function () {
    return view('admin.apps.users.roles');
});

Route::get('/apps/users/account-settings', function () {
    return view('admin.apps.users.account-settings');
});

Route::get('/apps/users/contacts', function () {
    return view('admin.apps.users.contacts');
});

Route::get('/apps/users/role-details', function () {
    return view('admin.apps.users.role-details');
});

Route::get('/apps/users/profile', function () {
    return view('admin.apps.users.profile');
});

Route::get('/apps/ecommerce/refunds', function () {
    return view('admin.apps.ecommerce.refunds');
});

Route::get('/apps/ecommerce/categories', function () {
    return view('admin.apps.ecommerce.categories');
});

Route::get('/apps/ecommerce/reviews', function () {
    return view('admin.apps.ecommerce.reviews');
});

Route::get('/apps/ecommerce/sellers', function () {
    return view('admin.apps.ecommerce.sellers');
});

Route::get('/apps/ecommerce/orders', function () {
    return view('admin.apps.ecommerce.orders');
});

Route::get('/apps/ecommerce/product-details', function () {
    return view('admin.apps.ecommerce.product-details');
});

Route::get('/apps/ecommerce/customers', function () {
    return view('admin.apps.ecommerce.customers');
});

Route::get('/apps/ecommerce/attributes', function () {
    return view('admin.apps.ecommerce.attributes');
});

Route::get('/apps/ecommerce/order-details', function () {
    return view('admin.apps.ecommerce.order-details');
});

Route::get('/apps/ecommerce/seller-details', function () {
    return view('admin.apps.ecommerce.seller-details');
});

Route::get('/apps/ecommerce/order-add', function () {
    return view('admin.apps.ecommerce.order-add');
});

Route::get('/apps/ecommerce/products-grid', function () {
    return view('admin.apps.ecommerce.products-grid');
});

Route::get('/apps/ecommerce/cart', function () {
    return view('admin.apps.ecommerce.cart');
});

Route::get('/apps/ecommerce/product-add', function () {
    return view('admin.apps.ecommerce.product-add');
});

Route::get('/apps/ecommerce/products', function () {
    return view('admin.apps.ecommerce.products');
});

Route::get('/apps/invoice/list', function () {
    return view('admin.apps.invoice.list');
});

Route::get('/apps/invoice/create', function () {
    return view('admin.apps.invoice.create');
});

Route::get('/apps/invoice/details', function () {
    return view('admin.apps.invoice.details');
});

Route::get('/tables/datatables/fixed-columns', function () {
    return view('admin.tables.datatables.fixed-columns');
});

Route::get('/tables/datatables/ajax', function () {
    return view('admin.tables.datatables.ajax');
});

Route::get('/tables/datatables/checkbox-select', function () {
    return view('admin.tables.datatables.checkbox-select');
});

Route::get('/tables/datatables/columns', function () {
    return view('admin.tables.datatables.columns');
});

Route::get('/tables/datatables/column-searching', function () {
    return view('admin.tables.datatables.column-searching');
});

Route::get('/tables/datatables/basic', function () {
    return view('admin.tables.datatables.basic');
});

Route::get('/tables/datatables/export-data', function () {
    return view('admin.tables.datatables.export-data');
});

Route::get('/tables/datatables/rows-add', function () {
    return view('admin.tables.datatables.rows-add');
});

Route::get('/tables/datatables/range-search', function () {
    return view('admin.tables.datatables.range-search');
});

Route::get('/tables/datatables/child-rows', function () {
    return view('admin.tables.datatables.child-rows');
});

Route::get('/tables/datatables/select', function () {
    return view('admin.tables.datatables.select');
});

Route::get('/tables/datatables/javascript', function () {
    return view('admin.tables.datatables.javascript');
});

Route::get('/tables/datatables/rendering', function () {
    return view('admin.tables.datatables.rendering');
});

Route::get('/tables/datatables/fixed-header', function () {
    return view('admin.tables.datatables.fixed-header');
});

Route::get('/tables/datatables/scroll', function () {
    return view('admin.tables.datatables.scroll');
});

Route::get('/layouts/sidebar/dark', function () {
    return view('admin.layouts.sidebar.dark');
});

Route::get('/layouts/sidebar/offcanvas', function () {
    return view('admin.layouts.sidebar.offcanvas');
});

Route::get('/layouts/sidebar/no-icons', function () {
    return view('admin.layouts.sidebar.no-icons');
});

Route::get('/layouts/sidebar/compact', function () {
    return view('admin.layouts.sidebar.compact');
});

Route::get('/layouts/sidebar/image', function () {
    return view('admin.layouts.sidebar.image');
});

Route::get('/layouts/sidebar/on-hover', function () {
    return view('admin.layouts.sidebar.on-hover');
});

Route::get('/layouts/sidebar/gray', function () {
    return view('admin.layouts.sidebar.gray');
});

Route::get('/layouts/sidebar/gradient', function () {
    return view('admin.layouts.sidebar.gradient');
});

Route::get('/layouts/sidebar/on-hover-active', function () {
    return view('admin.layouts.sidebar.on-hover-active');
});

Route::get('/layouts/sidebar/with-lines', function () {
    return view('admin.layouts.sidebar.with-lines');
});

Route::get('/layouts/topbar/gray', function () {
    return view('admin.layouts.topbar.gray');
});

Route::get('/layouts/topbar/gradient', function () {
    return view('admin.layouts.topbar.gradient');
});

Route::get('/layouts/topbar/light', function () {
    return view('admin.layouts.topbar.light');
});

Route::get('/charts/apex/heatmap', function () {
    return view('admin.charts.apex.heatmap');
});

Route::get('/charts/apex/bar', function () {
    return view('admin.charts.apex.bar');
});

Route::get('/charts/apex/boxplot', function () {
    return view('admin.charts.apex.boxplot');
});

Route::get('/charts/apex/column', function () {
    return view('admin.charts.apex.column');
});

Route::get('/charts/apex/sparklines', function () {
    return view('admin.charts.apex.sparklines');
});

Route::get('/charts/apex/area', function () {
    return view('admin.charts.apex.area');
});

Route::get('/charts/apex/radialbar', function () {
    return view('admin.charts.apex.radialbar');
});

Route::get('/charts/apex/scatter', function () {
    return view('admin.charts.apex.scatter');
});

Route::get('/charts/apex/funnel', function () {
    return view('admin.charts.apex.funnel');
});

Route::get('/charts/apex/candlestick', function () {
    return view('admin.charts.apex.candlestick');
});

Route::get('/charts/apex/slope', function () {
    return view('admin.charts.apex.slope');
});

Route::get('/charts/apex/mixed', function () {
    return view('admin.charts.apex.mixed');
});

Route::get('/charts/apex/bubble', function () {
    return view('admin.charts.apex.bubble');
});

Route::get('/charts/apex/polar-area', function () {
    return view('admin.charts.apex.polar-area');
});

Route::get('/charts/apex/radar', function () {
    return view('admin.charts.apex.radar');
});

Route::get('/charts/apex/treemap', function () {
    return view('admin.charts.apex.treemap');
});

Route::get('/charts/apex/line', function () {
    return view('admin.charts.apex.line');
});

Route::get('/charts/apex/timeline', function () {
    return view('admin.charts.apex.timeline');
});

Route::get('/charts/apex/pie', function () {
    return view('admin.charts.apex.pie');
});

Route::get('/charts/apex/range', function () {
    return view('admin.charts.apex.range');
});

Route::get('/charts/chartjs/bar', function () {
    return view('admin.charts.chartjs.bar');
});

Route::get('/charts/chartjs/area', function () {
    return view('admin.charts.chartjs.area');
});

Route::get('/charts/chartjs/other', function () {
    return view('admin.charts.chartjs.other');
});

Route::get('/charts/chartjs/line', function () {
    return view('admin.charts.chartjs.line');
});
