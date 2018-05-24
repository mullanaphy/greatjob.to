(function($) {
  // Generate a url.
  $(function() {
    var alertElement = $('#generate-input');
    if (!alertElement) {
      return;
    }
    var element = document.getElementById('generate-input');
    var form = document.getElementById('generate');
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
    var alertElement = $('#url');
    if (!alertElement) {
      return;
    }
    var element = document.getElementById('url');
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
  if (!alertElement) {
    return;
  }
  var modalElement = $('#modal-report');
  var formElement = $('#model-report-form');
  var idElement = $('#modal-report-id');
  var emailElement = $('#modal-report-email');
  var commentElement = $('#modal-report-comment');
  formElement.submit(function(event) {
    event.preventDefault && event.preventDefault();
    event.stopPropagation && event.stopPropagation();

    $.post({
      url: '/reserved/report?xsrfId=' + xsrfId,
      data: {
        report: {
          id: idElement.val(),
          email: emailElement.val(),
          comment: commentElement.val()
        }
      },
      complete: function() {
        modalElement.modal('hide');
        alertElement.modal('show');
      },
      dataType: 'json'
    });

    return false;
  });
})(jQuery);
