<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="html template">
    <meta name="keyword" content="your keyword goes to here">
    <meta name="author" content="themexriver">
    <title>Victoria Luxury Resort &amp; Spa HTML template</title>
    <link href="images/favicon.png" rel="icon">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Raleway:800,700,600,300' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Playfair+Display:400italic' rel='stylesheet' type='text/css'>
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="flat-icon/flaticon.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/css3-animation.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body id="shop">
    <div class="pre-loder">
        <div class="loding"> </div>
    </div> <!-- end of pre-loder -->

    @include('visitor/header')

    <section class="shop-title">
        <div class="container">
            <div class="row section-title">
                <span class="playfair">Spa &amp; Wellness</span>
                <h2>Victoria Shop</h2>
            </div>
        </div>
    </section> <!-- end of shop-title -->

    <div class="page-breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col col-xs-3 col-sm-3 col-md-2">
                    <p>Victoria shop</p>
                </div>
                <div class="col col-xs-6 col-sm-6 col-md-8">
                    <ol class="playfair breadcrumb">
                        <li><a href="#">Home</a></li>
                        <li class="active">Shop</li>
                    </ol>
                </div>
                <div class="filt-btn col col-xs-3 col-sm-3 col-md-2">
                    <button class="active btn btn-default">
                        <i class="flaticon-app"></i>
                    </button>
                    <button class="btn btn-default">
                        <i class="flaticon-squares-1"></i>
                    </button>
                    <button class="btn btn-default">
                        <i class="flaticon-three-1"></i>
                    </button>
                </div>
            </div>
        </div> <!-- end of container -->
    </div> <!-- end of page-breadcrumb -->


    <section class="shop">
        <h2 class="hidden">Shop</h2>
        <div class="container">
            <div class="content row">
                <div class="col col-xs-6 col-sm-4">
                    <div class="thumbnail">
                        <div>
                            <div class="title">
                                <h3>Facial day cream</h3>
                                <span class="playfair">Cream for all gender</span>
                            </div>
                            <img src="images/shop/main-shop/img-1.png" alt class="img img-responsive">
                            <span class="price">$25</span>
                        </div>
                    </div>
                </div>
                <div class="col col-xs-6 col-sm-4">
                    <div class="thumbnail">
                        <div>
                            <div class="title">
                                <h3>Facial day cream</h3>
                                <span class="playfair">Cream for all gender</span>
                            </div>
                            <img src="images/shop/main-shop/img-2.png" alt class="img img-responsive">
                            <span class="price">$25</span>
                        </div>
                    </div>
                </div>
                <div class="col col-xs-6 col-sm-4">
                    <div class="thumbnail">
                        <div>
                            <div class="title">
                                <h3>Facial day cream</h3>
                                <span class="playfair">Cream for all gender</span>
                            </div>
                            <img src="images/shop/main-shop/img-3.png" alt class="img img-responsive">
                            <span class="price">$25</span>
                        </div>
                    </div>
                </div>
                <div class="col col-xs-6 col-sm-4">
                    <div class="thumbnail">
                        <div>
                            <div class="title">
                                <h3>Facial day cream</h3>
                                <span class="playfair">Cream for all gender</span>
                            </div>
                            <img src="images/shop/main-shop/img-2.png" alt class="img img-responsive">
                            <span class="price">$25</span>
                        </div>
                    </div>
                </div>
                <div class="col col-xs-6 col-sm-4">
                    <div class="thumbnail">
                        <div>
                            <div class="title">
                                <h3>Facial day cream</h3>
                                <span class="playfair">Cream for all gender</span>
                            </div>
                            <img src="images/shop/main-shop/img-1.png" alt class="img img-responsive">
                            <span class="price">$25</span>
                        </div>
                    </div>
                </div>
                <div class="col col-xs-6 col-sm-4">
                    <div class="thumbnail">
                        <div>
                            <div class="title">
                                <h3>Facial day cream</h3>
                                <span class="playfair">Cream for all gender</span>
                            </div>
                            <img src="images/shop/main-shop/img-2.png" alt class="img img-responsive">
                            <span class="price">$25</span>
                        </div>
                    </div>
                </div>
                <a href="#" class="btn btn-default view-all">Load more</a>
            </div>
        </div> <!-- end of container -->
    </section> <!-- end of shop -->
    @include('visitor/footer')
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/common-script.js"></script>
</body>
</html>
