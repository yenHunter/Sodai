<div class="ec-shop-leftside ec-vendor-sidebar col-lg-3 col-md-12">
    <div class="ec-sidebar-wrap ec-border-box">
        <div class="ec-sidebar-block">
            <div class="ec-vendor-block">
                <div class="ec-vendor-block-items">
                    <ul>
                        <li><a href="{{ route('account.show') }}" class="{{ request()->routeIs('account.show') ? 'active' : '' }}">Profile</a></li>
                        <li><a href="{{ route('account.addresses.index') }}" class="{{ request()->routeIs('account.addresses.*') ? 'active' : '' }}">Address</a></li>
                        <li><a href="{{ route('account.orders.index') }}" class="{{ request()->routeIs('account.orders.*') ? 'active' : '' }}">Order</a></li>
                        <li><a href="{{ route('account.wishlist.index') }}" class="{{ request()->routeIs('account.wishlist.*') ? 'active' : '' }}">Wishlist</a></li>
                        <li><a href="{{ route('account.reviews.index') }}" class="{{ request()->routeIs('account.reviews.*') ? 'active' : '' }}">Reviews</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="border-0 bg-transparent p-0 text-start w-100">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>