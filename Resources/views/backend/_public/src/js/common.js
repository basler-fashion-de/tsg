function hideErrorPanel () {
  $('#one-click-system .alerts .ui-state-error').hide()
  $('#one-click-system .alerts .ui-state-error .content').text('')
}

function hideInfoPanel () {
  $('#one-click-system .alerts .ui-state-highlight').hide()
  $('#one-click-system .alerts .ui-state-highlight .content').text('')
}

function showErrorPanel (text) {
  if (text !== '') {
    $('#one-click-system .alerts .ui-state-error .content').text(text)
    $('#one-click-system .alerts .ui-state-error').show()

    setTimeout(function () {
      hideErrorPanel();
    }, 5000)
  }
}

function showInfoPanel (text) {
  if (text !== '') {
    $('#one-click-system .alerts .ui-state-highlight .content').text(text)
    $('#one-click-system .alerts .ui-state-highlight').show()

    setTimeout(function () {
      hideInfoPanel();
    }, 5000)
  }
}

function openNewIframe (title, controller, action, params) {
  var values = {
    component: 'customSubWindow',
    url: controller + '/' + action + '?' + jQuery.param(params),
    title: title
  }
  postMessageApi.createSubWindow(values)
}