



<!DOCTYPE html>

<html lang="zxx">



<head>

    <title>INFIDIA</title>

    <!-- Meta tag Keywords -->

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta charset="UTF-8" />

    <meta name="keywords" content="" />

    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 

    <script>

        addEventListener("load", function() {

            setTimeout(hideURLbar, 0);

        }, false);



        function hideURLbar() {

            window.scrollTo(0, 1);

        }



    </script>

    <!-- //Meta tag Keywords -->

    <!-- Custom-Files -->

    <link rel="stylesheet" href="website/css/bootstrap.css">

    <!-- Bootstrap-Core-CSS -->

    <link rel="stylesheet" href="website/css/style.css" type="text/css" media="all" />

    <link rel="stylesheet" href="website/css/slider.css" type="text/css" media="all" />

    <!-- Style-CSS -->

    <!-- font-awesome-icons -->

    <link href="website/css/font-awesome.css" rel="stylesheet">

    <!-- //font-awesome-icons -->

    <!-- /Fonts -->

    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900" rel="stylesheet">

    <!-- //Fonts -->

</head>



<body>

<!-- mian-content -->

<div class="main-w3layouts-header-sec" id="home">

    <!-- header -->

    <header>

        <div class="container">

            <div class="header d-lg-flex justify-content-between align-items-center">

                <div class="header-section">

                    <h1>

                        <a class="navbar-brand logo editContent" href="{{ route('website-home') }}">

                            <img src="website/images/infidia-logo.png" />

                        </a>

                    </h1>

                </div>

                <div class="nav_section">

                    <nav>

                        <ul class="menu">

                            <li><a href="{{ route('business') }}">INFIDIA for Business</a></li>



                        </ul>

                    </nav>

                </div>



            </div>

        </div>

    </header>

    <!-- //header -->

    <!-- banner -->

    <section class="banner-inner">



    </section>

    <!-- //slider -->

</div>



    <!-- //banner -->

    <!-- //banner-botttom -->

    <section class="content-info py-5">

        <div class="container py-md-5">

            <div class="text-center px-lg-5">

                <h3 class="title-w3pvt mb-5">Contact Us</h3>

                <div class="title-desc text-center px-lg-5">

                    <p class="px-lg-5 sub-wthree">B4/4 Sadhguru  Manson near chitrakoot stadium Vaishali nagar, Jaipur, Rajasthan 302021</p>

                </div>

            </div>

            <div class="contact-w3pvt-form mt-5">

                <form action="contact_email.php" class="w3layouts-contact-fm" id="contactForm" method="post">
                <!-- <form action="http://infidia.tk/api/contact-us" class="w3layouts-contact-fm" id="contactForm" method="post"> -->

                    <div class="row">

                        <div class="col-lg-6">

                            <div class="form-group">

                                <label>First Name*</label>

                                <input class="form-control" type="text" name="first_name" placeholder="" required="">

                            </div>

                            <div class="form-group">

                                <label>Last Name*</label>

                                <input class="form-control" type="text" name="last_name" placeholder="" required="">

                            </div>

                            <div class="form-group">

                                <label>Email*</label>

                                <input class="form-control" type="email" name="email" placeholder="" required="">

                            </div>

                        </div>

                        <div class="col-lg-6">

                            <div class="form-group">

                                <label>Write Message*</label>

                                <textarea class="form-control" name="message" placeholder="" required=""></textarea>

                            </div>

                        </div>

                        <div class="form-group mx-auto mt-3">

                            <button type="button" id="submit-btn" class="btn submit">Submit</button>

                        </div>

                    </div>



                </form>

            </div>

        </div>

    </section>

    <!-- //banner-botttom -->



    <div class="map-w3layouts">

        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d227748.99973488602!2d75.65047209086042!3d26.885141679373124!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x396c4adf4c57e281%3A0xce1c63a0cf22e09!2sJaipur%2C%20Rajasthan!5e0!3m2!1sen!2sin!4v1567616691715!5m2!1sen!2sin" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
    
    </div>





    <div class="copy-right">

        <div class="container">

            <div class="row">

                <div class="copy-right-grids w3layouts-footer text-md-left text-center my-sm-4 my-4 col-md-8">

                <ul class="list-unstyled w3layouts-icons">
                    <li>
                        <a href="{{ route('website-home') }}">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business') }}">
                            For Business
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy') }}">
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms') }}">
                            Terms & Conditions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}">
                            Contact
                        </a>
                    </li>
                </ul>

                </div>

                <div class="w3layouts-footer text-md-right text-center mt-4 col-md-3">

                    <ul class="list-unstyled w3layouts-icons">

                        <li>

                            <a href="https://www.facebook.com/www.infidia.in/">

                                <span class="fa fa-facebook-f"></span>

                            </a>

                        </li>

                        <li>

                            <a href="https://twitter.com/infidia1">

                                <span class="fa fa-twitter"></span>

                            </a>

                        </li>

                    </ul>

                </div>

                <div class="move-top text-right col-md-1"><a href="#home" class="move-top"> <span class="fa fa-angle-up  mb-3" aria-hidden="true"></span></a></div>



            </div>

        </div>

    </div>

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.2/jquery.js"></script>
<script>
// this is the id of the form
$("#contactForm").submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');

    $('#submit-btn').attr('disabled', true)

    $.ajax({
        type: "GET",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {

            $('#submit-btn').attr('disabled', false);

            //form.reset();
            form.trigger("reset");

            alert('Thanks, We will contact you soon.'); // show response from the php script.
        },
        error: function(data)
        {
            $('#submit-btn').attr('disabled', false);
        },
    });
});
</script>

</html>

