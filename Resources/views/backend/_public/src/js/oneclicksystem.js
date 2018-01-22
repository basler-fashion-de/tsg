//{literal}

$(function () {
  activateAccordion()
  $('button').button();

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
  registerShowButton()
  registerCreateButton()
  registerDeleteButton()
  startUpdateInterval()
}

function registerShowButton(){
  $('#show-options-button').on('click', function () {
    $('#options').show()
    $('#show-options-button').hide()
  })
}

function registerCreateButton () {
  $('#create-button').on('click', function () {

    //Kurzer Timeout für den create Button um doppel Klicks besser abzufangen.
    $( "#create-button" ).button( "option", "disabled", true );
    setTimeout(function () {
      $( "#create-button" ).button( "option", "disabled", false );
    }, 2000);

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
          showInfoPanel(response.message);
        } else {
          showErrorPanel(response.error);
        }
      }
    });

    loadSystemList();
  })
}

function registerDeleteButton () {
  $('.delete-button').on('click', function () {
    $(this).button( "option", "disabled", true );

    hideErrorPanel();
    hideInfoPanel();

    var url = $('#one-click-system').data('deletesystemurl');
    var params = {'id': $(this).data('id')};

    $.ajax({
      type: 'post',
      url: url,
      data: params,
      success: function (response) {
        if (response.success) {
          showInfoPanel(response.message);
        } else {
          showErrorPanel(response.error);
        }
      }
    });

    loadSystemList();
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
        registerDeleteButton();
        $('button').button();
      }

      if (callback) {
        callback()
      }
    }
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
}

//{/literal}