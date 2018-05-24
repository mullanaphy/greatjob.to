(function($) {
  // Generate a url.
  $(function() {
    var form = document.getElementById('generate');
    var element = document.getElementById('generate-input');
    var alertElement = $('#generate-input');
    form.addEventListener('submit', function(event) {
      event.preventDefault && event.preventDefault();
      event.stopPropagation && event.stopPropagation();
      console.log(element.value);
      if (element.value) {
        window.location = 'https://\ud83d\udc4b\ud83d\udc4b.to/' + encodeURIComponent(element.value);
      } else {
        alertElement.popover('show');
        setTimeout(function() {
          alertElement.popover('hide');
        }, 3000);
      }
      return false;
    });
  });
})(jQuery);
(function($) {
  // Copy a url.
  $(function() {
    var element = document.getElementById('url');
    var alertElement = $('#url');
    element.addEventListener('click', function() {
      element.select();
      var success = document.execCommand('copy');
      if (success) {
        alertElement.popover('show');
        setTimeout(function() {
          alertElement.popover('hide');
        }, 3000);
      }
    });
  });
})(jQuery);
(function($) {
  // Report a url.
  var alertElement = $('#modal-report-thanks');
  var formElement = $('#model-report-form');
  var slugElement = $('#modal-report-slug');
  var emailElement = $('#modal-report-email');
  var commentElement = $('#modal-report-comment');
  formElement.submit(function(event) {
    event.preventDefault && event.preventDefault();
    event.stopPropagation && event.stopPropagation();

    $.ajax({
      type: 'POST',
      url: '/reserved/report',
      data: {
        report: {
          slug: slugElement.val(),
          email: emailElement.val(),
          comment: commentElement.val()
        }
      },
      complete: function() {
        alertElement.modal('show');
      },
      dataType: 'json'
    });

    return false;
  });
})(jQuery);