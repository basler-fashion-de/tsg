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

function registerCreateButton () {
  $('#create-button').on('click', function () {

    //Kurzer Timeout für den create Button um doppel Klicks besser abzufangen.
    var btn = $(this)
    btn.button('option', 'disabled', true)

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
          if(response.dbOverwrite){
            openModal('.db-overwrite-error', [
              {
                text: 'Abbrechen',
                click: function () {
                  $(this).dialog('close')
                }
              },
              {
                text: 'Ok',
                'class': 'blau',
                click: function () {
                  $(this).dialog('close')
                  $('#dbOverwrite').prop('checked', true);
                  $('#create-button').click()
                  $('#back-button').click()
                }
              }
            ]);
          }else{
            showErrorPanel(response.error)
          }
        }

        loadSystemList()
        setTimeout(function () {
          btn.button('option', 'disabled', false)
        }, 1000)
      }
    })
  })
}

function registerShowButton () {
  $('#show-options-button').on('click', function () {
    $('#create-button').button('option', 'disabled', true)
    $('#show-options-button').button('option', 'disabled', true)
    $('#show-options-button').addClass('active')
    $('#action-field').show()
  })
}

function registerBackButton () {
  $('#back-button').on('click', function () {
    $('#create-button').button('option', 'disabled', false)
    $('#show-options-button').button('option', 'disabled', false)
    $('#show-options-button').removeClass('active')
    $('#action-field').hide()
  })
}

function registerNextButton () {
  $('#next-button').on('click', function () {
    openConfirmModal()
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