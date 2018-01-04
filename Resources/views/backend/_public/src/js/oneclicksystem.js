//{literal}

$(function () {
  $('#systems').accordion({
    header: '.system-header',
    collapsible: true,
    active: false
  });

  $( "#options input" ).controlgroup();

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

  $('#create-button').on('click', function () {

    $('#one-click-system .alerts .ui-state-error').hide();
    $('#one-click-system .alerts .ui-state-highlight').hide();

    var url = $('#one-click-system').data('createsystemurl')
    var params = $('form#options-form').serialize()

    $.ajax({
      type: 'post',
      url: url,
      data: params,
      success: function (response) {
        if(response.success){
          $('#one-click-system .alerts .ui-state-highlight .content').text(response.message);
          $('#one-click-system .alerts .ui-state-highlight').show();
        }else{
          $('#one-click-system .alerts .ui-state-error .content').text(response.error);
          $('#one-click-system .alerts .ui-state-error').show();
        }
      }
    })
  })
}

//{/literal}