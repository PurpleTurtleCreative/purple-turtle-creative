/**
 * PTC Icon Cards Slider block frontend functionality.
 */

document.addEventListener('DOMContentLoaded', function() {
  jQuery('.ptc-block-icon-cards-slider').slick({
  	slide: '.icon-card',
  	dots: true,
  	prevArrow: 'button.slick-prev',
  	nextArrow: 'button.slick-next',
  	infinite: true,
    centerMode: true,
    variableWidth: true,
    slidesToShow: 1,
    autoplay: true,
    autoplaySpeed: 5000
  });
});
