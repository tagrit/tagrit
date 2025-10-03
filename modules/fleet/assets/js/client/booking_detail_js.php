<script>
var SetRatingStar = function() {
"use strict";
  var $star_rating = $('.star-rating .fa-star');
  return $star_rating.each(function() {
    if (parseInt($star_rating.siblings('input[name="rating"]').val()) >= parseInt($(this).data('rating'))) {
      return $(this).removeClass('fa-regular').addClass('fa');
    } else {
      return $(this).removeClass('fa').addClass('fa-regular');
    }
  });
};
var SetRatingViewStar = function() {
"use strict";
  var $star_rating_view = $('.star-rating-view .fa-star');
  return $star_rating_view.each(function() {
    if (parseInt($star_rating_view.siblings('input.rating-value').val()) >= parseInt($(this).data('rating'))) {
      return $(this).removeClass('fa-regular').addClass('fa');
    } else {
      return $(this).removeClass('fa').addClass('fa-regular');
    }
  });
};

(function($) {
"use strict";
$(document).ready(function () {
  $('.star-rating .fa-star').on('click', function() {
     $('.star-rating .fa-star').siblings('input[name="rating"]').val($(this).data('rating'));
    return SetRatingStar();
  });
  SetRatingViewStar();
  SetRatingStar();
  });
})(jQuery);

function rating() {
  "use strict";
  $('#rating-modal').modal('show');
}

</script>


