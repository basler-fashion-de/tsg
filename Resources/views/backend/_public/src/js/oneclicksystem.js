$(function () {
  activateAccordion()
  $('button').button()
  $(document).tooltip()

  $('#options input').controlgroup()

  $('#name').autocomplete({
    source: autocompleteNames
  })

  $('#name').val(autocompleteNames[0])
  $('#dbName').val(autocompleteNames[0].toLowerCase().replace(' ', '-'))

  $('#type').selectmenu()
})

function startUpdateInterval () {
  setInterval(function () {
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
        handleSystemLoadListResponse(response)
      }

      if (callback) {
        callback()
      }
    }
  })
}

function handleSystemLoadListResponse (response) {
  var activeId = $('#systems').accordion('option', 'active')

  var deleteDisabledStates = []
  $('button.delete-button').each(
    function () {
      deleteDisabledStates.push({id: $(this).data('id'), state: $(this).button('option', 'disabled')})
    }
  )

  var mediaDisabledStates = []
  $('button.media-button').each(
    function () {
      mediaDisabledStates.push({id: $(this).data('id'), state: $(this).button('option', 'disabled')})
    }
  )

  $('#system-list').html(response.html)

  activateAccordion()
  $('#systems').accordion('option', 'active', activeId)

  registerDeleteButton()
  registerCompareButton()
  registerMediaButton()

  $('button').button()
  $.each(deleteDisabledStates, function (key, value) {
    $($('.delete-button[data-id=' + value.id + ']')).button('option', 'disabled', value.state)
  })

  $.each(mediaDisabledStates, function (key, value) {
    $($('.media-button[data-id=' + value.id + ']')).button('option', 'disabled', value.state)
  })
}

function activateAccordion () {
  $('#systems').accordion({
    header: '.system-header',
    collapsible: true,
    active: false,
    animate: false
  })

  $('.file_compare, .table_compare').accordion({
    header: '.title',
    collapsible: true,
    active: false,
    animate: false
  })
}