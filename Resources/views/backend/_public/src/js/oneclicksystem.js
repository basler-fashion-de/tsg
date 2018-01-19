//{literal}

$(function () {
  activateAccordion()
  $('button').button()

  $('#options input').controlgroup()

  registerEvents()

  $('#name').autocomplete({
    source: autocompleteNames
  })

  $('#name').val(autocompleteNames[0])
  $('#dbname').val(autocompleteNames[0].toLowerCase())

  $('#type').selectmenu()
})

function registerEvents () {
  //Erweiterte Einstellungen anzeigen
  $('#show-options-button').on('click', function () {
    $('#options').show()
    $('#show-options-button').hide()
  })

  registerCreateButton()

  registerDeleteButton()

  startUpdateInterval()

}

function registerCreateButton () {
  $('#create-button').on('click', function () {
    $('#one-click-system .alerts .ui-state-error').hide()
    $('#one-click-system .alerts .ui-state-highlight').hide()

    var url = $('#one-click-system').data('createsystemurl')
    var params = $('form#options-form').serialize()

    $.ajax({
      type: 'post',
      url: url,
      data: params,
      success: function (response) {
        if (response.success) {
          $('#one-click-system .alerts .ui-state-highlight .content').text(response.message)
          $('#one-click-system .alerts .ui-state-highlight').show()
        } else {
          $('#one-click-system .alerts .ui-state-error .content').text(response.error)
          $('#one-click-system .alerts .ui-state-error').show()
        }
      }
    })
  })
}

function registerDeleteButton () {
  $('.delete-button').on('click', function () {
    loadSystemList()

    $('#one-click-system .alerts .ui-state-error').hide()
    $('#one-click-system .alerts .ui-state-highlight').hide()

    var url = $('#one-click-system').data('deletesystemurl')
    var params = {'id': $(this).data('id')}

    $.ajax({
      type: 'post',
      url: url,
      data: params,
      success: function (response) {
        if (response.success) {
          $('#one-click-system .alerts .ui-state-highlight .content').text(response.message)
          $('#one-click-system .alerts .ui-state-highlight').show()
        } else {
          $('#one-click-system .alerts .ui-state-error .content').text(response.error)
          $('#one-click-system .alerts .ui-state-error').show()
        }
      }
    })

    $('.delete-button').remove()
  })
}

function startUpdateInterval () {
  var interval = setInterval(function () {
    loadSystemList()
  }, 5000)
}

function loadSystemList (callback) {
  var url = $('#one-click-system').data('systemlisturl')

  $.ajax({
    type: 'post',
    url: url,
    success: function (response) {
      if (response.success) {
        var activeId = $('#systems').accordion('option', 'active')
        $('#system-list').html(response.html)
        activateAccordion()
        $('#systems').accordion('option', 'active', activeId)
        registerDeleteButton()
      }

      if(callback){
        callback();
      }
    }
  })
}

function activateAccordion () {
  $('#systems').accordion({
    header: '.system-header',
    collapsible: true,
    active: false,
    animate: false
  })
}

//{/literal}