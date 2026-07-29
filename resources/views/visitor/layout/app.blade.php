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
                     <a href="index.html" class="ec-header-btn"><i class="fi-rr-home"></i></a>
                 </div>
                 <div class="ec-nav-panel-icons">
                     <a href="wishlist.html" class="ec-header-btn"><i class="fi-rr-heart"></i><span
                             class="ec-cart-noti">4</span></a>
                 </div>
                 <div class="ec-nav-panel-icons">
                     <a href="login.html" class="ec-header-btn"><i class="fi-rr-user"></i></a>
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

     <!-- Whatsapp -->
     <div class="ec-style ec-right-bottom">
         <!-- Start Floating Panel Container -->
         <div class="ec-panel">
             <!-- Panel Header -->
             <div class="ec-header">
                 <strong>Need Help?</strong>
                 <p>Chat with us on WhatsApp</p>
             </div>
             <!-- Panel Content -->
             <div class="ec-body">
                 <ul>
                     <!-- Start Single Contact List -->
                     <li>
                         <a class="ec-list" data-number="918866774266"
                             data-message="Please help me! I have got wrong product - ORDER ID is : #654321485">
                             <div class="d-flex bd-highlight">
                                 <!-- Profile Picture -->
                                 <div class="ec-img-cont">
                                     <img src="{{ asset('visitor/images/whatsapp/profile_01.jpg') }}" class="ec-user-img"
                                         alt="Profile image">
                                     <span class="ec-status-icon"></span>
                                 </div>
                                 <!-- Display Name & Last Seen -->
                                 <div class="ec-user-info">
                                     <span>Sahar Darya</span>
                                     <p>Sahar left 7 mins ago</p>
                                 </div>
                                 <!-- Chat iCon -->
                                 <div class="ec-chat-icon">
                                     <i class="fa fa-whatsapp"></i>
                                 </div>
                             </div>
                         </a>
                     </li>
                     <!--/ End Single Contact List -->
                     <!-- Start Single Contact List -->
                     <li>
                         <a class="ec-list" data-number="918866774266"
                             data-message="Please help me! I have got wrong product - ORDER ID is : #654321485">
                             <div class="d-flex bd-highlight">
                                 <!-- Profile Picture -->
                                 <div class="ec-img-cont">
                                     <img src="{{ asset('visitor/images/whatsapp/profile_02.jpg') }}" class="ec-user-img"
                                         alt="Profile image">
                                     <span class="ec-status-icon ec-online"></span>
                                 </div>
                                 <!-- Display Name & Last Seen -->
                                 <div class="ec-user-info">
                                     <span>Yolduz Rafi</span>
                                     <p>Yolduz is online</p>
                                 </div>
                                 <!-- Chat iCon -->
                                 <div class="ec-chat-icon">
                                     <i class="fa fa-whatsapp"></i>
                                 </div>
                             </div>
                         </a>
                     </li>
                     <!--/ End Single Contact List -->
                     <!-- Start Single Contact List -->
                     <li>
                         <a class="ec-list" data-number="918866774266"
                             data-message="Please help me! I have got wrong product - ORDER ID is : #654321485">
                             <div class="d-flex bd-highlight">
                                 <!-- Profile Picture -->
                                 <div class="ec-img-cont">
                                     <img src="{{ asset('visitor/images/whatsapp/profile_03.jpg') }}" class="ec-user-img"
                                         alt="Profile image">
                                     <span class="ec-status-icon ec-offline"></span>
                                 </div>
                                 <!-- Display Name & Last Seen -->
                                 <div class="ec-user-info">
                                     <span>Nargis Hawa</span>
                                     <p>Nargis left 30 mins ago</p>
                                 </div>
                                 <!-- Chat iCon -->
                                 <div class="ec-chat-icon">
                                     <i class="fa fa-whatsapp"></i>
                                 </div>
                             </div>
                         </a>
                     </li>
                     <!--/ End Single Contact List -->
                     <!-- Start Single Contact List -->
                     <li>
                         <a class="ec-list" data-number="918866774266"
                             data-message="Please help me! I have got wrong product - ORDER ID is : #654321485">
                             <div class="d-flex bd-highlight">
                                 <!-- Profile Picture -->
                                 <div class="ec-img-cont">
                                     <img src="{{ asset('visitor/images/whatsapp/profile_04.jpg') }}" class="ec-user-img"
                                         alt="Profile image">
                                     <span class="ec-status-icon ec-offline"></span>
                                 </div>
                                 <!-- Display Name & Last Seen -->
                                 <div class="ec-user-info">
                                     <span>Khadija Mehr</span>
                                     <p>Khadija left 50 mins ago</p>
                                 </div>
                                 <!-- Chat iCon -->
                                 <div class="ec-chat-icon">
                                     <i class="fa fa-whatsapp"></i>
                                 </div>
                             </div>
                         </a>
                     </li>
                     <!--/ End Single Contact List -->
                 </ul>
             </div>
         </div>
         <!--/ End Floating Panel Container -->
         <!-- Start Right Floating Button-->
         <div class="ec-right-bottom">
             <div class="ec-box">
                 <div class="ec-button rotateBackward">
                     <img class="whatsapp" src="{{ asset('visitor/images/common/whatsapp.png') }}" alt="whatsapp icon">
                 </div>
             </div>
         </div>
         <!--/ End Right Floating Button-->
     </div>
     <!-- Whatsapp end -->

     @include('visitor.include.script')

 </body>

 </html>
