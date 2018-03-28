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

  //$('#type').selectmenu()
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

var newHtmlHash = null

function handleSystemLoadListResponse (response) {
  //Hash generieren um einen Abgleich mit dem letzten Response zu haben
  var newHtml = response.html.trim()
  var hash = 0, i, chr, len
  if (newHtml.length === 0) return hash
  for (i = 0, len = newHtml.length; i < len; i++) {
    chr = newHtml.charCodeAt(i)
    hash = ((hash << 5) - hash) + chr
    hash |= 0 // Convert to 32bit integer
  }

  //Keine Änderung
  if (newHtmlHash === hash) {
    return
  } else {
    newHtmlHash = hash
  }

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

  $('#system-list').html(newHtml)

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

function openConfirmModal () {
  var id = 'modal-confirm'
  var selector = '#' + id
  var values = $('#options-form').clone()
  values.find('input').attr('disabled', 'disabled')
  values.find(':hidden').show()
  values.find('label[for=type]').hide()
  values.find('select#type').hide()

  $(selector).remove()
  $('body').append('<div id="' + id + '"></div>')
  $(selector).html(values)

  $(selector).dialog({
    resizable: false,
    height: 'auto',
    width: '50%',
    modal: true,
    buttons: [
      {
        text: 'Abbrechen',
        click: function () {
          $(this).dialog('close')
        }
      },
      {
        text: 'Fortfahren',
        'class': 'blau',
        click: function () {
          $(this).dialog('close')
          $('#create-button').click()
          $('#back-button').click()
        }
      }
    ]
  })
}