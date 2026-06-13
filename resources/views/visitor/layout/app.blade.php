 <!DOCTYPE html>
 <html lang="en">

 <head>
     @include('visitor.include.head')
 </head>

 <body>

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

     <!-- Modal -->
     <div class="modal fade" id="ec_quickview_modal" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-dialog-centered" role="document">
             <div class="modal-content">
                 <button type="button" class="btn-close qty_close" data-bs-dismiss="modal" aria-label="Close"></button>
                 <div class="modal-body">
                     <div class="row">
                         <div class="col-md-5 col-sm-12 col-xs-12">
                             <!-- Swiper -->
                             <div class="qty-product-cover">
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_1.jpg"
                                         alt="">
                                 </div>
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_2.jpg"
                                         alt="">
                                 </div>
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_3.jpg"
                                         alt="">
                                 </div>
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_2.jpg"
                                         alt="">
                                 </div>
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_3.jpg"
                                         alt="">
                                 </div>
                             </div>
                             <div class="qty-nav-thumb">
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_1.jpg"
                                         alt="">
                                 </div>
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_2.jpg"
                                         alt="">
                                 </div>
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_3.jpg"
                                         alt="">
                                 </div>
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_2.jpg"
                                         alt="">
                                 </div>
                                 <div class="qty-slide">
                                     <img class="img-responsive" src="visitor/images/product-image/35_3.jpg"
                                         alt="">
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-7 col-sm-12 col-xs-12">
                             <div class="quickview-pro-content">
                                 <h5 class="ec-quick-title"><a href="product-left-sidebar.html">Hooded Neck full slive
                                         T-Shirt</a></h5>
                                 <div class="ec-quickview-rating">
                                     <i class="ecicon eci-star fill"></i>
                                     <i class="ecicon eci-star fill"></i>
                                     <i class="ecicon eci-star fill"></i>
                                     <i class="ecicon eci-star fill"></i>
                                     <i class="ecicon eci-star"></i>
                                 </div>

                                 <div class="ec-quickview-desc">Lorem Ipsum is simply dummy text of the printing and
                                     typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever
                                     since the 1500s,</div>
                                 <div class="ec-quickview-price">
                                     <span class="new-price">$199.00</span>
                                     <span class="old-price">$200.00</span>
                                 </div>

                                 <div class="ec-pro-variation">
                                     <div class="ec-pro-variation-inner ec-pro-variation-color">
                                         <span>Color</span>
                                         <div class="ec-pro-variation-content">
                                             <ul>
                                                 <li><span style="background-color:#3943bb;"></span></li>
                                                 <li><span style="background-color:#de772c;"></span></li>
                                                 <li><span style="background-color:#da0f2b;"></span></li>
                                             </ul>
                                         </div>
                                     </div>
                                     <div class="ec-pro-variation-inner ec-pro-variation-size">
                                         <span>Size</span>
                                         <div class="ec-pro-variation-content">
                                             <ul>
                                                 <li><span>S</span></li>
                                                 <li><span>M</span></li>
                                                 <li><span>L</span></li>
                                                 <li><span>XL</span></li>
                                             </ul>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="ec-quickview-qty">
                                     <div class="qty-plus-minus">
                                         <input class="qty-input" type="text" name="ec_qtybtn" value="1" />
                                     </div>
                                     <div class="ec-quickview-cart ">
                                         <button class="btn btn-primary">Add To Cart</button>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <!-- Modal end -->

     <!-- Click To Call -->
     <div class="ec-cc-style cc-right-bottom">
         <!-- Start Floating Panel Container -->
         <div class="cc-panel">
             <!-- Panel Content -->
             <div class="cc-header">
                 <img src="visitor/images/whatsapp/profile_01.jpg" alt="profile image" />
                 <h2>John Mark</h2>
                 <p>Tachnical Manager</p>
             </div>
             <div class="cc-body">
                 <p><b>Hey there &#128515;</b></p>
                 <p>Need help ? just give me a call.</p>
             </div>
             <div class="cc-footer">
                 <a href="tel:+919099153528" class="cc-call-button">
                     <span>Call me</span>
                     <svg width="13px" height="10px" viewBox="0 0 13 10">
                         <path d="M1,5 L11,5"></path>
                         <polyline points="8 1 12 5 8 9"></polyline>
                     </svg>
                 </a>
             </div>
         </div>
         <!--/ End Floating Panel Container -->

         <!-- Start Right Floating Button-->
         <div class="cc-button cc-right-bottom">
             <i class="fi-rr-phone-call"></i>
         </div>
         <!--/ End Right Floating Button-->

     </div>
     <!-- Click To Call end -->

     <!-- Newsletter Modal Start -->
     <div id="ec-popnews-bg"></div>
     <div id="ec-popnews-box">
         <div id="ec-popnews-close"><i class="ecicon eci-close"></i></div>
         <div id="ec-popnews-box-content">
             <h1>Subscribe Newsletter</h1>
             <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
             <form id="ec-popnews-form" action="#" method="post">
                 <input type="email" name="newsemail" placeholder="Email Address" required />
                 <button type="button" class="btn btn-secondary" name="subscribe">Subscribe</button>
             </form>
         </div>
     </div>
     <!-- Newsletter Modal end -->

     <!-- Add to Cart successfully toast Start -->
     <div id="addtocart_toast" class="addtocart_toast">
         <div class="desc">You Have Add To Cart Successfully</div>
     </div>
     <div id="wishlist_toast" class="wishlist_toast">
         <div class="desc">You Have Add To Wishlist Successfully</div>
     </div>
     <!-- Add to Cart successfully toast end -->

     @include('visitor.include.script')

 </body>

 </html>
