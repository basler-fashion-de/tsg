//{literal}

$(function () {
  $('#systems').accordion({
      header: '.system-header'
    })

  registerEvents();

  $( "#name" ).autocomplete({
    source: autocompleteNames
  });

  $( "#name" ).val(autocompleteNames[0]);
})

function registerEvents(){
  //Erweiterte Einstellungen anzeigen
  $('#show-options-button').on( "click", function() {
    $( "#options" ).show();
    $('#show-options-button').hide();
  });

  $('#create-button').on( "click", function() {
    $( "#options" ).show();
    $('#show-options-button').hide();
  });
}

//{/literal}