/*
   
   Table Of Content
   
   1. Preloader
   2. Smooth Scroll
   3. Scroll Naviagation Background Change with Sticky Navigation
   4. Mobile Navigation Hide or Collapse on Click
   5. Scroll To Top
   6. Tooltip
   7. Ajaxchimp for Subscribe Form
   8. Portfolio Filtering
   9. Magnific Popup
  10. Testimonial Carousel/Slider
  11. Statistics Counter
 

*/


(function ($) {
    'use strict';

    jQuery(document).ready(function () {


        /* Preloader */

        $(window).load(function () {
            $('.preloader').delay(800).fadeOut('slow');
        });



        /* Smooth Scroll */

        $('a.smoth-scroll').on("click", function (e) {
            var anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: $(anchor.attr('href')).offset().top - 50
            }, 1000);
            e.preventDefault();
        });




        /* Scroll Naviagation Background Change with Sticky Navigation */

        $(window).on('scroll', function () {
            if ($(window).scrollTop() > 100) {
                $('.header-top-area').addClass('navigation-background');
            } else {
                $('.header-top-area').removeClass('navigation-background');
            }
        });




        /* Mobile Navigation Hide or Collapse on Click */

        $(document).on('click', '.navbar-collapse.in', function (e) {
            if ($(e.target).is('a') && $(e.target).attr('class') != 'dropdown-toggle') {
                $(this).collapse('hide');
            }
        });
        $('body').scrollspy({
            target: '.navbar-collapse',
            offset: 195

        });




        /* Scroll To Top */

        $(window).scroll(function () {
            if ($(this).scrollTop() >= 500) {
                $('.scroll-to-top').fadeIn();
            } else {
                $('.scroll-to-top').fadeOut();
            }
        });


        $('.scroll-to-top').click(function () {
            $('html, body').animate({
                scrollTop: 0
            }, 800);
            return false;
        });



        /* Tooltip */

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })



        /* Ajaxchimp for Subscribe Form */

        $('#mc-form').ajaxChimp();




        /* Portfolio Filtering */

        $('.portfolio-inner').mixItUp();



        /* Magnific Popup */

        $('.portfolio-popup').magnificPopup({
            type: 'image',

            gallery: {
                enabled: true
            },
            zoom: {
                enabled: true,
                duration: 500

            },


            image: {
                markup: '<div class="mfp-figure portfolio-pop-up">' +
                    '<div class="mfp-close"></div>' +
                    '<div class="mfp-img"></div>' +
                    '<div class="mfp-bottom-bar portfolio_title">' +
                    '<div class="mfp-title"></div>' +
                    '<div class="mfp-counter"></div>' +
                    '</div>' +
                    '</div>',

                titleSrc: function (item) {
                    return item.el.attr('title');
                }
            }


        });


        /* Testimonial Carousel/Slider */

        $(".testimonial-carousel-list").owlCarousel({
            items: 1,
            autoPlay: true,
            stopOnHover: false,
            navigation: true,
            navigationText: ["<i class='fa fa-long-arrow-left fa-2x owl-navi'></i>", "<i class='fa fa-long-arrow-right fa-2x owl-navi'></i>"],
            itemsDesktop: [1199, 1],
            itemsDesktopSmall: [980, 1],
            itemsTablet: [768, 1],
            itemsTabletSmall: false,
            itemsMobile: [479, 1],
            autoHeight: true,
            pagination: false,
            transitionStyle: "fadeUp"
        });




        /* Statistics Counter */

        $('.statistics').appear(function () {
            var counter = $(this).find('.statistics-count');
            var toCount = counter.data('count');

            $(counter).countTo({
                from: 0,
                to: toCount,
                speed: 5000,
                refreshInterval: 50
            })
        });


    });

})(jQuery);


$('#applybtn').click(function (e) {
    e.preventDefault()
    $('#applydiv').block({
        message: '<h4>Just a moment...</h4>',
        css: {
            border: '1px solid #fff'
        }
    })
    $.ajax({
        type: 'POST',
        url: '/apply/candidate',
        data: $('#apply').serialize(),
        dataType: 'json',
        beforeSend: function () {
            $('#applydiv').block({
                message: '<h4>Please wait...</h4>',
                css: {
                    border: '1px solid #fff'
                }
            });
        },
        success: function (response) {
            $('#applydiv').unblock();
            swal({
                title: response.title,
                text: response.message,
                type: response.error ? 'error' : 'success',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Close'
            })
        },
        error: function (response) {
            console.log(response)
            $('#applydiv').unblock();
        }
    });
})

function vote(candidateId)
{
    console.log(candidateId)
    swal({   
        title: "Are you sure?",   
        text: "You will not be able to revert this operation",   
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#DD6B55",   
        confirmButtonText: "Yes, vote",   
        cancelButtonText: "No, cancel!",   
        closeOnConfirm: true,   
        closeOnCancel: false 
    }, function(isConfirm){   
        if (isConfirm) {     
            var data = $('#candidate'+candidateId+'').serialize();
            $.ajax({
            type : 'POST',
            url  : '/candidate',
            data : data,
            dataType: 'json',
            beforeSend: function()
            {
                $('#votepanel').block({
                    message: '<h4>Just a moment...</h4>', 
                    css: {
                        border: '1px solid #fff'
                    }
                });
            },
            success :  function(response)
            {
                //var response = JSON.parse(JSON.stringify(response))

                $('#votepanel').unblock();

                swal({
                    title: response.title,
                    text: response.message,
                    type: response.error ? 'error':'success',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Close'
                 })

                 

            },
            error: function(response)
            {
                console.log(response)
                $('#votepanel').unblock();
                swal({
                    title: response.statusText,
                    text: response.responseText+' Please Refresh these page and try again',
                    type: 'error',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Close'
                 })
            }
        });  
        } else {     
            swal("Cancelled", "operation terminated", "error");   
        } 
    });
}