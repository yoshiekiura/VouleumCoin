

$(document).ready(function ($)
{
  $(window).scroll(function () {
    if ($(this).scrollTop() > 5) {
      $('.logo-dark').removeClass('hide');
      $('.logo-dark').addClass('show');
      $('.logo-light').addClass('hide');
      $('.logo_name').css('color','#a78555');
      $('.logo_name a span').css('color','rgb(227, 207, 153)');
      
    } 
    else 
    {
      $('.logo-light').removeClass('hide');
     $('.llogo-light').addClass('show');
     $('.logo-dark').removeClass('show'); 
     $('.logo_name').css('color','#fff'); 
     $('.logo_name a span').css('color','#fff');
    }
  });

   $('.backtop-icon').fadeOut();

    $(window).scroll(function () {
    if ($(this).scrollTop() > 200) {
      $('.backtop-icon').fadeIn();
    } else {
      $('.backtop-icon').fadeOut();
    }
  });
  $('.backtop-icon').click(function () {
    $('html, body').animate({
      scrollTop: 0
    }, 1500, 'easeInOutExpo');
    return false;
  });
 new WOW().init();
});