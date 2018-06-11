$(function () {
  var max = $('#tsg').data('maxcount')
  var url = $('#tsg').data('loadurl')
  var current = 1

  loadCount(url, current, max)
})


function loadCount (url, count, max) {
  $('.current-counter').html(count)

  $.ajax({
    type: 'post',
    url: url,
    data: {'count': count},
    success: function (response) {
      if (response.success) {
        if(response.type == 'table' && response.html != ''){
          $('.db-results').append(response.html);
          $('.db-title').show();
          $('.identical-title').hide();
          $('.commit-button-wrapper').show();
        }

        if(response.type == 'folder' && response.html != ''){
          $('.folder-results').append(response.html);
          $('.folder-title').show();
          $('.identical-title').hide();
          $('.commit-button-wrapper').show();
        }

        if (count < max) {
          loadCount(url, count + 1, max)
        }else{
          $('.loading-info-box').remove();
          $('.commit-button').button({disabled: false});
        }
      } else {
        showErrorPanel(response.error)
        $('.loading-info-box').hide();
      }
    }
  })
}