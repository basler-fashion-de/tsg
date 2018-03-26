$(function () {
  registerEvents()
})

function registerEvents () {
  registerCreateButton()
  registerShowButton()
  registerBackButton()
  registerNextButton()

  registerDeleteButton()
  registerMediaButton()
  registerCompareButton()
  registerCommitButton()
  registerRemoteDbCheckbox()
  startUpdateInterval()
  registerMailButton()
}

function registerCreateButton (selector) {
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

function registerShowButton () {
  $('#show-options-button').on('click', function () {
    $('#options').show()
    $('#create-button').button('option', 'disabled', true)
    $('#show-options-button').button('option', 'disabled', true)
    $('#show-options-button').addClass('active')
    $('#action-field').show();
  })
}

function registerBackButton () {
  $('#back-button').on('click', function () {
    location.reload()
  })
}

function registerNextButton(){

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
  $('.dblocal').toggle($('#dbRemote').val())

  $('#dbRemote').on('change', function () {
    $('.dblocal').toggle($(this).val())
  })
}

function registerMailButton () {
  $('[name="radio-mail"]').on('change', function () {
    var me = this
    var url = $('#one-click-system').data('mailurl')
    var params = {'allow': $(me).attr('id')}

    $.ajax({
      type: 'post',
      url: url,
      data: params,
      success: function (response) {
        if (!response.success) {
          showErrorPanel(response.error)
          $('#radio-yes').attr('checked', response.allow)
          $('#radio-no').attr('checked', !response.allow)
        }
      }
    })
  })
}