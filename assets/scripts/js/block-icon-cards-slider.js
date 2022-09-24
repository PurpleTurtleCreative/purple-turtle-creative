/**
 * PTC Icon Cards Slider block frontend functionality.
 *
 * Ensure this script is loaded [defer] to avoid DOMContentLoaded listener.
 */

if (
  'jQuery' in window
  && 'function' === typeof window.jQuery
  && 'slick' in window.jQuery.fn
  && 'function' === typeof window.jQuery.fn.slick
) {
  window.jQuery('.ptc-block-icon-cards-slider').slick({
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
}
