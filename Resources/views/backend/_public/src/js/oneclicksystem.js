//{literal}

$(function () {
  activateAccordion()
  $('button').button()
  $(document).tooltip()

  $('#options input').controlgroup()

  registerEvents()

  $('#name').autocomplete({
    source: autocompleteNames
  })

  $('#name').val(autocompleteNames[0])
  $('#dbName').val(autocompleteNames[0].toLowerCase().replace(' ', '-'))

  $('#type').selectmenu()
})

function registerEvents () {
  registerShowButton()
  registerCreateButton()
  registerDeleteButton()
  registerMediaButton()
  registerCompareButton()
  registerCommitButton()
  registerRemoteDbCheckbox()
  startUpdateInterval()
}

function registerShowButton () {
  $('#show-options-button').on('click', function () {
    $('#options').show()
    $('#show-options-button').hide()
  })
}

function registerCreateButton () {
  $('#create-button').on('click', function () {

    //Kurzer Timeout für den create Button um doppel Klicks besser abzufangen.
    var btn = $(this)
    btn.button('option', 'disabled', true)
    btn.html(btn.data('disabledtext'))
    setTimeout(function () {
      btn.button('option', 'disabled', false)
      btn.html(btn.data('activetext'))
    }, 5000)

    hideErrorPanel()
    hideInfoPanel()

    var url = $('#one-click-system').data('createsystemurl')
    var params = $('form#options-form').serialize()

    $.ajax({
      type: 'post',
      url: url,
      data: params,
      success: function (response) {
        if (response.success) {
          showInfoPanel(response.message)
          $('#name').val(response.shopTitle)
        } else {
          showErrorPanel(response.error)
        }
      }
    })

    setTimeout(function () {
      loadSystemList()
    }, 1000)
  })
}

function registerDeleteButton () {
  $('.delete-button').on('click', function () {
    $(this).button('option', 'disabled', true)

    hideErrorPanel()
    hideInfoPanel()

    var url = $('#one-click-system').data('deletesystemurl')
    var params = {'id': $(this).data('id')}

    $.ajax({
      type: 'post',
      url: url,
      data: params,
      success: function (response) {
        if (response.success) {
          showInfoPanel(response.message)
        } else {
          showErrorPanel(response.error)
        }
      }
    })

    loadSystemList()
  })
}

function registerMediaButton () {
  $('.media-button').on('click', function () {
    $(this).button('option', 'disabled', true)

    hideErrorPanel()
    hideInfoPanel()

    var url = $('#one-click-system').data('duplicatemediafolderurl')
    var params = {'id': $(this).data('id')}

    $.ajax({
      type: 'post',
      url: url,
      data: params,
      success: function (response) {
        if (!response.success) {
          showErrorPanel(response.error)
        }
      }
    })

    loadSystemList()
  })
}

function registerCompareButton () {
  $('.compare-button').on('click', function () {
    openNewIframe(
      $(this).data('title'),
      'BlaubandCompare',
      'index',
      {'id': $(this).data('id'), 'group': $(this).data('group')}
    )
  })
}

function registerCommitButton () {
  $('.commit-button').on('click', function () {
    $(this).button('option', 'disabled', true)

    var url = $(this).data('url')
    window.location.replace(url)
  })
}

function registerRemoteDbCheckbox () {
  $('.dblocal').toggle($('#dbremote').val())

  $('#dbremote').on('change', function () {
    $('.dblocal').toggle($(this).val())
  })
}

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