<div class="ec-shop-leftside ec-vendor-sidebar col-lg-3 col-md-12">
    <div class="ec-sidebar-wrap ec-border-box">
        <div class="ec-sidebar-block">
            <div class="ec-vendor-block">
                <div class="ec-vendor-block-items">
                    <ul>
                        <li><a href="{{ route('visitor.account.show') }}" class="{{ request()->routeIs('visitor.account.show') ? 'active' : '' }}">Profile</a></li>
                        <li><a href="{{ route('visitor.account.addresses.index') }}" class="{{ request()->routeIs('visitor.account.addresses.*') ? 'active' : '' }}">Address</a></li>
                        <li><a href="{{ route('visitor.account.orders.index') }}" class="{{ request()->routeIs('visitor.account.orders.*') ? 'active' : '' }}">Order</a></li>
                        <li><a href="{{ route('visitor.account.wishlist.index') }}" class="{{ request()->routeIs('visitor.account.wishlist.*') ? 'active' : '' }}">Wishlist</a></li>
                        <li><a href="{{ route('visitor.account.reviews.index') }}" class="{{ request()->routeIs('visitor.account.reviews.*') ? 'active' : '' }}">Reviews</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>