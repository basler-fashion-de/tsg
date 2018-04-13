function hideErrorPanel () {
  $('#tsg .alerts .ui-state-error').hide()
  $('#tsg .alerts .ui-state-error .content').text('')
}

function hideInfoPanel () {
  $('#tsg .alerts .ui-state-highlight').hide()
  $('#tsg .alerts .ui-state-highlight .content').text('')
}

function showErrorPanel (text) {
  if (text !== '') {
    $('#tsg .alerts .ui-state-error .content').text(text)
    $('#tsg .alerts .ui-state-error').show()

    setTimeout(function () {
      hideErrorPanel();
    }, 5000)
  }
}

function showInfoPanel (text) {
  if (text !== '') {
    $('#tsg .alerts .ui-state-highlight .content').text(text)
    $('#tsg .alerts .ui-state-highlight').show()

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

function openModal(selector, buttons){
  $(selector).dialog({
    resizable: false,
    height: 'auto',
    width: '50%',
    modal: true,
    buttons: buttons
  })
  $('button').blur()
}