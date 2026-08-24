 <!DOCTYPE html>
 <html lang="en">

 <head>
     @include('visitor.include.head')
     @stack('head')
 </head>

 <body class="{{ $bodyClass }}">

     <div id="ec-overlay">
         <div class="ec-ellipsis">
             <div></div>
             <div></div>
             <div></div>
             <div></div>
         </div>
     </div>

     @include('visitor.include.header')

     @yield('content')

     @include('visitor.include.footer')

     <!-- Newsletter Modal Start -->
     <div id="ec-popnews-bg"></div>
     <div id="ec-popnews-box">
         <div id="ec-popnews-close"><i class="ecicon eci-close"></i></div>
         <div class="row">
             <div class="col-md-6 disp-no-767">
                 <img src="{{ asset('visitor/images/banner/newsletter.png') }}" alt="newsletter">
             </div>
             <div class="col-md-6">
                 <div id="ec-popnews-box-content">
                     <h2>Subscribe Newsletter</h2>
                     <p>Subscribe the ekka ecommerce to get in touch and get the future update. </p>
                     <form id="ec-popnews-form" action="#" method="post">
                         <input type="email" name="newsemail" placeholder="Email Address" required />
                         <button type="button" class="btn btn-primary" name="subscribe">Subscribe</button>
                     </form>
                 </div>
             </div>
         </div>
     </div>
     <!-- Newsletter Modal end -->

     <!-- Footer navigation panel for responsive display -->
     <div class="ec-nav-toolbar">
         <div class="container">
             <div class="ec-nav-panel">
                 <div class="ec-nav-panel-icons">
                     <a href="#ec-mobile-menu" class="navbar-toggler-btn ec-header-btn ec-side-toggle"><i
                             class="fi-rr-menu-burger"></i></a>
                 </div>
                 <div class="ec-nav-panel-icons">
                     <a href="#ec-side-cart" class="toggle-cart ec-header-btn ec-side-toggle"><i
                             class="fi-rr-shopping-bag"></i><span
                             class="ec-cart-noti ec-header-count cart-count-lable">3</span></a>
                 </div>
                 <div class="ec-nav-panel-icons">
                     <a href="{{ route('visitor.index') }}" class="ec-header-btn"><i class="fi-rr-home"></i></a>
                 </div>
                 <div class="ec-nav-panel-icons">
                     <a href="wishlist.html" class="ec-header-btn"><i class="fi-rr-heart"></i><span
                             class="ec-cart-noti">4</span></a>
                 </div>
                 <div class="ec-nav-panel-icons">
                     @auth('customer')
                         <a href="{{ route('visitor.account.show') }}" class="ec-header-btn"><i class="fi-rr-user"></i></a>
                     @else
                         <a href="{{ route('visitor.login') }}" class="ec-header-btn"><i class="fi-rr-user"></i></a>
                     @endauth
                 </div>

             </div>
         </div>
     </div>
     <!-- Footer navigation panel for responsive display end -->

     <!-- Cart Floating Button -->
     <div class="ec-cart-float">
         <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle">
             <div class="header-icon"><i class="fi-rr-shopping-basket"></i>
             </div>
             <span class="ec-cart-count cart-count-lable">3</span>
         </a>
     </div>
     <!-- Cart Floating Button end -->

     @include('visitor.include.script')

 </body>

 </html>
