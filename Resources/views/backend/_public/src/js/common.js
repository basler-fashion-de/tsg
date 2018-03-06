//{literal}


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
  }
}

function showInfoPanel (text) {
  if (text !== '') {
    $('#one-click-system .alerts .ui-state-highlight .content').text(text)
    $('#one-click-system .alerts .ui-state-highlight').show()
  }
}

function openNewIframe (title, controller, action, params) {
  var values = {
    width: 1600,
    height: 800,
    component: 'customSubWindow',
    url: controller + '/' + action + '?' + jQuery.param(params),
    title: title
  }
  postMessageApi.createSubWindow(values)
}

//{/literal}