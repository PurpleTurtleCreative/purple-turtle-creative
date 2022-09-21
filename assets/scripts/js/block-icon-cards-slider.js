/**
 * PTC Icon Cards Slider block frontend functionality.
 */

document.addEventListener('DOMContentLoaded', function() {
  jQuery('.ptc-block-icon-cards-slider').slick({
  	infinite: true,
  	dots: true,
  	slide: '.icon-card',
    centerMode: true,
    variableWidth: true,
    slidesToShow: 1,
    autoplay: true,
    autoplaySpeed: 5000
  });
});
